<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} — Welcome</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|jetbrains-mono:400,500&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f1419;
            --ink-soft: #3d4554;
            --muted: #6b7280;
            --surface: #ffffff;
            --surface-glass: rgba(255, 255, 255, 0.72);
            --line: rgba(15, 20, 25, 0.08);
            --shadow: 0 24px 48px -12px rgba(15, 20, 25, 0.12), 0 12px 24px -8px rgba(15, 20, 25, 0.06);
            --shadow-sm: 0 4px 14px rgba(15, 20, 25, 0.06);
            --gold: #b8860b;
            --gold-soft: #c9a227;
            --radius: 20px;
            --radius-sm: 12px;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Outfit, ui-sans-serif, system-ui, sans-serif;
            color: var(--ink);
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            background:
                radial-gradient(1200px 600px at 85% -10%, rgba(201, 162, 39, 0.09), transparent 55%),
                radial-gradient(800px 400px at 0% 100%, rgba(15, 20, 25, 0.04), transparent 50%),
                linear-gradient(165deg, #faf9f7 0%, #f0f2f6 50%, #eceef3 100%);
        }
        .page {
            position: relative;
            overflow-x: hidden;
        }
        .container {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 1.25rem 4rem;
        }
        /* Header */
        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 0 2.5rem;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
            color: var(--ink);
        }
        .logo-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(145deg, var(--ink) 0%, #2a3340 100%);
            display: grid;
            place-items: center;
            box-shadow: var(--shadow-sm);
        }
        .logo-mark svg { width: 20px; height: 20px; }
        nav { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.15rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 999px;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .btn:active { transform: scale(0.98); }
        .btn-solid {
            background: var(--ink);
            color: #fff;
            box-shadow: 0 4px 14px rgba(15, 20, 25, 0.2);
        }
        .btn-solid:hover {
            background: #1a222d;
            box-shadow: 0 8px 22px rgba(15, 20, 25, 0.25);
        }
        .btn-ghost {
            background: transparent;
            color: var(--ink-soft);
            border: 1px solid var(--line);
        }
        .btn-ghost:hover {
            background: var(--surface);
            border-color: rgba(15, 20, 25, 0.12);
            color: var(--ink);
        }
        /* Hero */
        .hero {
            display: grid;
            gap: 2.5rem;
            align-items: center;
            padding-bottom: 3rem;
        }
        @media (min-width: 900px) {
            .hero { grid-template-columns: 1fr 1fr; gap: 3rem; padding-bottom: 3.5rem; }
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold-soft);
            margin-bottom: 1rem;
        }
        .eyebrow::before {
            content: "";
            width: 22px;
            height: 2px;
            background: linear-gradient(90deg, var(--gold-soft), transparent);
            border-radius: 2px;
        }
        h1 {
            font-size: clamp(2rem, 4.5vw, 2.85rem);
            font-weight: 700;
            letter-spacing: -0.035em;
            line-height: 1.1;
            margin: 0 0 1rem;
            color: var(--ink);
        }
        h1 span {
            background: linear-gradient(120deg, var(--ink) 0%, #4a5568 40%, var(--gold-soft) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .lede {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 38ch;
            margin: 0 0 1.5rem;
            font-weight: 400;
        }
        .hero-cta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }
        .trust {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--line);
        }
        .trust-item {
            font-size: 0.8rem;
            color: var(--muted);
        }
        .trust-item strong {
            display: block;
            font-size: 0.9rem;
            color: var(--ink);
            font-weight: 600;
            margin-bottom: 0.15rem;
        }
        /* Watch dial decoration */
        .dial-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 280px;
        }
        @media (min-width: 900px) {
            .dial-wrap { min-height: 360px; justify-content: flex-end; }
        }
        .dial {
            position: relative;
            width: min(72vw, 320px);
            height: min(72vw, 320px);
            border-radius: 50%;
            background: linear-gradient(160deg, #fff 0%, #f4f5f8 100%);
            box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.8);
        }
        .dial::after {
            content: "";
            position: absolute;
            inset: 18%;
            border-radius: 50%;
            border: 1px solid var(--line);
            opacity: 0.7;
        }
        .dial-inner {
            position: absolute;
            inset: 0;
            border-radius: 50%;
        }
        .dial-ticks {
            position: absolute;
            inset: 6%;
            border-radius: 50%;
        }
        .dial-hands {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
        }
        .hand {
            position: absolute;
            width: 4px;
            background: var(--ink);
            border-radius: 4px;
            transform-origin: bottom center;
            bottom: 50%;
            left: calc(50% - 2px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        .hand-hour { height: 22%; transform: rotate(-60deg); }
        .hand-min { height: 32%; width: 3px; left: calc(50% - 1.5px); transform: rotate(42deg); background: linear-gradient(180deg, #2a3340, var(--ink)); }
        .dial-cap {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--gold-soft);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 0 3px #fff, 0 2px 8px rgba(0,0,0,0.12);
            z-index: 2;
        }
        .dial-brand {
            position: absolute;
            bottom: 28%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.55rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--muted);
        }
        /* Cards section */
        .section-title {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin: 0 0 1rem;
        }
        .cards {
            display: grid;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .cards { grid-template-columns: repeat(2, 1fr); }
        }
        .card {
            background: var(--surface-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: var(--radius);
            padding: 1.5rem 1.6rem;
            box-shadow: var(--shadow-sm);
        }
        .card h2 {
            margin: 0 0 0.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--ink);
        }
        .card p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--muted);
        }
        .card p + p { margin-top: 0.65rem; }
        .card a {
            color: var(--ink);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px solid rgba(15, 20, 25, 0.15);
        }
        .card a:hover { border-bottom-color: var(--gold-soft); color: var(--gold-soft); }
        code, .mono-block {
            font-family: "JetBrains Mono", ui-monospace, monospace;
            font-size: 0.75rem;
            background: rgba(15, 20, 25, 0.06);
            color: var(--ink-soft);
            padding: 0.2rem 0.45rem;
            border-radius: 6px;
        }
        .mono-block {
            display: block;
            margin-top: 0.75rem;
            padding: 0.65rem 0.75rem;
            word-break: break-all;
            line-height: 1.45;
        }
        .cred {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--line);
            font-size: 0.85rem;
            color: var(--muted);
        }
        .cred strong { color: var(--ink); font-weight: 600; }
        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--line);
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1.25rem;
            justify-content: space-between;
            align-items: center;
        }

        .site-footer {
            margin-top: 3.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--line);
        }
        .site-footer-grid {
            display: grid;
            gap: 1.75rem;
            align-items: start;
        }
        @media (min-width: 900px) {
            .site-footer-grid { grid-template-columns: 1.2fr 1fr 1fr; }
        }
        .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255,255,255,0.9);
            box-shadow: var(--shadow-sm);
        }
        .footer-logo img { display: block; height: 34px; width: auto; }
        .footer-tagline {
            margin: 0.9rem 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .footer-title {
            margin: 0 0 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ink);
        }
        .footer-item {
            display: flex;
            gap: 0.65rem;
            align-items: flex-start;
            color: var(--ink-soft);
            font-size: 0.9rem;
            margin: 0.55rem 0;
        }
        .footer-item svg { flex: 0 0 auto; opacity: 0.9; margin-top: 0.1rem; }
        .footer-item a { color: var(--ink); font-weight: 600; text-decoration: none; border-bottom: 1px solid rgba(15, 20, 25, 0.15); }
        .footer-item a:hover { border-bottom-color: var(--gold-soft); color: var(--gold-soft); }

        .social {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 0.9rem;
        }
        .social a {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.7);
            border: 1px solid rgba(255,255,255,0.9);
            box-shadow: var(--shadow-sm);
            color: var(--ink);
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .social a:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(15,20,25,0.08); color: var(--gold-soft); }
        .social svg { width: 16px; height: 16px; }
    </style>
