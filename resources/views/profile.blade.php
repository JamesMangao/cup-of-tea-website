@extends('layouts.app')

@section('title', 'Your Profile')

@section('content')
<div class="animate-fade-in">
    <div class="page-eyebrow">Account Intelligence</div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="page-title mb-0">Your Profile</h1>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-lime-dim border border-lime/20">
            <span class="live-blink" style="width:6px; height:6px; background:var(--lime); border-radius:50%;"></span>
            <span class="text-[10px] font-black text-lime uppercase tracking-widest">Active Session</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Profile Header & Main Info -->
        <div class="lg:col-span-8 space-y-8">
            <div class="glass-card p-10 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 opacity-[0.03] rotate-12">
                    <svg width="240" height="240" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
                
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
                    <div class="avatar avatar-lg avatar-glow shadow-2xl">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                    
                    <div class="text-center md:text-left flex-1">
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                            <h2 class="text-3xl font-black tracking-tight text-white">{{ $user->name }}</h2>
                            @if($user->is_admin)
                                <span class="tag tag-blue">SYSTEM ADMINISTRATOR</span>
                            @else
                                <span class="tag tag-lime">MEMBER NODE</span>
                            @endif
                        </div>
                        <p class="text-secondary text-lg mb-8 opacity-70">{{ $user->email }}</p>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-12">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-muted uppercase tracking-widest mb-1.5 opacity-50">Discovery Date</span>
                                <span class="text-sm font-bold text-white">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-muted uppercase tracking-widest mb-1.5 opacity-50">Signal Integrity</span>
                                <span class="text-sm font-bold text-lime flex items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Verified
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('profile.edit') }}" class="glass-card p-6 flex items-center gap-5 group transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-lime-dim text-lime flex items-center justify-center group-hover:scale-110 transition-transform border border-lime/10">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-white mb-0.5">Edit Configuration</div>
                        <div class="text-xs text-muted">Update personal parameters</div>
                    </div>
                </a>
                
                <a href="{{ route('profile.edit') }}#password" class="glass-card p-6 flex items-center gap-5 group transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-blue-dim text-blue flex items-center justify-center group-hover:scale-110 transition-transform border border-blue/10">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-white mb-0.5">Security Protocol</div>
                        <div class="text-xs text-muted">Passwords & encryption</div>
                    </div>
                </a>
            </div>

            <!-- Destructive Zone -->
            <div class="glass-card p-8 border-t border-red/10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <div class="font-bold text-white mb-1">Delete Neural Link</div>
                        <div class="text-xs text-muted">This action is irreversible and will purge all data.</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('profile.destroy') }}" x-data="{ confirming: false }" @submit.prevent="if(confirm('Purge all data? This cannot be undone.')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-black text-red-500 hover:text-red-400 uppercase tracking-widest flex items-center gap-2 transition-colors">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Purge Identity
                            </button>
                        </form>
                        <div class="h-4 w-px bg-border"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-black text-lime uppercase tracking-widest hover:text-white transition-colors">
                                Disconnect
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card p-8 bg-gradient-to-b from-[#1a1a1a] to-[#141414]">
                <h3 class="text-[10px] font-black text-muted uppercase tracking-widest mb-8 opacity-60">Engagement Pulse</h3>
                
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-lime-dim flex items-center justify-center text-lime border border-lime/10">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-white">Archives</span>
                        </div>
                        <span class="text-xl font-black text-white">{{ $savedCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-dim flex items-center justify-center text-blue border border-blue/10">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <span class="text-sm font-bold text-white">Synthetics</span>
                        </div>
                        <span class="text-xl font-black text-white">{{ $summaryCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-pink-dim flex items-center justify-center text-pink border border-pink/10">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                            </div>
                            <span class="text-sm font-bold text-white">Integrity</span>
                        </div>
                        <span class="text-xl font-black text-white">94%</span>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-border opacity-80 text-center">
                    <div class="text-[9px] font-black text-muted uppercase tracking-widest mb-2 opacity-50">Last Signal Sync</div>
                    <div class="text-xs font-bold text-secondary italic">" {{ $user->updated_at->diffForHumans() }} "</div>
                </div>
            </div>

            <div class="glass-card p-8 bg-gradient-to-br from-[#1c1c1c] to-[#111] border border-pink/10">
                <div class="w-12 h-12 rounded-2xl bg-pink-dim text-pink flex items-center justify-center mb-6 shadow-lg shadow-pink/5">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <h4 class="text-lg font-black text-white mb-2 tracking-tight">Neural Elite</h4>
                <p class="text-xs text-muted leading-relaxed mb-6">You've synthesized {{ $summaryCount }} insights this period. Your engagement is exceptional. Unlock detailed analytics soon.</p>
                <button class="w-full py-3 rounded-xl bg-pink-dim border border-pink/20 text-[10px] font-black text-pink uppercase tracking-widest hover:bg-pink/20 hover:scale-[1.02] transition-all">
                    System Achievements
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
on