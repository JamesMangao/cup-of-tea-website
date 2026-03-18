<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedArticle;
use Illuminate\Support\Facades\Auth;

class ContentLibraryController extends Controller
{
    /**
     * Display a listing of the saved articles.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $query = SavedArticle::where('user_id', $user->id);

        // Filter by type
        $filterType = $request->get('type', 'all');
        
        if ($filterType !== 'all') {
            $query->where('type', $filterType);
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'az':
                $query->orderBy('news_title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $savedArticles = $query->get();

        // Convert to array format that Alpine expects
        $items = $savedArticles->map(function ($article) {
            return [
                'id' => $article->id,
                'type' => $article->type,
                'title' => $article->news_title,
                'description' => $article->news_description,
                'content' => $article->news_description,
                'source' => $article->news_source,
                'url' => $article->news_url,
                'image' => $article->news_image,
                'date' => $article->created_at->toIso8601String(),
                'created_at' => $article->created_at->toIso8601String(),
            ];
        })->toArray();

        if ($request->wantsJson()) {
            return response()->json(['items' => $items]);
        }

        return view('content-library', [
            'items' => $items
        ]);
    }

    /**
     * Delete a saved article
     */
    public function destroy($id)
    {
        $saved = SavedArticle::findOrFail($id);
        
        // Check authorization
        if ($saved->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $saved->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Removed from library']);
        }

        return back()->with('success', 'Removed from library');
    }

    /**
     * Clear all saved articles for user
     */
    public function clearAll()
    {
        $user = Auth::user();
        SavedArticle::where('user_id', $user->id)->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Library cleared']);
        }

        return back()->with('success', 'Library cleared');
    }
}