</head>
<body>
<div class="page">
    <div class="container">
        <header class="top">
            <div class="logo">
                <div class="logo-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="9" stroke="#c9a227" stroke-width="1.5"/>
                        <path d="M12 7v5l3 2" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                Vantier
            </div>
            @if (Route::has('admin.login'))
                <nav>
                    <span class="btn btn-ghost" style="pointer-events:none;cursor:default;">Est. {{ date('Y') }}</span>
                    @auth
                        <a class="btn btn-solid" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    @else
                        <a class="btn btn-solid" href="{{ route('admin.login') }}">Staff login</a>
                    @endauth
                </nav>
            @endif
        </header>

        <main>
            <div class="hero">
                <div>
                    <p class="eyebrow">Timepieces &amp; accessories</p>
                    <h1>Crafted for <span>those who value every second.</span></h1>
                    <p class="lede">
                        Your watch boutique backend is running. Manage collections, inventory, and orders from the admin suite—or wire the storefront to the customer API.
                    </p>
                    @if (Route::has('admin.login'))
                        <div class="hero-cta">
                            @auth
                                <a class="btn btn-solid" href="{{ route('admin.dashboard') }}">Open dashboard</a>
                            @else
                                <a class="btn btn-solid" href="{{ route('admin.login') }}">Open admin</a>
                            @endauth
                            <a class="btn btn-ghost" href="#store-access">API &amp; seeded logins</a>
                        </div>
                    @endif
                    <div class="trust">
                        <div class="trust-item">
                            <strong>Steel &amp; leather</strong>
                            Straps, cases, winders
                        </div>
                        <div class="trust-item">
                            <strong>Curated SKUs</strong>
                            Variants &amp; inventory ready
                        </div>
                        <div class="trust-item">
                            <strong>Orders &amp; fulfilment</strong>
                            Built into this stack
                        </div>
                    </div>
                </div>
                <div class="dial-wrap" aria-hidden="true">
                    <div class="dial">
                        <svg class="dial-ticks" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            @for ($i = 0; $i < 60; $i++)
                                <line
                                    x1="50" y1="4" x2="50" y2="{{ 4 + ($i % 5 === 0 ? 4 : 2) }}"
                                    stroke="{{ $i % 5 === 0 ? '#0f1419' : '#c5cad4' }}"
                                    stroke-width="{{ $i % 5 === 0 ? 1.2 : 0.6 }}"
                                    stroke-linecap="round"
                                    transform="rotate({{ $i * 6 }} 50 50)"
                                />
                            @endfor
                        </svg>
                        <div class="dial-hands">
                            <div class="hand hand-hour"></div>
                            <div class="hand hand-min"></div>
                            <div class="dial-cap"></div>
                        </div>
                        <div class="dial-brand">{{ strtoupper(substr(config('app.name', 'House'), 0, 12)) }}</div>
                    </div>
                </div>
            </div>

            @if (Route::has('admin.login'))
                <p id="store-access" class="section-title">Local development access</p>
                <div class="cards">
                    <div class="card">
                        <h2>Store admin</h2>
                        <p>Sign in at <a href="{{ route('admin.login') }}">/admin/login</a>, then continue to the <a href="{{ route('admin.dashboard') }}">dashboard</a> to run the shop.</p>
                        <div class="cred">
                            <strong>Seeded user</strong><br>
                            <code>admin@store.com</code> · password <code>password</code>
                        </div>
                    </div>
                    <div class="card">
                        <h2>Customer API</h2>
                        <p>There is no public customer web login in this project. Apps should authenticate with JSON against the login endpoint below (after seeding, customer emails use the same password).</p>
                        <span class="mono-block">POST {{ url('/api/v1/auth/login') }}</span>
                        <div class="cred">
                            Example body: <code>{"email":"ahmed.khan@example.com","password":"password"}</code>
                        </div>
                    </div>
                </div>
            @endif
        </main>

        <section class="site-footer" aria-label="Store information">
            <div class="site-footer-grid">
                <div>
                    <div class="footer-logo">
                        <img
                            src="https://thevantier.com/cdn/shop/files/Vantier_Logo_Horizontal_Whitebg_1_1_ffc68b92-1999-43ea-b9a9-1a03d9b4bcbd.png?v=1771514672&width=720"
                            alt="Vantier"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                    <p class="footer-tagline">Created with passion, crafted by hand.</p>
                </div>

                <div>
                    <div class="footer-title">Store info</div>
                    <div class="footer-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M 12 12 C 14.210938 12 16 10.210938 16 8 C 16 5.789062 14.210938 4 12 4 C 9.789062 4 8 5.789062 8 8 C 8 10.210938 9.789062 12 12 12 Z M 12 6 C 13.105469 6 14 6.894531 14 8 C 14 9.105469 13.105469 10 12 10 C 10.894531 10 10 9.105469 10 8 C 10 6.894531 10.894531 6 12 6 Z M 16 22.03125 L 24 23.976562 L 24 13.484375 C 24 12.160156 23.132812 10.996094 21.867188 10.613281 L 19.765625 9.910156 C 19.921875 9.289062 20 8.648438 20 8.007812 C 20 3.585938 16.417969 0.0078125 12 0.0078125 C 7.582031 0.0078125 4 3.585938 4 8.007812 C 4.003906 8.410156 4.035156 8.816406 4.101562 9.21875 C 3.179688 8.851562 2.132812 8.964844 1.3125 9.519531 C 0.492188 10.078125 0 11.007812 0 12 L 0 21.753906 L 7.984375 24.03125 Z M 7.757812 3.765625 C 10.101562 1.449219 13.875 1.460938 16.207031 3.789062 C 18.539062 6.117188 18.558594 9.890625 16.25 12.242188 L 12 16.398438 L 7.757812 12.25 C 5.414062 9.90625 5.414062 6.105469 7.757812 3.765625 Z" />
                        </svg>
                        <div>RHHA7558, Riyadh</div>
                    </div>
                    <div class="footer-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M 19 1 L 5 1 C 2.238281 1.003906 0.00390625 3.238281 0 6 L 0 18 C 0.00390625 20.761719 2.238281 22.996094 5 23 L 19 23 C 21.761719 22.996094 23.996094 20.761719 24 18 L 24 6 C 23.996094 3.238281 21.761719 1.003906 19 1 Z M 5 3 L 19 3 C 20.226562 3.003906 21.324219 3.75 21.78125 4.886719 L 14.121094 12.546875 C 12.949219 13.714844 11.050781 13.714844 9.878906 12.546875 L 2.21875 4.886719 C 2.675781 3.75 3.773438 3.003906 5 3 Z M 19 21 L 5 21 C 3.34375 21 2 19.65625 2 18 L 2 7.5 L 8.464844 13.960938 C 10.417969 15.910156 13.582031 15.910156 15.535156 13.960938 L 22 7.5 L 22 18 C 22 19.65625 20.65625 21 19 21 Z" />
                        </svg>
                        <a href="mailto:info@thevantier.com">info@thevantier.com</a>
                    </div>

                    <div class="social" aria-label="Social links">
                        <a href="https://www.facebook.com/thevantier/" target="_blank" rel="noreferrer" aria-label="Facebook">
                            <svg viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M1.7778 3.33336V5.00005H0V7.50004H1.7778V15H5.33333V7.50004H7.6978L8 5.00005H5.33333V3.54169C5.33333 2.86673 5.40445 2.50837 6.51553 2.50837H8V0H5.61781C2.77338 7.16144e-05 1.7778 1.25003 1.7778 3.33336Z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/thevantier" target="_blank" rel="noreferrer" aria-label="Instagram">
                            <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M12 0H2.99999C1.34315 0 0 1.34315 0 2.99999V12C0 13.6569 1.34315 15 2.99999 15H12C13.6569 15 15 13.6569 15 12V3.00005C15.0001 1.34315 13.6569 0 12 0ZM10.5 2.25001H12.75V4.50001H10.5V2.25001ZM7.5 4.50008C9.15684 4.50008 10.5 5.84322 10.5 7.50006C10.5 9.15691 9.15684 10.5001 7.5 10.5001C5.84316 10.5001 4.50001 9.15691 4.50001 7.50006C4.50001 5.84322 5.84316 4.50008 7.5 4.50008Z"/></svg>
                        </a>
                        <a href="https://x.com/Thevantier" target="_blank" rel="noreferrer" aria-label="X">
                            <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M4.89795 0H0.0824736L5.66219 8.28512L0 15H1.98459L6.54332 9.59344L10.1845 15H15L9.22049 6.4184L14.6323 0H12.6478L8.33923 5.1099L4.89795 0Z"/></svg>
                        </a>
                        <a href="https://www.tiktok.com/@thevantier" target="_blank" rel="noreferrer" aria-label="TikTok">
                            <svg viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M5.07645 15C3.73009 15 2.43888 14.4957 1.48686 13.5981C0.534838 12.7005 0 11.4831 0 10.2136C0 8.94421 0.534838 7.72678 1.48686 6.82917C2.43888 5.93155 3.73009 5.42727 5.07645 5.42727H6.31591V7.76318H5.07645C4.56156 7.76318 4.05824 7.90714 3.63013 8.17685C3.20202 8.44656 2.86834 8.8299 2.67131 9.27841Z"/></svg>
                        </a>
                        <a href="https://api.whatsapp.com/send/?phone=%2B966534563122&text=Hi%2C+I+would+like+to+inqure+about&type=phone_number&app_absent=0" target="_blank" rel="noreferrer" aria-label="WhatsApp">
                            <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M12.9991 2.17987C11.589 0.774902 9.71375 0.000823973 7.71594 0C3.59926 0 0.248873 3.33426 0.247217 7.43234C0.246666 8.74237 0.590535 10.0212 1.24414 11.1484L0.18457 15L4.14385 13.9664C5.2348 14.5586 6.463 14.8707 7.7129 14.8711Z"/></svg>
                        </a>
                        <a href="https://www.snapchat.com/@thevantier" target="_blank" rel="noreferrer" aria-label="Snapchat">
                            <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M15.6864 11.201C13.5473 10.849 12.5832 8.658 12.5442 8.565C12.5412 8.559 12.5392 8.553 12.5352 8.547Z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <div class="footer-title">Quick links</div>
                    <div class="footer-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1a5 5 0 0 1-7 0l-.5-.5 1.4-1.4.5.5a3 3 0 0 0 4.2 0l1-1a3 3 0 1 0-4.2-4.2l-1 1a3 3 0 0 0 0 4.2l.3.3-1.4 1.4-.3-.3Z"/></svg>
                        <a href="{{ url('/admin/login') }}">Admin login</a>
                    </div>
                    <div class="footer-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 4h16v2H4V4Zm0 14h16v2H4v-2Zm0-7h16v2H4v-2Z"/></svg>
                        <a href="{{ url('/api/v1/health') }}">API docs / health</a>
                    </div>
                    <div class="footer-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2Zm1 14.5h-2V10h2v6.5ZM12 8.5a1.25 1.25 0 1 1 0-2.5a1.25 1.25 0 0 1 0 2.5Z"/></svg>
                        <span>Vantier — premium timepieces &amp; accessories.</span>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <span>Vantier · Laravel {{ Illuminate\Foundation\Application::VERSION }} · PHP {{ PHP_VERSION }}</span>
        </footer>
    </div>
</div>
</body>
</html>
