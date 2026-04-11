<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SchoolFeed — School Canteen Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #E8300A;
            --red-dark: #B82608;
            --red-light: #FF5030;
            --cream: #FAF8F3;
            --warm-white: #FDFCF9;
            --ink: #1A1510;
            --ink-60: rgba(26,21,16,0.6);
            --ink-20: rgba(26,21,16,0.12);
            --green: #25D366;
            --card-bg: #FFFFFF;
            --border: rgba(26,21,16,0.1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── NOISE TEXTURE OVERLAY ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* ── NAV ── */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(250,248,243,0.88);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
        }

        .nav-inner {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
        }

        .logo-mark {
            width: 36px;
            height: 36px;
            background: var(--red);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Mono', monospace;
            font-weight: 500;
            font-size: 13px;
            color: white;
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }

        .logo-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--ink);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--ink-60);
            font-size: 0.9rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }

        .nav-links a:hover {
            color: var(--ink);
            background: var(--ink-20);
        }

        .btn-nav-primary {
            background: var(--ink) !important;
            color: white !important;
            padding: 8px 18px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            transition: background 0.2s !important;
        }

        .btn-nav-primary:hover {
            background: var(--red) !important;
            color: white !important;
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            max-width: 1180px;
            margin: 0 auto;
            padding: 5rem 2rem 4rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            z-index: 1;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232,48,10,0.08);
            border: 1px solid rgba(232,48,10,0.2);
            color: var(--red);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 1.5rem;
        }

        .hero-eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--red);
            border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: clamp(2.4rem, 4vw, 3.5rem);
            line-height: 1.1;
            color: var(--ink);
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
        }

        .hero-title span {
            color: var(--red);
            position: relative;
            display: inline-block;
        }

        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--red);
            border-radius: 2px;
            opacity: 0.3;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--ink-60);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .hero-cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .btn-primary {
            background: var(--red);
            color: white;
            text-decoration: none;
            padding: 13px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(232,48,10,0.35);
        }

        .btn-primary:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232,48,10,0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--ink);
            text-decoration: none;
            padding: 13px 28px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1.5px solid var(--border);
            transition: border-color 0.2s, transform 0.15s;
        }

        .btn-secondary:hover {
            border-color: var(--ink-60);
            transform: translateY(-1px);
        }

        /* ── LOGIN BOX ── */
        .login-box {
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 2px 16px rgba(26,21,16,0.06);
        }

        .login-box-label {
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ink-60);
            margin-bottom: 0.75rem;
        }

        .login-input-row {
            display: flex;
            gap: 8px;
        }

        .login-input-row input {
            flex: 1;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            background: var(--cream);
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s;
        }

        .login-input-row input:focus {
            border-color: var(--red);
        }

        .login-input-row input::placeholder {
            color: var(--ink-60);
            font-family: 'DM Mono', monospace;
            font-size: 0.8rem;
        }

        .login-input-row button {
            background: var(--ink);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .login-input-row button:hover {
            background: var(--red);
        }

        /* ── HERO RIGHT: DASHBOARD MOCKUP ── */
        .hero-visual {
            position: relative;
        }

        .dashboard-card {
            background: white;
            border-radius: 18px;
            border: 1.5px solid var(--border);
            box-shadow: 0 20px 60px rgba(26,21,16,0.12), 0 4px 16px rgba(26,21,16,0.06);
            overflow: hidden;
            transform: perspective(1000px) rotateY(-4deg) rotateX(2deg);
            transition: transform 0.4s ease;
        }

        .dashboard-card:hover {
            transform: perspective(1000px) rotateY(-1deg) rotateX(0deg);
        }

        .dash-header {
            background: var(--ink);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot-r { background: #FF5F57; }
        .dot-y { background: #FEBC2E; }
        .dot-g { background: #28C840; }

        .dash-title-bar {
            flex: 1;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
            height: 22px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
        }

        .dash-body { padding: 20px; }

        .dash-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .stat-tile {
            background: var(--cream);
            border-radius: 10px;
            padding: 12px;
        }

        .stat-label {
            font-size: 0.67rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ink-60);
            margin-bottom: 4px;
        }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ink);
        }

        .stat-sub {
            font-size: 0.7rem;
            color: var(--ink-60);
            margin-top: 2px;
        }

        .stat-tile.red { background: rgba(232,48,10,0.07); }
        .stat-tile.red .stat-num { color: var(--red); }

        .dash-row-title {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ink-60);
            margin-bottom: 8px;
        }

        .dash-roster {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .roster-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--cream);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .roster-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .roster-avatar.green { background: #16A34A; }
        .roster-avatar.orange { background: #EA580C; }
        .roster-avatar.blue { background: #2563EB; }

        .roster-name { font-size: 0.82rem; font-weight: 500; flex: 1; }
        .roster-class { font-size: 0.75rem; color: var(--ink-60); }

        .badge {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .badge-paid { background: rgba(22,163,74,0.12); color: #16A34A; }
        .badge-pending { background: rgba(234,88,12,0.12); color: #EA580C; }

        /* ── FEATURES SECTION ── */
        .section {
            max-width: 1180px;
            margin: 0 auto;
            padding: 5rem 2rem;
            position: relative;
            z-index: 1;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-bottom: 1rem;
            max-width: 560px;
        }

        .section-sub {
            font-size: 1rem;
            color: var(--ink-60);
            line-height: 1.7;
            max-width: 500px;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .feature-card {
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 1.75rem;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--red);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .feature-card:hover {
            border-color: rgba(232,48,10,0.25);
            box-shadow: 0 8px 30px rgba(26,21,16,0.09);
            transform: translateY(-2px);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(232,48,10,0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .feature-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            font-size: 0.88rem;
            color: var(--ink-60);
            line-height: 1.65;
        }

        /* ── HOW IT WORKS ── */
        .how-section {
            background: var(--ink);
            color: white;
            padding: 5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .how-section::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(232,48,10,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .how-inner {
            max-width: 1180px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .step {
            position: relative;
        }

        .step-num {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: rgba(255,255,255,0.08);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .step-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .step-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.65;
        }

        .step-connector {
            position: absolute;
            top: 1.6rem;
            right: -1rem;
            width: calc(100% - 1rem);
            height: 1px;
            background: linear-gradient(90deg, rgba(232,48,10,0.5), rgba(232,48,10,0.1));
        }

        .step:last-child .step-connector { display: none; }

        /* ── DIVIDER ── */
        .divider {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .divider hr {
            border: none;
            border-top: 1px solid var(--border);
        }

        /* ── CTA SECTION ── */
        .cta-section {
            max-width: 1180px;
            margin: 0 auto;
            padding: 5rem 2rem;
            text-align: center;
            z-index: 1;
            position: relative;
        }

        .cta-card {
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 24px;
            padding: 4rem 2rem;
            box-shadow: 0 4px 30px rgba(26,21,16,0.07);
            position: relative;
            overflow: hidden;
        }

        .cta-card::before {
            content: '';
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(232,48,10,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: clamp(2rem, 3vw, 2.75rem);
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .cta-sub {
            font-size: 1rem;
            color: var(--ink-60);
            line-height: 1.7;
            max-width: 480px;
            margin: 0 auto 2.5rem;
            position: relative;
            z-index: 1;
        }

        .cta-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        /* ── FOOTER ── */
        footer {
            border-top: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
            font-size: 0.82rem;
            color: var(--ink-60);
            position: relative;
            z-index: 1;
        }

        footer a {
            color: var(--ink-60);
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover { color: var(--ink); }

        /* ── WHATSAPP ── */
        .whatsapp-btn {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 200;
            width: 56px;
            height: 56px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(37,211,102,0.4);
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .whatsapp-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 28px rgba(37,211,102,0.5);
        }

        .whatsapp-btn svg { width: 28px; height: 28px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; gap: 2.5rem; }
            .dashboard-card { transform: none !important; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr 1fr; }
            .nav-links a:not(.btn-nav-primary) { display: none; }
        }

        @media (max-width: 600px) {
            nav { padding: 0 1rem; }
            .hero, .section { padding: 3rem 1rem; }
            .steps-grid { grid-template-columns: 1fr; }
            .cta-card { padding: 3rem 1.5rem; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .anim { opacity: 0; animation: fade-up 0.7s ease forwards; }
        .anim-1 { animation-delay: 0.1s; }
        .anim-2 { animation-delay: 0.22s; }
        .anim-3 { animation-delay: 0.34s; }
        .anim-4 { animation-delay: 0.46s; }
        .anim-5 { animation-delay: 0.58s; }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="/" class="logo">
            <div class="logo-mark">SF</div>
            <span class="logo-name">SchoolFeed</span>
        </a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How it works</a>
            <a href="#school-login" class="btn-nav-primary">School Login</a>
            <a href="/register-school" class="btn-nav-primary" style="background: var(--red) !important; margin-left:4px;">Register School</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section style="position:relative; z-index:1;">
    <div class="hero">
        <div>
            <div class="hero-eyebrow anim anim-1">Purpose-built for Schools</div>
            <h1 class="hero-title anim anim-2">
                School Canteen<br>
                <span>Made Simple.</span>
            </h1>
            <p class="hero-subtitle anim anim-3">
                Streamline meal planning, attendance, payments, inventory, and reporting — all in one platform built for school canteen operators.
            </p>
            <div class="hero-cta anim anim-4">
                <a href="/register-school" class="btn-primary">Register your school</a>
                <a href="#features" class="btn-secondary">Explore features →</a>
            </div>
            <div id="school-login" class="login-box anim anim-5">
                <div class="login-box-label">School Login</div>
                <div class="login-input-row">
                    <input id="schoolSlugInput" type="text" placeholder="your-school-slug">
                    <button type="button" onclick="(function(){var v=document.getElementById('schoolSlugInput').value.trim(); if(v){ window.location.href='/' + encodeURIComponent(v) + '/login'; }})()">Login →</button>
                </div>
            </div>
        </div>

        <!-- DASHBOARD MOCKUP -->
        <div class="hero-visual anim anim-3">
            <div class="dashboard-card">
                <div class="dash-header">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                    <div class="dash-title-bar">schoolfeed.app/the-citadel/dashboard</div>
                </div>
                <div class="dash-body">
                    <div class="dash-stats">
                        <div class="stat-tile red">
                            <div class="stat-label">Fed Today</div>
                            <div class="stat-num">247</div>
                            <div class="stat-sub">↑ 12 from yesterday</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-label">Revenue</div>
                            <div class="stat-num">GHS84k</div>
                            <div class="stat-sub">This week</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-label">Pending</div>
                            <div class="stat-num">18</div>
                            <div class="stat-sub">Unpaid meals</div>
                        </div>
                    </div>
                    <div class="dash-row-title">Today's Roster</div>
                    <div class="dash-roster">
                        <div class="roster-row">
                            <div class="roster-avatar green">AO</div>
                            <div>
                                <div class="roster-name">Abdul-Fatahi Mohammed</div>
                                <div class="roster-class">Basic 1</div>
                            </div>
                            <span class="badge badge-paid">Paid</span>
                        </div>
                        <div class="roster-row">
                            <div class="roster-avatar blue">EI</div>
                            <div>
                                <div class="roster-name">Abdul Wadud</div>
                                <div class="roster-class">JHS 1</div>
                            </div>
                            <span class="badge badge-paid">Paid</span>
                        </div>
                        <div class="roster-row">
                            <div class="roster-avatar orange">TN</div>
                            <div>
                                <div class="roster-name">Rashid Radia</div>
                                <div class="roster-class">KG 1</div>
                            </div>
                            <span class="badge badge-pending">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="section-label">Features</div>
    <h2 class="section-title">Everything your canteen needs to run smoothly</h2>
    <p class="section-sub">From the first meal of the term to end-of-month reporting, SchoolFeed handles every step of your canteen operations.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🗓️</div>
            <div class="feature-title">Daily Roster & Attendance</div>
            <p class="feature-desc">Track who paid and who was fed each day. Filter by class, date, or meal plan. Export to PDF or Excel with one click.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <div class="feature-title">Inventory & Kitchen</div>
            <p class="feature-desc">Manage stock in/out, kitchen usage, recipes, and low-stock alerts. Know your costs before you cook.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <div class="feature-title">Payments & Feeding Plans</div>
            <p class="feature-desc">Flexible weekly or termly feeding plans. Accept online payments, issue receipts, and track balances per student.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <div class="feature-title">Reports & Insights</div>
            <p class="feature-desc">Powerful daily and monthly usage reports. Understand your cost per meal, profit margins, and busiest meal days.</p>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<div class="how-section" id="how-it-works">
    <div class="how-inner">
        <div class="section-label" style="color: rgba(255,255,255,0.5);">How it works</div>
        <h2 class="section-title" style="color:white; max-width:none;">Up and running in four steps</h2>
        <div class="steps-grid">
            <div class="step">
                <div class="step-connector"></div>
                <div class="step-num">01</div>
                <div class="step-title">Register your school</div>
                <p class="step-desc">Provide your school's basic details and submit for approval.</p>
            </div>
            <div class="step">
                <div class="step-connector"></div>
                <div class="step-num">02</div>
                <div class="step-title">Account approval</div>
                <p class="step-desc">Our Super Admin reviews and approves your school account.</p>
            </div>
            <div class="step">
                <div class="step-connector"></div>
                <div class="step-num">03</div>
                <div class="step-title">Login at your URL</div>
                <p class="step-desc">Access your dashboard at <code style="font-family:'DM Mono',monospace; font-size:0.8rem; color:rgba(255,80,48,0.9);">yourapp.com/{school_slug}</code></p>
            </div>
            <div class="step">
                <div class="step-num">04</div>
                <div class="step-title">Start operations</div>
                <p class="step-desc">Configure meals, classes, and inventory — and start feeding.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-card">
        <h2 class="cta-title">Ready to modernise your<br>school canteen?</h2>
        <p class="cta-sub">Join schools across Nigeria using SchoolFeed to save time, reduce waste, and keep parents informed.</p>
        <div class="cta-actions">
            <a href="/register-school" class="btn-primary">Register your school — it's free</a>
            <a href="https://wa.me/2348012345678?text=Hello%20SchoolFeed%20Team%2C%20I%27d%20like%20to%20learn%20more." target="_blank" rel="noopener" class="btn-secondary">Chat with us on WhatsApp</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p style="margin-bottom:0.5rem;">
        <a href="/">SchoolFeed</a> &nbsp;·&nbsp;
        <a href="#features">Features</a> &nbsp;·&nbsp;
        <a href="#how-it-works">How it works</a> &nbsp;·&nbsp;
        <a href="/register-school">Register</a>
    </p>
    <p>© 2026 SchoolFeed. All rights reserved.</p>
</footer>

<!-- WHATSAPP FAB -->
<a href="https://wa.me/233246166303?text=Hello%20SchoolFeed%20Team%2C%20I%27d%20like%20to%20learn%20more." target="_blank" rel="noopener" class="whatsapp-btn" title="Chat on WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="white">
        <path d="M19.11 17.07c-.28-.14-1.62-.8-1.87-.9-.25-.09-.43-.14-.62.14-.19.28-.71.9-.86 1.08-.16.19-.32.21-.6.07-.28-.14-1.16-.43-2.2-1.36-.81-.72-1.36-1.6-1.52-1.87-.16-.28-.02-.43.12-.57.12-.12.28-.32.43-.47.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.5-.07-.14-.62-1.5-.85-2.06-.22-.53-.45-.46-.62-.46h-.53c-.19 0-.5.07-.76.35-.26.28-1 1-1 2.43s1.02 2.82 1.16 3.01c.14.19 2.01 3.07 4.87 4.31.68.29 1.21.46 1.62.59.68.22 1.31.19 1.8.12.55-.08 1.62-.66 1.85-1.3.23-.64.23-1.19.16-1.3-.07-.11-.25-.18-.53-.32z"/>
        <path d="M26.67 5.33C23.73 2.38 19.97.8 16 .8 8.58.8 2.8 6.58 2.8 14c0 2.5.67 4.94 1.95 7.08L2 30l9.12-2.7c2.07 1.13 4.41 1.73 6.88 1.73 7.42 0 13.2-5.78 13.2-13.2 0-3.97-1.58-7.73-4.53-10.67zM18.99 26.2c-2.22 0-4.38-.6-6.26-1.74l-.45-.27-5.41 1.6 1.6-5.28-.29-.47C7 17.77 6.4 15.74 6.4 14c0-5.29 4.31-9.6 9.6-9.6 2.56 0 4.96.99 6.77 2.8 1.81 1.81 2.8 4.21 2.8 6.77 0 5.29-4.31 9.6-9.6 9.6z"/>
    </svg>
</a>

</body>
</html>