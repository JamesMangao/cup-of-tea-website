<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display AI Settings page
     */
    public function ai()
    {
        $groqModels = [
            'llama-3.1-8b-instant',
            'llama3-8b-8192',
            'llama3-70b-8192',
            'gemma2-9b-it',
            'mixtral-8x7b-32768',
        ];

        $aiStats = [
            'groq_key_set' => !empty(env('GROQ_API_KEY')),
            'gnews_key_set' => !empty(env('GNEWS_API_KEY')),
            'default_model' => config('services.groq.default_model', 'llama-3.1-8b-instant'),
            'temperature' => config('services.groq.temperature', 0.7),
        ];

        return view('ai-settings', compact('groqModels', 'aiStats'));
    }

    /**
     * Test AI connection (GROQ)
     */
    public function testAiConnection(Request $request)
    {
        try {
            $apiKey = env('GROQ_API_KEY');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'GROQ_API_KEY not set in .env'
                ]);
            }

            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get('https://api.groq.com/openai/v1/models');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful! ' . $response->json('data.0.id'),
                    'models_available' => count($response->json('data', []))
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'API responded with status: ' . $response->status()
            ], 400);

        } catch (\Exception $e) {
            Log::error('AI Connection Test Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display System Settings page
     */
    public function system()
    {
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'log_level' => config('logging.default'),
            'app_env' => app()->environment(),
            'debug_mode' => config('app.debug'),
        ];

        return view('system-settings', compact('systemInfo'));
    }

    /**
     * Clear application caches
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            Log::info('System caches cleared by admin: ' . auth()->user()->email);

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Cache Clear Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cache clear failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

