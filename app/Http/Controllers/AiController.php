<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Summary;

class AiController extends Controller
{
    /**
     * Groq free-tier models (fast, reliable, no rate limit issues).
     * Fallback order: fastest → most capable.
     */
    private array $models = [
        'llama-3.1-8b-instant',   // Fastest, great for summaries
        'llama3-8b-8192',         // Reliable fallback
        'llama3-70b-8192',        // Most capable, use if others fail
        'gemma2-9b-it',           // Google Gemma via Groq
        'mixtral-8x7b-32768',     // Large context window fallback
    ];

    public function summarize(Request $request)
    {
        $request->validate([
            'content'   => 'nullable|string|max:25000',
            'url'       => 'nullable|string|url',
            'style'     => 'nullable|string',
            'prompt'    => 'nullable|string',
            'title'     => 'nullable|string',
            'sourceUrl' => 'nullable|string',
        ]);

        $content = $request->input('content');
        $url = $request->input('url');
        $style   = $request->input('style', 'professional');
        $title   = $request->input('title', '');

        // If topic query is provided, fetch latest news for that topic
        if (!$content && !$url && $request->has('topic')) {
            $topic = $request->input('topic');
            $news = (new NewsController())->news(new Request(['q' => $topic]));
            $data = $news->getData(true);
            if (!empty($data['articles'])) {
                $firstArticle = $data['articles'][0];
                $url = $firstArticle['url'];
                $title = $firstArticle['title'];
            } else {
                return response()->json([
                    'error' => "No news found for topic: {$topic}. Try a different query."
                ], 404);
            }
        }

        // If URL is provided but no content, try to fetch it
        if ($url && !$content) {
            $content = $this->fetchArticleContent($url);
            if (!$content) {
                return response()->json([
                    'error' => 'Could not fetch article content from URL. Please paste the text directly instead.'
                ], 400);
            }
        }

        // Validate we have content
        if (!$content || strlen(trim($content)) < 50) {
            return response()->json([
                'error' => 'Please provide article content or a valid URL. Minimum 50 characters required.'
            ], 400);
        }

        $stylePrompts = [
            'professional' => 'Write a clear, professional 3-paragraph summary of this news article. Use formal language suitable for a business audience. Be concise and factual.',
            'executive'    => "Write an executive TL;DR with exactly 5 bullet points.\nEach bullet must be one concise sentence.\nStart each bullet with \"• \".",
            'genz'         => 'Summarize this news article in Gen-Z casual style — modern language, keep it real and relatable, no cap. Write 3-4 short punchy paragraphs.',
        ];

        $styleInstruction = $request->input('prompt')
            ?? ($stylePrompts[$style] ?? $stylePrompts['professional']);

        // Truncate to ~6000 chars — well within Groq's context window
        $truncated = strlen($content) > 6000
            ? substr($content, 0, 6000) . '...'
            : $content;

        $userMessage = implode("\n\n", array_filter([
            $title ? "Article Title: {$title}" : null,
            "Article Content:\n{$truncated}",
            "---\n{$styleInstruction}",
        ]));

        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'error' => 'GROQ_API_KEY not set. Get a free key at https://console.groq.com'
            ], 500);
        }

        foreach ($this->models as $model) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'       => $model,
                        'messages'    => [
                            [
                                'role'    => 'system',
                                'content' => 'You are a professional news summarizer. Respond with only the summary text — no preamble like "Here is a summary:", no meta-commentary, just the summary itself.',
                            ],
                            [
                                'role'    => 'user',
                                'content' => $userMessage,
                            ],
                        ],
                        'max_tokens'  => 700,
                        'temperature' => 0.7,
                        'stream'      => false,
                    ]);

                if ($response->successful()) {
                    $summary = $response->json('choices.0.message.content') ?? null;

                    if (empty(trim($summary ?? ''))) {
                        Log::warning("Groq model {$model} returned empty content");
                        continue;
                    }

                    // Strip common preamble the model sometimes adds anyway
                    $summary = preg_replace('/^(here(?:\'s| is)(?: a| the)? (?:summary|breakdown|tldr)[:\s]*\n?)/i', '', trim($summary));
                    $summary = preg_replace('/^(summary[:\s]+)/i', '', $summary);
                    $summary = trim($summary);

                    Log::info("Summary OK — model: {$model}, style: {$style}, chars: " . strlen($summary));

                    return response()->json([
                        'summary' => $summary,
                        'model'   => $model,
                        'style'   => $style,
                    ]);
                }

                $errorBody = $response->json('error.message') ?? $response->body();
                Log::warning("Groq {$model} failed HTTP {$response->status()}: {$errorBody}");

                // Don't try next model on auth errors — key is wrong
                if ($response->status() === 401) {
                    return response()->json(['error' => 'Invalid GROQ_API_KEY.'], 401);
                }

            } catch (\Throwable $e) {
                Log::warning("Groq {$model} exception: " . $e->getMessage());
            }
        }

        return response()->json([
            'error' => 'All Groq models are currently unavailable. Please try again shortly.'
        ], 503);
    }

    /**
     * Fetch article content from a URL
     */
    private function fetchArticleContent(string $url): ?string
    {
        try {
            $response = Http::timeout(15)
                ->connectTimeout(10)
                ->get($url);

            if (!$response->successful()) {
                Log::warning("Failed to fetch URL {$url}: HTTP {$response->status()}");
                return null;
            }

            $html = $response->body();
            
            // Extract article content
            $content = $this->extractArticleText($html);
            
            if (strlen($content) < 50) {
                Log::warning("Extracted content too short from {$url}");
                return null;
            }

            return $content;

        } catch (\Throwable $e) {
            Log::error("Error fetching article from {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract readable text from HTML
     */
    private function extractArticleText(string $html): string
    {
        // Remove script and style tags
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $html);

        // Look for article containers
        $patterns = [
            '/<article[^>]*>(.*?)<\/article>/is',
            '/<div[^>]*class=["\'](?:.*?)?article(?:.*?)?["\'][^>]*>(.*?)<\/div>/is',
            '/<main[^>]*>(.*?)<\/main>/is',
            '/<div[^>]*id=["\'](?:.*?)?content(?:.*?)?["\'][^>]*>(.*?)<\/div>/is',
        ];

        $content = '';
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $content = $matches[1];
                break;
            }
        }

        // Fallback to body
        if (empty($content)) {
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches);
            $content = $matches[1] ?? $html;
        }

        // Extract paragraphs
        preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $matches);
        $paragraphs = $matches[1] ?? [];

        // Clean paragraphs
        $text = implode("\n\n", array_map(function($para) {
            $para = strip_tags($para);
            $para = html_entity_decode($para);
            $para = trim($para);
            return $para;
        }, $paragraphs));

        // Fallback: extract all visible text
        if (strlen($text) < 50) {
            $text = strip_tags($content);
            $text = html_entity_decode($text);
            $text = preg_replace('/\s+/', ' ', $text);
        }

        return trim($text);
    }

    public function storeSummary(Request $request)
    {
        $request->validate([
            'original_text' => 'required|string',
            'summary'       => 'required|string',
        ]);

        $summary = Summary::create([
            'user_id'       => auth()->id(),
            'original_text' => $request->original_text,
            'summary'       => $request->summary,
        ]);

        return response()->json($summary, 201);
    }

    public function summaries()
    {
        $summaries = Summary::with('user')->latest()->limit(20)->get();
        return response()->json($summaries);
    }
}