<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — Cup of Tea</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:        #0f0f0f;
            --bg-card:   #1a1a1a;
            --border:    #2e2e2e;
            --border-focus: rgba(182,224,64,0.5);
            --lime:      #b6e040;
            --lime-hover:#cdf053;
            --lime-glow: rgba(182,224,64,0.10);
            --text:      #f0f0f0;
            --muted:     #888;
            --error:     #f87171;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        body::before {
            content: '';
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 300px;
            background: radial-gradient(ellipse, var(--lime-glow) 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--lime), transparent);
        }

        .logo {
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .logo-lockup {
            display: inline-flex;
            align-items: center;
            gap: 0.35em;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--text);
        }
        .logo-lockup .lime { color: var(--lime); }
        .logo-tagline {
            font-size: 0.78rem;
            font-style: italic;
            color: var(--muted);
            letter-spacing: 0.04em;
            margin-top: 0.25rem;
        }

        .card-title {
            text-align: center;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--lime);
            margin: 1.25rem 0 1.75rem;
            letter-spacing: -0.01em;
        }

        .field { margin-bottom: 1.1rem; }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: #111;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input::placeholder { color: #444; }
        input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(182,224,64,0.08);
        }

        .error-msg {
            font-size: 0.78rem;
            color: var(--error);
            margin-top: 0.35rem;
        }

        /* Password strength hint */
        .hint {
            font-size: 0.74rem;
            color: #555;
            margin-top: 0.35rem;
        }

        .btn-submit {
            width: 100%;
            background: var(--lime);
            color: #0f0f0f;
            border: none;
            border-radius: 10px;
            padding: 0.9rem;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-submit:hover {
            background: var(--lime-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(182,224,64,0.2);
        }
        .btn-submit:active { transform: translateY(0); }

        .footer-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.82rem;
            color: var(--muted);
        }
        .footer-link a {
            color: var(--lime);
            text-decoration: none;
            font-weight: 600;
        }
        .footer-link a:hover { text-decoration: underline; }

        /* Two-col row for password fields */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }
        @media (max-width: 440px) {
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="card">

        <!-- Logo -->
        <div class="logo">
            <div class="logo-lockup">
                Cup
                <svg class="lime" width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 8h1a4 4 0 0 1 0 8h-1"/>
                    <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/>
                    <line x1="6" y1="1" x2="6" y2="4"/>
                    <line x1="10" y1="1" x2="10" y2="4"/>
                    <line x1="14" y1="1" x2="14" y2="4"/>
                </svg>
                Tea
            </div>
            <p class="logo-tagline">Sip on the facts.</p>
        </div>

        <h1 class="card-title">Create Account</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       placeholder="Your name" required autofocus autocomplete="name" />
                @error('name')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       placeholder="you@email.com" required autocomplete="username" />
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password + Confirm side by side -->
            <div class="field-row">
                <div class="field" style="margin-bottom:0">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password"
                           placeholder="••••••••" required autocomplete="new-password" />
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                    <p class="hint">Min. 8 characters</p>
                </div>

                <div class="field" style="margin-bottom:0">
                    <label for="password_confirmation">Confirm</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           placeholder="••••••••" required autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <p class="footer-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</body>
</html>