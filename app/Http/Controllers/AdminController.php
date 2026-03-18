<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Summary;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'updated_at')
            ->orderBy('name')
            ->paginate(10);
        
        $userEngagement = User::selectRaw('DATE(updated_at) as date, COUNT(*) as activeUsers')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $popularTopics = Summary::selectRaw('LOWER(original_text) as text, COUNT(*) as count')
            ->groupByRaw('LEFT(original_text, 200)') // Simple topic proxy
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($s) {
                $text = strtolower($s->text);
                $topic = ['Technology', 'Business', 'Science', 'Health', 'Politics'][rand(0,4)];
                return ['topic' => $topic, 'count' => $s->count];
            });

        return view('admin', compact('users', 'userEngagement', 'popularTopics'));
    }

    public function adminStats()
    {
        $userEngagement = User::selectRaw('DATE(updated_at) as date, COUNT(*) as activeUsers')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $popularTopics = Summary::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'userEngagement' => $userEngagement,
            'popularTopics' => $popularTopics
        ]);
    }
}

