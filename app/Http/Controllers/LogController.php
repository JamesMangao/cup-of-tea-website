<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Get logs directly from Laravel log file (FAST - no database)
     */
    public function index(Request $request)
    {
        $level = $request->get('level', 'all');
        $channel = $request->get('channel', 'all');
        $search = $request->get('search', '');
        $limit = $request->get('limit', 100);

        try {
            // Try to find the log file - handles both single and daily logging
            $logFile = $this->findLogFile();

            if (!$logFile || !file_exists($logFile)) {
                return response()->json([
                    'success' => true,
                    'logs' => [],
                    'stats' => ['total' => 0, 'info' => 0, 'success' => 0, 'warning' => 0, 'error' => 0, 'debug' => 0],
                    'message' => 'No log file found',
                ]);
            }

            // Read file in reverse order (newest first)
            $lines = array_reverse(file($logFile));
            
            $logs = [];
            $stats = ['total' => 0, 'info' => 0, 'success' => 0, 'warning' => 0, 'error' => 0, 'debug' => 0];
            $currentLog = null;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Parse new log entry: [2026-03-17 15:12:07] local.LEVEL: Message
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.+?)(?:\s+(\{.+\}))?$/', $line, $matches)) {
                    
                    // Save previous log if it matches filters
                    if ($currentLog) {
                        if ($this->matchesFilters($currentLog, $level, $channel, $search)) {
                            $logs[] = $currentLog;
                            if (isset($stats[$currentLog['level']])) {
                                $stats[$currentLog['level']]++;
                            }
                            $stats['total']++;
                            
                            if (count($logs) >= $limit) break;
                        }
                    }

                    // Start new log
                    $timestamp = $matches[1];
                    $env = $matches[2];
                    $logLevel = $matches[3];
                    $message = trim($matches[4]);
                    $contextJson = $matches[5] ?? null;

                    $logChannel = $this->extractChannel($message);
                    $context = null;

                    if ($contextJson) {
                        try {
                            $context = json_decode($contextJson, true);
                        } catch (\Exception $e) {
                            // Invalid JSON
                        }
                    }

                    $currentLog = [
                        'id' => md5($timestamp . $message),
                        'level' => $logLevel,
                        'channel' => $logChannel,
                        'message' => $message,
                        'time' => substr($timestamp, 11),
                        'fullTime' => $timestamp,
                        'date' => substr($timestamp, 0, 10),
                        'user' => $context['user_id'] ?? $context['userId'] ?? null,
                        'ip' => $context['ip'] ?? null,
                        'context' => $context,
                    ];
                } else if ($currentLog) {
                    // Continuation of previous log (stack trace, etc.)
                    $currentLog['message'] .= "\n" . $line;
                }
            }

            // Don't forget the last log
            if ($currentLog && $this->matchesFilters($currentLog, $level, $channel, $search)) {
                $logs[] = $currentLog;
                if (isset($stats[$currentLog['level']])) {
                    $stats[$currentLog['level']]++;
                }
                $stats['total']++;
            }

            return response()->json([
                'success' => true,
                'logs' => array_slice($logs, 0, $limit),
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            \Log::error('LogController error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'logs' => [],
                'stats' => ['total' => 0, 'info' => 0, 'success' => 0, 'warning' => 0, 'error' => 0, 'debug' => 0],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Find the most recent log file
     * Handles both single file and daily logging
     */
    private function findLogFile()
    {
        $logsDir = storage_path('logs');

        // Check if logs directory exists
        if (!is_dir($logsDir)) {
            return null;
        }

        // Try to find laravel.log (single file logging)
        $singleFile = $logsDir . DIRECTORY_SEPARATOR . 'laravel.log';
        if (file_exists($singleFile)) {
            return $singleFile;
        }

        // Try to find the most recent daily log file
        $files = glob($logsDir . DIRECTORY_SEPARATOR . 'laravel-*.log');
        if (!empty($files)) {
            // Sort by modification time, newest first
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            return $files[0]; // Return most recent
        }

        return null;
    }

    private function matchesFilters($log, $level, $channel, $search)
    {
        if ($level !== 'all' && $log['level'] !== $level) {
            return false;
        }
        if ($channel !== 'all' && $log['channel'] !== $channel) {
            return false;
        }
        if ($search && stripos($log['message'], $search) === false) {
            return false;
        }
        return true;
    }

    private function extractChannel($message): string
    {
        $msg = strtolower($message);

        if (strpos($msg, 'gnews') !== false || strpos($msg, 'news') !== false) return 'news';
        if (strpos($msg, 'auth') !== false || strpos($msg, 'login') !== false) return 'auth';
        if (strpos($msg, 'groq') !== false) return 'groq';
        if (strpos($msg, 'database') !== false || strpos($msg, 'query') !== false) return 'db';
        if (strpos($msg, 'job') !== false || strpos($msg, 'scheduler') !== false) return 'scheduler';
        if (strpos($msg, 'api') !== false) return 'api';

        return 'general';
    }

    /**
     * Export logs as CSV
     */
    public function export(Request $request)
    {
        try {
            $logFile = $this->findLogFile();
            if (!$logFile || !file_exists($logFile)) {
                return response()->json(['success' => false, 'message' => 'No log file found'], 404);
            }

            $lines = array_reverse(file($logFile));
            $csv = "Timestamp,Level,Channel,Message\n";

            foreach ($lines as $line) {
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.+?)(?:\s+\{.+\})?$/', $line, $matches)) {
                    $csv .= sprintf(
                        '"%s","%s","%s","%s"' . "\n",
                        $matches[1],
                        $matches[3],
                        'general',
                        str_replace('"', '""', trim($matches[4]))
                    );
                }
            }

            return response()->streamDownload(
                fn() => print($csv),
                'system-logs-' . now()->format('Y-m-d-Hi') . '.csv',
                ['Content-Type' => 'text/csv']
            );

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}