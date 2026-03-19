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
            'model'     => 'nullable|string', // Allow model selection
            'prompt'    => 'nullable|string',
            'title'     => 'nullable|string',
            'sourceUrl' => 'nullable|string',
        ]);

        $content = $request->input('content');
        $url = $request->input('url');
        $style   = $request->input('style', 'professional');
        $title   = $request->input('title', '');

        // If topic query is provided, synthesize from multiple news sources
        if (!$content && !$url && $request->has('topic')) {
            $topic = $request->input('topic');
            $news = (new NewsController())->news(new Request(['q' => $topic, 'limit' => 5]));
            $data = $news->getData(true);
            if (empty($data['articles'])) {
                return response()->json([
                    'error' => "No news found for topic: {$topic}. Try a different query."
                ], 404);
            }

            // Build comprehensive context from multiple articles
            $articleContexts = [];
            $titles = [];
            foreach (array_slice($data['articles'], 0, 5) as $article) {
                $titles[] = $article['title'] ?? '';
                $desc = $article['description'] ?? '';
                $source = $article['source']['name'] ?? 'Unknown';
                if ($desc) {
                    $articleContexts[] = "[{$source}] {$article['title']}: {$desc}";
                }
            }

            $title = implode(' | ', array_slice($titles, 0, 3));
            $content = implode("\n\n---\n\n", $articleContexts);

            if (strlen(trim($content)) < 100) {
                return response()->json([
                    'error' => "Not enough content found for topic: {$topic}. Try a broader search term."
                ], 400);
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

        // Detect if this is a topic query (content built from multiple news articles)
        $isTopicQuery = $request->has('topic') && strpos($content, '---') !== false;

        $stylePrompts = [
            'professional' => 'Write a clear, professional 3-paragraph summary of this news article. Use formal language suitable for a business audience. Be concise and factual.',
            'detailed'     => 'Write a comprehensive multi-paragraph analysis. Cover all key points, provide context, and include relevant details. Well-structured with clear sections.',
            'concise'      => 'Write a brief 2-3 sentence summary capturing only the most essential information. Get straight to the point.',
            'bullets'      => 'Extract 5-7 high-impact bullet points. Each bullet should cover a distinct key insight or finding. Start each with "• ".',
            'eli5'         => 'Explain this in simple, easy-to-understand terms as if talking to a curious beginner. Avoid jargon. Make it accessible to everyone.',
            'executive'    => "Write an executive TL;DR with exactly 5 bullet points.\nEach bullet must be one concise sentence.\nStart each bullet with \"• \".",
            'genz'         => 'Summarize this news article in Gen-Z casual style — modern language, keep it real and relatable, no cap. Write 3-4 short punchy paragraphs.',
        ];

        $topicPrompts = [
            'professional' => 'Synthesize a comprehensive research briefing from multiple news sources. Present findings in a structured format with: (1) Overview, (2) Key Developments, (3) Implications, (4) What to Watch. Use formal business language.',
            'detailed'     => 'Create an in-depth research report synthesizing all the provided sources. Organize by themes, provide background context, and highlight conflicting viewpoints. Cover the topic thoroughly.',
            'concise'      => 'Synthesize the key takeaways into 2-3 concise sentences. Focus on what matters most.',
            'bullets'      => 'Extract 7-10 key insights from across all sources. Group related points together. Each bullet should be a distinct, valuable takeaway. Start with "• ".',
            'eli5'         => 'Explain this topic in simple terms that anyone can understand. Use everyday language, analogies, and avoid technical jargon. Make it approachable.',
            'executive'    => "Create an executive briefing with 5 bullet points synthesizing all sources. Each bullet: one clear insight. Group into: [KEY FINDINGS] and [STRATEGIC IMPLICATIONS]. Start bullets with \"• \".",
            'genz'         => 'Synthesize this topic in a way that is relatable and easy to understand. Keep it real, use modern language, no cap. Make it engaging and informative.',
        ];

        $styleInstruction = $request->input('prompt');
        if (!$styleInstruction) {
            $styleInstruction = $isTopicQuery 
                ? ($topicPrompts[$style] ?? $topicPrompts['professional'])
                : ($stylePrompts[$style] ?? $stylePrompts['professional']);
        }

        // Add topic context to prompt if applicable
        if ($isTopicQuery) {
            $topicName = $request->input('topic');
            $styleInstruction = "TOPIC: {$topicName}\n\n{$styleInstruction}\n\nSynthesize information from multiple reliable news sources. Identify common themes, differences in reporting, and emerging trends.";
        }

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
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("Failed to fetch URL {$url}: HTTP {$response->status()}");
                return null;
            }

            $html = $response->body();
            
            // Extract article content
            $content = $this->extractArticleText($html);
            
            if (strlen($content) < 100) { // Increased minimum length
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
            '/<div[^>]*class=["\'](?:.*?)?(?:article|post|story)-body(?:.*?)?["\'][^>]*>(.*?)<\/div>/is',
            '/<div[^>]*id=["\'](?:.*?)?article(?:.*?)?["\'][^>]*>(.*?)<\/div>/is',
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

        // 1. Keep the history in Summary table
        $summary = \App\Models\Summary::create([
            'user_id'       => auth()->id(),
            'original_text' => $request->original_text,
            'summary'       => $request->summary,
        ]);

        try {
            // 2. Add to SavedArticle table so it shows up in Content Library
            // We use a specific type and store the actual summary in the description field
            \App\Models\SavedArticle::create([
                'user_id'          => auth()->id(),
                'news_title'       => 'AI Summary: ' . (strlen($request->original_text) > 60 ? substr($request->original_text, 0, 60) . '...' : $request->original_text),
                'news_description' => $request->summary,
                'news_source'      => 'AI Summarizer',
                'news_url'         => 'summary-' . $summary->id . '-' . time(),
                'type'             => 'summary',
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to sync summary to library: " . $e->getMessage());
            // We still return the summary as the primary action succeeded
        }

        return response()->json($summary, 201);
    }

    public function summaries()
    {
        $summaries = Summary::where('user_id', auth()->id())->latest()->limit(20)->get();
        return response()->json($summaries);
    }
}