<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cup of Tea - AI News Summarizer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-deep:    #0f0f0f;
            --bg-card:    #1a1a1a;
            --border:     #2e2e2e;
            --lime:       #b6e040;
            --lime-glow:  rgba(182, 224, 64, 0.12);
            --text-main:  #f0f0f0;
            --text-muted: #888888;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg-deep);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* NAV */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(15, 15, 15, 0.9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: baseline;
            gap: 0.4rem;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.14em;
            color: var(--text-main);
            text-transform: uppercase;
        }
        .nav-brand .lime { color: var(--lime); }
        .nav-brand .tagline {
            font-size: 0.63rem;
            font-weight: 400;
            font-style: italic;
            color: var(--text-muted);
            letter-spacing: 0.03em;
            text-transform: none;
            margin-left: 0.15rem;
        }
        .nav-links { display: flex; align-items: center; gap: 0.5rem; }
        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.4rem 0.9rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-link:hover { color: var(--text-main); }
        .nav-cta {
            background: var(--lime);
            color: #0f0f0f;
            text-decoration: none;
            padding: 0.45rem 1.2rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 700;
            transition: background 0.2s, transform 0.15s;
        }
        .nav-cta:hover { background: #cdf053; transform: translateY(-1px); }

        /* HERO */
        .hero {
            max-width: 860px;
            margin: 0 auto;
            padding: 7rem 2rem 5rem;
            text-align: center;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            height: 280px;
            background: radial-gradient(ellipse, var(--lime-glow) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid rgba(182, 224, 64, 0.3);
            color: var(--lime);
            background: rgba(182, 224, 64, 0.06);
            padding: 0.3rem 1rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 2.5rem;
        }
        .hero-badge .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--lime);
            animation: blink 2s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        .hero-logo {
            font-size: clamp(3rem, 9vw, 6.5rem);
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4em;
            white-space: nowrap;
        }
        .hero-logo .lime { color: var(--lime); }

        .hero-sub {
            font-size: 1rem;
            font-style: italic;
            color: var(--text-muted);
            margin-bottom: 2rem;
            letter-spacing: 0.04em;
        }

        .hero-desc {
            font-size: clamp(0.95rem, 1.8vw, 1.15rem);
            color: var(--text-muted);
            max-width: 560px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: var(--lime);
            color: #0f0f0f;
            text-decoration: none;
            padding: 0.85rem 2.1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            background: #cdf053;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(182,224,64,0.22);
        }
        .btn-ghost {
            border: 1px solid var(--border);
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.85rem 2.1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            background: transparent;
            transition: border-color 0.2s, color 0.2s, transform 0.15s;
        }
        .btn-ghost:hover {
            border-color: var(--lime);
            color: var(--lime);
            transform: translateY(-2px);
        }

        /* STATS */
        .stats {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem 0;
            display: flex;
            justify-content: center;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--lime);
        }
        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 0.2rem;
        }

        /* FEATURE CARDS */
        .features {
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 2rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }
        @media (max-width: 768px) {
            .features { grid-template-columns: 1fr; }
            .hero-logo { font-size: 2.8rem; letter-spacing: 0.1em; }
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
        }
        .card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--lime), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .card:hover {
            border-color: rgba(182,224,64,0.35);
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.45);
        }
        .card:hover::after { opacity: 1; }

        .card-icon {
            width: 48px; height: 48px;
            background: rgba(182,224,64,0.08);
            border: 1px solid rgba(182,224,64,0.18);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1.25rem;
        }
        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
        }
        .card-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* STYLES SECTION */
        .styles-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem 6rem;
        }
        .section-eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--lime);
            margin-bottom: 0.6rem;
        }
        .section-title {
            font-size: clamp(1.4rem, 2.5vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.7rem;
        }
        .section-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            max-width: 460px;
            line-height: 1.75;
            margin-bottom: 2rem;
        }
        .pills { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .pill {
            border: 1px solid var(--border);
            background: var(--bg-card);
            border-radius: 10px;
            padding: 0.85rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.2s;
            cursor: default;
        }
        .pill small {
            display: block;
            font-size: 0.68rem;
            font-weight: 400;
            margin-top: 0.2rem;
        }
        .pill:hover, .pill.active {
            border-color: var(--lime);
            color: var(--lime);
            background: rgba(182,224,64,0.06);
        }
        .pill.active small, .pill:hover small { color: rgba(182,224,64,0.55); }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.78rem;
            letter-spacing: 0.02em;
        }
        footer a { color: var(--lime); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        .page-entrance {
            animation: slideUpFade 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="page-entrance">

    <!-- NAV -->
    <nav>
        <div class="nav-inner">
            <a href="/" class="nav-brand">
                <img src="{{ asset('logo.png') }}" alt="Cup of Tea Logo" style="width: 1em; height: 1em; margin-right: 0.2em; vertical-align: middle;"> <span class="logo-text">Cup of<span class="lime"> Tea</span></span>
            </a>
            <div class="nav-links">
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-badge">
            <span class="dot"></span>
            Powered by GNews &amp; Groq AI
        </div>

        <div class="hero-logo">
            <img src="{{ asset('logo.png') }}" alt="Cup of Tea Logo" style="width: 1em; height: 1em; margin-right: 0.2em; vertical-align: middle;"> <span class="logo-text">Cup of<span class="lime"> Tea</span></span>
        </div>
        <p class="hero-sub">Sip on the facts.</p>

        <p class="hero-desc">
            Stay informed without the overwhelm. Get AI-powered summaries of breaking news — delivered in the style that suits you best.
        </p>

        <div class="hero-btns">
            <a href="{{ route('register') }}" class="btn-primary">Register</a>
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats">
        <div class="stat">
            <div class="stat-value">3+</div>
            <div class="stat-label">Summary Styles</div>
        </div>
        <div class="stat">
            <div class="stat-value">5+</div>
            <div class="stat-label">News Categories</div>
        </div>
        <div class="stat">
            <div class="stat-value">AI</div>
            <div class="stat-label">Groq</div>
        </div>
    </div>

    <!-- FEATURES -->
    <section class="features">
        <div class="card">
            <div class="card-icon">📰</div>
            <div class="card-title">Latest Headlines</div>
            <p class="card-desc">Pull top stories from GNews across tech, business, politics, and more — always fresh, always relevant.</p>
        </div>
        <div class="card">
            <div class="card-icon">✨</div>
            <div class="card-title">AI Summaries</div>
            <p class="card-desc">Smart summaries powered by Groq. Long articles distilled in seconds.</p>
        </div>
        <div class="card">
            <div class="card-icon">⚙️</div>
            <div class="card-title">Admin Dashboard</div>
            <p class="card-desc">Manage users, review summaries, and track usage stats — secured with Laravel Breeze authentication.</p>
        </div>
    </section>

    <!-- STYLES -->
    <div class="styles-wrap">
        <p class="section-eyebrow">Your voice, your news</p>
        <h2 class="section-title">Pick your summary style</h2>
        <p class="section-desc">Same story. Three totally different vibes. Choose how you want to digest the news.</p>
        <div class="pills">
            <div class="pill active">
                Professional
                <small>Clear &amp; formal</small>
            </div>
            <div class="pill">
                Executive
                <small>TL;DR bullet points</small>
            </div>
            <div class="pill">
                Gen-Z
                <small>No cap, full tea ☕</small>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        Built with <a href="https://laravel.com" target="_blank">Laravel 12</a> · Tailwind CSS · GNews · Groq AI
    </footer>

</body>
</html>