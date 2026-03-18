<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ config('app.name', 'Cup of Tea') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:        #0f0f0f;
            --bg-card:   #1a1a1a;
            --bg-card2:  #141414;
            --border:    #252525;
            --border2:   #2e2e2e;
            --lime:      #b6e040;
            --lime-h:    #cdf053;
            --lime-dim:  rgba(182,224,64,0.1);
            --text:      #f0f0f0;
            --muted:     #666;
            --muted2:    #444;
            --red:       #e05555;
            --red-dim:   rgba(224,85,85,0.12);
            --blue:      #60a5fa;
            --pink:      #f472b6;
            --amber:     #fbbf24;
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        html, body { 
            height: 100%; 
        }

        body { 
            font-family: 'Figtree', sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            overflow-x: hidden; 
        }

        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { 
            background: #2a2a2a; 
            border-radius: 3px; 
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: #333; 
        }

        /* LAYOUT */
        .app-shell { 
            display: flex; 
            height: 100vh; 
            overflow: hidden; 
        }

        /* SIDEBAR */
        .sidebar { 
            width: 200px; 
            min-width: 200px; 
            background: #111; 
            border-right: 1px solid var(--border); 
            display: flex; 
            flex-direction: column; 
            overflow-y: auto; 
            flex-shrink: 0;
        }

        .logo-wrap { 
            padding: 20px 16px 16px; 
            border-bottom: 1px solid var(--border); 
        }

        .logo-row { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }

        .logo-text { 
            font-size: 1rem; 
            font-weight: 800; 
            letter-spacing: 0.14em; 
            text-transform: uppercase; 
            color: var(--text); 
        }

        .logo-text .lime { 
            color: var(--lime); 
        }

        .logo-sub { 
            font-size: 0.62rem; 
            font-style: italic; 
            color: var(--muted); 
            margin-top: 3px; 
        }

        .nav-section { 
            padding: 14px 10px 4px; 
        }

        .nav-label { 
            font-size: 0.6rem; 
            font-weight: 700; 
            letter-spacing: 0.14em; 
            text-transform: uppercase; 
            color: var(--muted2); 
            padding: 0 8px; 
            margin-bottom: 6px; 
        }

        .nav-item { 
            display: flex; 
            align-items: center; 
            gap: 9px; 
            padding: 8px 10px; 
            border-radius: 8px; 
            cursor: pointer; 
            color: var(--muted); 
            font-size: 0.82rem; 
            font-weight: 500; 
            text-decoration: none; 
            transition: all 0.15s; 
            margin-bottom: 1px; 
            background: none; 
            border: none; 
            width: 100%; 
            font-family: inherit;
        }

        .nav-item:hover { 
            background: #1c1c1c; 
            color: #ccc; 
        }

        .nav-item.active { 
            background: var(--lime); 
            color: #0f0f0f; 
            font-weight: 700; 
        }

        .nav-item.active svg { 
            stroke: #0f0f0f; 
        }

        .nav-item svg { 
            flex-shrink: 0; 
            width: 15px; 
            height: 15px; 
        }

        .sidebar-bottom { 
            margin-top: auto; 
            padding: 10px; 
            border-top: 1px solid var(--border); 
        }

        .nav-item.logout { 
            color: var(--red); 
        }

        .nav-item.logout:hover { 
            background: var(--red-dim); 
            color: #f87171; 
        }

        /* MAIN */
        .main-area { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }

        /* TOPBAR */
        .topbar { 
            height: 58px; 
            background: #111; 
            border-bottom: 1px solid var(--border); 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 0 24px; 
            flex-shrink: 0; 
        }

        .search-wrap { 
            flex: 1; 
            max-width: 480px; 
            background: var(--bg-card); 
            border: 1px solid var(--border2); 
            border-radius: 9px; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            padding: 0 12px; 
            height: 36px; 
            transition: border-color 0.2s; 
        }

        .search-wrap:focus-within { 
            border-color: rgba(182,224,64,0.4); 
        }

        .search-wrap input { 
            background: none; 
            border: none; 
            outline: none; 
            color: var(--text); 
            font-size: 0.82rem; 
            font-family: inherit; 
            flex: 1; 
        }

        .search-wrap input::placeholder { 
            color: var(--muted2); 
        }

        .topbar-right { 
            margin-left: auto; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }

        .icon-btn { 
            width: 34px; 
            height: 34px; 
            border-radius: 8px; 
            background: var(--bg-card); 
            border: 1px solid var(--border); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            color: var(--muted); 
            transition: all 0.15s; 
            text-decoration: none; 
            position: relative;
        }

        .icon-btn:hover { 
            background: #222; 
            color: #ccc; 
        }

        .notif-badge { 
            position: absolute; 
            top: -3px; 
            right: -3px; 
            width: 14px; 
            height: 14px; 
            background: var(--red); 
            border-radius: 50%; 
            font-size: 0.55rem; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
        }

        .avatar { 
            width: 34px; 
            height: 34px; 
            border-radius: 50%; 
            background: var(--lime); 
            color: #0f0f0f; 
            font-size: 0.7rem; 
            font-weight: 800; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
        }

        /* PAGE */
        .page-body { 
            flex: 1; 
            overflow-y: auto; 
            padding: 28px; 
        }

        .page-eyebrow { 
            font-size: 0.65rem; 
            font-weight: 700; 
            letter-spacing: 0.12em; 
            text-transform: uppercase; 
            color: var(--muted); 
            margin-bottom: 4px; 
        }

        .page-title { 
            font-size: 1.9rem; 
            font-weight: 800; 
            letter-spacing: -0.025em; 
            color: var(--text); 
            margin-bottom: 24px; 
        }

        .live-pill { 
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
            font-size: 0.7rem; 
            color: var(--muted); 
        }

        .live-pill::before { 
            content: ''; 
            width: 7px; 
            height: 7px; 
            border-radius: 50%; 
            background: var(--lime); 
            display: inline-block; 
            animation: liveblink 2s infinite; 
        }

        @keyframes liveblink { 
            0%, 100% { opacity: 1; } 
            50% { opacity: 0.25; } 
        }

        /* BUTTONS */
        .btn { 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            padding: 7px 14px; 
            border-radius: 8px; 
            font-size: 0.8rem; 
            font-weight: 700; 
            font-family: inherit; 
            cursor: pointer; 
            border: none; 
            transition: all 0.15s; 
            text-decoration: none;
        }

        .btn:disabled { 
            opacity: 0.6; 
            cursor: not-allowed; 
        }

        .btn-lime { 
            background: var(--lime); 
            color: #0f0f0f; 
        }

        .btn-lime:hover:not(:disabled) { 
            background: var(--lime-h); 
            transform: translateY(-1px); 
        }

        .btn-outline { 
            background: transparent; 
            border: 1px solid var(--border2); 
            color: var(--muted); 
        }

        .btn-outline:hover { 
            border-color: var(--lime); 
            color: var(--lime); 
        }

        .btn-outline-lime { 
            background: transparent; 
            border: 1px solid rgba(182,224,64,0.35); 
            color: var(--lime); 
        }

        .btn-outline-lime:hover { 
            background: var(--lime-dim); 
        }

        .btn-lg { 
            padding: 12px 24px; 
            font-size: 0.95rem; 
            border-radius: 10px; 
        }

        /* TAGS */
        .tag { 
            display: inline-flex; 
            align-items: center; 
            padding: 2px 8px; 
            border-radius: 5px; 
            font-size: 0.62rem; 
            font-weight: 700; 
            letter-spacing: 0.06em; 
            text-transform: uppercase;
        }

        .tag-tech   { background: rgba(96,165,250,0.12);  color: #60a5fa; }
        .tag-biz    { background: rgba(182,224,64,0.1);   color: var(--lime); }
        .tag-world  { background: rgba(251,191,36,0.1);   color: var(--amber); }
        .tag-health { background: rgba(244,114,182,0.1);  color: var(--pink); }
        .tag-sci    { background: rgba(167,139,250,0.12); color: #a78bfa; }
        .tag-gen    { background: rgba(102,102,102,0.15); color: #888; }

        /* EMPTY STATE */
        .empty-state { 
            text-align: center; 
            padding: 56px 20px; 
        }

        .empty-icon { 
            width: 54px; 
            height: 54px; 
            background: var(--red-dim); 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 14px; 
        }

        .empty-title { 
            font-size: 0.95rem; 
            font-weight: 700; 
            color: var(--red); 
            margin-bottom: 6px; 
        }

        .empty-sub { 
            font-size: 0.8rem; 
            color: var(--muted); 
            margin-bottom: 18px; 
        }

        /* UTILITIES */
        .spin { 
            animation: spin-anim 0.8s linear infinite; 
        }

        @keyframes spin-anim { 
            from { transform: rotate(0deg); } 
            to { transform: rotate(360deg); } 
        }

        [x-cloak] { 
            display: none !important; 
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .page-body {
                padding: 16px;
            }
        }
    </style>
@stack('styles')

<style>
/* Notyf Toast Overrides - Theme Integration */
.notyf__toast {
  font-family: 'Figtree', sans-serif;
  border-radius: 10px;
  backdrop-filter: blur(12px);
  box-shadow: 0 12px 32px rgba(0,0,0,0.4);
  padding: 14px 18px;
  min-height: 56px;
  font-weight: 600;
  font-size: 0.88rem;
  color: #0f0f0f !important;
}

.notyf__icon--success svg,
.notyf__icon--error svg {
  stroke: #0f0f0f !important;
  stroke-width: 2.2;
}

.notyf__wrapper {
  padding-top: 20px;
}

.notyf__ripple {
  border-radius: 10px;
}

@media (max-width: 480px) {
  .notyf__toast {
    font-size: 0.85rem;
    padding: 12px 16px;
    max-width: 90vw;
  }
}
</style>

</head>
<body>
<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo-wrap">
            <div class="logo-row">
                <image src="{{ asset('logo.png') }}" alt="Cup of Tea" width="24" height="24"></image>
                <span class="logo-text">Cup of<span class="lime"> Tea</span></span>
            </div>
            <div class="logo-sub">Sip on the facts.</div>
        </div>

        <!-- Main Nav -->
        <div class="nav-section">
            <div class="nav-label">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('news.feed') }}" class="nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                News Feed
            </a>
            <a href="{{ route('ai.summarizer') }}" class="nav-item {{ request()->routeIs('ai.summarizer') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                AI Summarizer
            </a>
            <a href="{{ route('content.library') }}" class="nav-item {{ request()->routeIs('content.library') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Content Library
            </a>
            @if(auth()->user()?->is_admin)
            <a href="{{ route('analytics') }}" class="nav-item {{ request()->routeIs('analytics') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Analytics
            </a>
            <a href="/users" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>
            <a href="{{ route('system.logs') }}" class="nav-item {{ request()->routeIs('system.logs') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                System Logs
            </a>
            @endif
        </div>

        <!-- Settings -->
        <div class="sidebar-bottom">
            <div class="nav-label">Settings</div>
            <a href="#" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                AI Settings
            </a>
            <a href="#" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                System Settings
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item logout" onclick="return confirm('Logout from all sessions? All data remains safe.')" style="width:100%;text-align:left;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-area">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="Search articles, summaries..." />
            </div>
            @auth
            <div class="topbar-right">
                <div class="icon-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <div class="notif-badge">3</div>
                </div>
                <a href="{{ route('profile.show') }}" class="icon-btn" title="Profile">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06-.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </a>
                <div class="avatar" title="{{ auth()->user()?->name ?? 'User' }}">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}</div>
            </div>
            @endauth
        </div>

        <!-- PAGE BODY -->
        <div class="page-body" x-data="sessionToasts">
            @yield('content')
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('sessionToasts', () => ({
      init() {
        const success = @json(session('success'));
        const error = @json(session('error'));
        const warning = @json(session('warning'));
        const info = @json(session('info'));
        
        if (success) window.toast.success(success);
        if (error) window.toast.error(error);
        if (warning) window.toast.warning(warning);
        if (info) window.toast.open('info', info);
      }
    }));
});
</script>

@stack('scripts')

</body>
</html>
