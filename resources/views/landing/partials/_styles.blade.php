<style>
    :root {
        /* Brand palette: red, green, black (+ neutrals). DB-driven with safe fallbacks. */
        --brand-red: #cc1f2b;
        --brand-red-dark: #9b0c23;
        --brand-green: #23803a;
        --brand-green-dark: #19662c;
        --brand-green-deep: #054d1f;
        --brand-black: #111111;

        --ink: #141414;
        --ink-soft: #4a4a4a;
        --ink-muted: #6b6b6b;
        --line: #e8e8e8;
        --surface: #ffffff;
        --surface-alt: #f6f6f4;

        --gradient-red: linear-gradient(135deg, var(--brand-red), var(--brand-red-dark));
        --gradient-green: linear-gradient(135deg, var(--brand-green-dark), var(--brand-green-deep));
        --gradient-accent: linear-gradient(90deg, var(--brand-red), var(--brand-green));

        --radius-lg: 20px;
        --radius-md: 14px;
        --shadow-sm: 0 1px 2px rgba(17,17,17,0.04), 0 4px 16px rgba(17,17,17,0.05);
        --shadow-lg: 0 2px 6px rgba(17,17,17,0.05), 0 18px 40px rgba(17,17,17,0.10);
        --ease: cubic-bezier(.2,.7,.2,1);

        /* Keep Bootstrap defaults off-brand-blue */
        --bs-link-color-rgb: 155, 12, 35;
        --bs-link-hover-color-rgb: 200, 16, 46;
        --bs-focus-ring-color: rgba(9, 145, 57, 0.3);
    }

    html { scroll-behavior: smooth; scroll-padding-top: 84px; }

    body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        background-color: var(--surface);
        color: var(--ink);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 { color: var(--ink); letter-spacing: -0.02em; }

    ::selection { background: var(--brand-red); color: #fff; }
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #b5b5b5; border-radius: 10px; border: 2px solid #f1f1f1; }
    ::-webkit-scrollbar-thumb:hover { background: var(--brand-red); }

    a:focus-visible, .btn:focus-visible, button:focus-visible {
        outline: 3px solid var(--brand-green);
        outline-offset: 3px;
        box-shadow: none;
    }

    /* ---------- Buttons ---------- */
    .btn-brand, .btn-accent, .btn-ghost-light {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        font-weight: 600; border-radius: 999px; padding: .8rem 1.6rem;
        transition: transform .25s var(--ease), box-shadow .25s var(--ease), background-color .25s, color .25s, border-color .25s;
    }
    .btn-brand { background: var(--gradient-red); color: #fff; border: 0; }
    .btn-brand:hover, .btn-brand:focus { color: #fff; transform: translateY(-2px); box-shadow: 0 10px 24px rgba(200,16,46,0.30); }
    .btn-accent { background: var(--brand-green-dark); color: #fff; border: 0; }
    .btn-accent:hover, .btn-accent:focus { background: var(--brand-green-deep); color: #fff; transform: translateY(-2px); box-shadow: 0 10px 24px rgba(5,77,31,0.35); }
    .btn-ghost-light { background: rgba(255,255,255,0.06); color: #fff; border: 1px solid rgba(255,255,255,0.45); }
    .btn-ghost-light:hover, .btn-ghost-light:focus { background: #fff; color: var(--brand-black); border-color: #fff; transform: translateY(-2px); }
    .btn-lg-pill { padding: 1rem 2rem; font-size: 1.05rem; }
    .btn-brand i, .btn-accent i, .btn-ghost-light i { transition: transform .25s var(--ease); }
    .btn-brand:hover i.bi-arrow-right, .btn-accent:hover i.bi-arrow-right { transform: translateX(3px); }
    .btn-ghost-light:hover i.bi-arrow-down { transform: translateY(3px); }

    /* ---------- Navbar ---------- */
    #mainNav {
        background: rgba(255,255,255,0.82);
        -webkit-backdrop-filter: saturate(160%) blur(14px);
        backdrop-filter: saturate(160%) blur(14px);
        border-bottom: 1px solid rgba(17,17,17,0.06);
        padding: 14px 0;
        transition: padding .3s var(--ease), background-color .3s, box-shadow .3s;
    }
    #mainNav.navbar-scrolled {
        background: #ffffff;
        padding: 8px 0;
        box-shadow: 0 6px 24px rgba(17,17,17,0.08);
    }
    #mainNav::after {
        content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 2px;
        background: var(--gradient-accent); opacity: 0; transition: opacity .3s;
    }
    #mainNav.navbar-scrolled::after { opacity: 1; }
    .navbar-brand { display: flex; align-items: center; gap: .65rem; font-weight: 800; color: var(--ink) !important; letter-spacing: -0.02em; }
    .navbar-brand img { height: 48px; width: auto; border-radius: 8px; transition: height .3s var(--ease); }
    #mainNav.navbar-scrolled .navbar-brand img { height: 40px; }
    .brand-text { line-height: 1.1; }
    .brand-text small { display: block; font-size: .7rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--brand-red); }
    .brand-text span { font-size: 1.1rem; }

    #mainNav .nav-link {
        position: relative; font-weight: 500; color: var(--ink-soft) !important;
        padding: .5rem .9rem !important; transition: color .2s;
    }
    #mainNav .nav-link:hover, #mainNav .nav-link:focus { color: var(--brand-red) !important; }
    #mainNav .nav-link::after {
        content: ''; position: absolute; height: 2px; left: .9rem; right: .9rem; bottom: .2rem;
        background: var(--gradient-accent); border-radius: 2px;
        transform: scaleX(0); transform-origin: left; transition: transform .3s var(--ease);
    }
    #mainNav .nav-link:hover::after { transform: scaleX(1); }
    .navbar-toggler { border: 1px solid var(--line); border-radius: 10px; padding: .45rem .6rem; }
    .navbar-toggler:focus { box-shadow: 0 0 0 3px rgba(9,145,57,0.25); }

    @media (max-width: 991.98px) {
        #mainNav .navbar-collapse {
            background: #fff; margin-top: .75rem; padding: .75rem; border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
        }
        #mainNav .navbar-nav { align-items: stretch !important; gap: .25rem !important; }
        #mainNav .navbar-nav .btn-brand, #mainNav .navbar-nav .btn-accent { width: 100%; margin-top: .25rem; }
        #mainNav .nav-link::after { display: none; }
    }

    /* ---------- Hero ---------- */
    .hero {
        position: relative; min-height: 100vh; min-height: 100svh;
        display: flex; align-items: center;
        color: #fff; background: var(--brand-black);
        padding: 130px 0 90px; overflow: hidden; isolation: isolate;
    }
    .hero-bg {
        position: absolute; inset: 0; z-index: -2;
        background-image: url('{{ asset('images/bg.jpg') }}');
        background-size: cover; background-position: center;
        filter: grayscale(1) contrast(1.05);
        transform: scale(1.04);
    }
    .hero-overlay {
        position: absolute; inset: 0; z-index: -1;
        background:
            radial-gradient(900px 500px at 12% 18%, rgba(200,16,46,0.30), transparent 60%),
            radial-gradient(800px 500px at 90% 85%, rgba(9,145,57,0.28), transparent 60%),
            linear-gradient(180deg, rgba(10,10,10,0.78) 0%, rgba(10,10,10,0.86) 60%, rgba(10,10,10,0.94) 100%);
    }
    .hero::after {
        content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 4px;
        background: var(--gradient-accent);
    }
    .hero-content { position: relative; z-index: 2; max-width: 880px; margin: 0 auto; }
    .hero-eyebrow {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .45rem 1rem; border-radius: 999px;
        background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18);
        font-size: .82rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
        color: #fff; margin-bottom: 1.75rem;
        -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
    }
    .hero-eyebrow .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--brand-green); box-shadow: 0 0 0 4px rgba(9,145,57,0.25); }
    .hero-title {
        color: #fff; font-weight: 800; letter-spacing: -0.035em; line-height: 1.08;
        font-size: clamp(2.2rem, 5.6vw, 4.25rem); margin-bottom: 1.25rem;
    }
    .hero-subtitle {
        color: rgba(255,255,255,0.86); font-size: clamp(1.02rem, 1.6vw, 1.25rem);
        max-width: 680px; margin: 0 auto 2.25rem; font-weight: 400;
    }
    .hero-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: .85rem; }
    .accent-rule { display: block; width: 72px; height: 4px; border-radius: 4px; background: var(--gradient-accent); margin: 0 auto 1.5rem; }

    #heroCarousel.has-indicators .carousel-inner { padding-bottom: 3.25rem; }
    #heroCarousel .carousel-item { min-height: 1px; }
    #heroCarousel .carousel-indicators { margin-bottom: 0; gap: .4rem; }
    #heroCarousel .carousel-indicators [data-bs-target] {
        width: 26px; height: 4px; border-radius: 4px; border: 0; margin: 0;
        background: #fff; opacity: .35; transition: width .3s var(--ease), opacity .3s;
    }
    #heroCarousel .carousel-indicators .active { width: 44px; opacity: 1; background: #fff; }
    .hero .carousel-control-prev, .hero .carousel-control-next {
        width: 46px; height: 46px; top: 40%; transform: translateY(-50%);
        border-radius: 50%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2);
        opacity: 1; transition: background-color .2s;
    }
    .hero .carousel-control-prev { left: -64px; }
    .hero .carousel-control-next { right: -64px; }
    .hero .carousel-control-prev:hover, .hero .carousel-control-next:hover { background: var(--brand-red); border-color: var(--brand-red); }
    .hero .carousel-control-prev-icon, .hero .carousel-control-next-icon { width: 1.1rem; height: 1.1rem; }

    .hero-trust {
        display: flex; flex-wrap: wrap; justify-content: center; gap: .6rem 1.75rem;
        margin-top: 2.5rem; padding: 0; list-style: none;
        color: rgba(255,255,255,0.8); font-size: .92rem; font-weight: 500;
    }
    .hero-trust i { color: #fff; margin-right: .4rem; }
    .hero-trust li { display: inline-flex; align-items: center; }

    @media (max-width: 1199.98px) {
        .hero .carousel-control-prev, .hero .carousel-control-next { display: none; }
    }

    /* ---------- Sections ---------- */
    .section { padding: 6rem 0; }
    .section-alt { background: var(--surface-alt); }
    .section-head { max-width: 680px; margin: 0 auto 3.5rem; text-align: center; }
    .section-kicker {
        display: inline-block; font-size: .78rem; font-weight: 700; letter-spacing: .14em;
        text-transform: uppercase; color: var(--brand-red); margin-bottom: .75rem;
    }
    .section-title { font-weight: 800; font-size: clamp(1.75rem, 3.4vw, 2.6rem); margin-bottom: 1rem; }
    .section-lead { color: var(--ink-muted); font-size: 1.08rem; margin: 0; }

    /* ---------- Feature cards ---------- */
    .feature-card {
        position: relative; height: 100%;
        background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg);
        padding: 2rem 1.75rem; box-shadow: var(--shadow-sm);
        transition: transform .35s var(--ease), box-shadow .35s var(--ease), border-color .35s;
        overflow: hidden;
    }
    .feature-card::before {
        content: ''; position: absolute; left: 0; right: 0; top: 0; height: 3px;
        background: var(--gradient-accent); transform: scaleX(0); transform-origin: left;
        transition: transform .4s var(--ease);
    }
    .feature-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: transparent; }
    .feature-card:hover::before { transform: scaleX(1); }
    .feature-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.6rem; margin-bottom: 1.4rem;
        background: rgba(200,16,46,0.09); color: var(--brand-red-dark);
        transition: transform .35s var(--ease);
    }
    .feature-card.is-green .feature-icon { background: rgba(9,145,57,0.10); color: var(--brand-green-deep); }
    .feature-card:hover .feature-icon { transform: scale(1.06) rotate(-3deg); }
    .feature-card h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: .6rem; }
    .feature-card p { color: var(--ink-muted); margin: 0; font-size: .97rem; }

    /* ---------- Board cards ---------- */
    .board-card {
        height: 100%; text-align: center;
        background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg);
        padding: 2rem 1.25rem 1.75rem; box-shadow: var(--shadow-sm);
        transition: transform .35s var(--ease), box-shadow .35s var(--ease);
    }
    .board-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }
    .board-avatar-wrap {
        width: 116px; height: 116px; margin: 0 auto 1.25rem; padding: 4px; border-radius: 50%;
        background: conic-gradient(from 210deg, var(--brand-red), var(--brand-black), var(--brand-green), var(--brand-red));
    }
    .board-avatar, .board-avatar-placeholder {
        width: 100%; height: 100%; border-radius: 50%; border: 4px solid #fff; display: block;
    }
    .board-avatar { object-fit: cover; background: #fff; }
    .board-avatar-placeholder {
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; font-weight: 800; color: #fff; background: var(--brand-black);
    }
    .board-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: .25rem; }
    .board-role { display: block; color: var(--brand-red-dark); font-weight: 600; font-size: .88rem; }
    .board-designation { display: block; color: var(--ink-muted); font-size: .82rem; margin-top: .2rem; }

    /* ---------- CTA ---------- */
    .cta-section { padding: 5rem 0; }
    .cta-band {
        position: relative; overflow: hidden; isolation: isolate;
        border-radius: 28px; padding: 4rem 2rem; text-align: center; color: #fff;
        background: var(--gradient-green);
        box-shadow: 0 24px 60px rgba(5,77,31,0.28);
    }
    .cta-band::before {
        content: ''; position: absolute; z-index: -1; width: 520px; height: 520px; border-radius: 50%;
        top: -260px; right: -160px; background: radial-gradient(circle, rgba(255,255,255,0.14), transparent 70%);
    }
    .cta-band::after {
        content: ''; position: absolute; z-index: -1; width: 420px; height: 420px; border-radius: 50%;
        bottom: -240px; left: -140px; background: radial-gradient(circle, rgba(0,0,0,0.25), transparent 70%);
    }
    .cta-band h2 { color: #fff; font-weight: 800; font-size: clamp(1.6rem, 3.2vw, 2.4rem); max-width: 760px; margin: 0 auto 1rem; }
    .cta-band p { color: rgba(255,255,255,0.9); font-size: 1.1rem; max-width: 620px; margin: 0 auto 2rem; }
    .cta-band .btn-light-solid {
        display: inline-flex; align-items: center; gap: .5rem;
        background: #fff; color: var(--brand-green-deep); font-weight: 700; border-radius: 999px; padding: 1rem 2rem; border: 0;
        transition: transform .25s var(--ease), box-shadow .25s var(--ease), color .25s;
    }
    .cta-band .btn-light-solid:hover, .cta-band .btn-light-solid:focus { color: var(--brand-red-dark); transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,0.25); }
    .cta-band .btn-light-solid:focus-visible { outline-color: #fff; }

    /* ---------- Footer ---------- */
    footer.site-footer { position: relative; background: var(--brand-black); color: rgba(255,255,255,0.78); padding: 4.5rem 0 1.75rem; }
    footer.site-footer::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gradient-accent); }
    footer.site-footer h5, footer.site-footer h6 { color: #fff; }
    footer.site-footer h6 { font-size: .8rem; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; margin-bottom: 1.1rem; }
    footer.site-footer a { color: rgba(255,255,255,0.78); text-decoration: none; transition: color .2s; }
    footer.site-footer a:hover, footer.site-footer a:focus { color: #fff; }
    .footer-contact li, .footer-links li { display: flex; align-items: flex-start; gap: .65rem; margin-bottom: .65rem; word-break: break-word; }
    .footer-contact i { color: var(--brand-green); margin-top: .15rem; }
    .footer-links a i { color: var(--brand-red); font-size: .85rem; transition: transform .2s; }
    .footer-links a:hover i { transform: translateX(3px); }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 2.5rem; padding-top: 1.5rem; color: rgba(255,255,255,0.6); font-size: .9rem; }

    /* ---------- Back to top ---------- */
    .back-to-top {
        position: fixed; bottom: 24px; right: 24px; z-index: 999;
        width: 46px; height: 46px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--brand-black); color: #fff; text-decoration: none;
        box-shadow: 0 8px 22px rgba(17,17,17,0.25);
        opacity: 0; visibility: hidden; transform: translateY(10px);
        transition: opacity .3s, visibility .3s, transform .3s var(--ease), background-color .2s;
    }
    .back-to-top.active { opacity: 1; visibility: visible; transform: none; }
    .back-to-top:hover, .back-to-top:focus { background: var(--brand-red); color: #fff; }

    /* ---------- Reveal on scroll (only when JS is available) ---------- */
    .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s var(--ease), transform .7s var(--ease); }
    .js .reveal.is-visible { opacity: 1; transform: none; }

    @media (max-width: 767.98px) {
        .section { padding: 4.25rem 0; }
        .hero { padding: 110px 0 70px; }
        .cta-section { padding: 3.5rem 0; }
        .cta-band { padding: 3rem 1.25rem; border-radius: 22px; }
        .hero-actions .btn-brand, .hero-actions .btn-accent, .hero-actions .btn-ghost-light { width: 100%; }
        .back-to-top { bottom: 16px; right: 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        *, *::before, *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; }
        .js .reveal { opacity: 1; transform: none; }
        .feature-card:hover, .board-card:hover, .btn-brand:hover, .btn-accent:hover, .btn-ghost-light:hover { transform: none; }
    }
</style>
