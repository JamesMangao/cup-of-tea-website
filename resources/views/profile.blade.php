@extends('layouts.app')

@section('title', 'Your Profile')

@section('content')
<div class="animate-fade-in">
    <div class="page-eyebrow">Account Settings</div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="page-title mb-0">Your Profile</h1>
        <div class="flex items-center gap-2">
            <span class="live-dot"></span>
            <span class="text-xs font-bold text-muted uppercase tracking-widest">Active Session</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Profile Header & Main Info -->
        <div class="lg:col-span-8 space-y-8">
            <div class="glass-card p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
                
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
                    <div class="avatar shadow-2xl" style="width: 100px; height: 100px; font-size: 2rem; border: 4px solid var(--primary-glow); background: linear-gradient(135deg, var(--primary) 0%, #88ab1a 100%); color: #0f0f0f;">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                    
                    <div class="text-center md:text-left flex-1">
                        <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                            <h2 class="text-3xl font-black tracking-tight">{{ $user->name }}</h2>
                            @if($user->is_admin)
                                <span class="tag-biz" style="font-size: 0.65rem;">ADMINISTRATOR</span>
                            @else
                                <span class="tag-gen" style="font-size: 0.65rem;">MEMBER</span>
                            @endif
                        </div>
                        <p class="text-secondary text-lg mb-6">{{ $user->email }}</p>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-muted uppercase tracking-widest mb-1">Member Since</span>
                                <span class="text-sm font-bold">{{ $user->created_at->format('F d, Y') }}</span>
                            </div>
                            <div class="w-px h-8 bg-border-light hidden md:block"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-muted uppercase tracking-widest mb-1">Account Status</span>
                                <span class="text-sm font-bold text-primary flex items-center gap-2">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Verified
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('profile.edit') }}" class="glass-card p-6 flex items-center gap-4 group hover:border-primary transition-all">
                    <div class="w-12 h-12 rounded-xl bg-primary-glow text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-white">Edit Profile</div>
                        <div class="text-xs text-muted">Update your personal information</div>
                    </div>
                </a>
                
                <a href="{{ route('profile.edit') }}#password" class="glass-card p-6 flex items-center gap-4 group hover:border-blue transition-all">
                    <div class="w-12 h-12 rounded-xl bg-blue-dim text-blue flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-white">Security</div>
                        <div class="text-xs text-muted">Manage your password and keys</div>
                    </div>
                </a>
            </div>

            <div class="flex items-center justify-between pt-8 border-t border-border-light">
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete account? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-400 transition-colors flex items-center gap-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Permanently Delete Account
                    </button>
                </form>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-lime px-8">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card p-8">
                <h3 class="text-xs font-black text-muted uppercase tracking-widest mb-6">Activity Snapshot</h3>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-lime"></div>
                            <span class="text-sm font-bold text-secondary">Saved Articles</span>
                        </div>
                        <span class="text-lg font-black text-white">{{ $savedCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue"></div>
                            <span class="text-sm font-bold text-secondary">AI Summaries</span>
                        </div>
                        <span class="text-lg font-black text-white">{{ $summaryCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-pink"></div>
                            <span class="text-sm font-bold text-secondary">Engagement Rate</span>
                        </div>
                        <span class="text-lg font-black text-white">94%</span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-border-light text-center">
                    <div class="text-[10px] font-black text-muted uppercase tracking-widest mb-2">Last Activity</div>
                    <div class="text-xs font-bold text-secondary italic">" {{ $user->updated_at->diffForHumans() }} "</div>
                </div>
            </div>

            <div class="glass-card p-8 bg-gradient-to-br from-[#171717] to-[#121212]">
                <div class="w-10 h-10 rounded-lg bg-pink-dim text-pink flex items-center justify-center mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <h4 class="font-bold text-white mb-2">Pro Insights</h4>
                <p class="text-xs text-muted leading-relaxed mb-4">You've saved {{ $savedCount }} articles this week. You're in the top 5% of active readers! Keep it up to earn more badges.</p>
                <button class="text-[10px] font-black text-pink uppercase tracking-widest hover:underline">View Achievements →</button>
            </div>
        </div>
    </div>
</div>
@endsection
@endsection