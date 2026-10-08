@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        $noteColors = [
            'yellow' => '#fff9c4',
            'blue'   => '#dbdbdb',
            'green'  => '#c8e6c9',
            'pink'   => '#f8bbd0',
        ];
        $liveCount     = $activeMeetings->count();
        $scheduledCount = $todaysMeetings->count();
    @endphp

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           Board Portal — Active Meetings (Premium)
           bp-* prefix  |  iPad-optimized  |  44px touch targets
        ═══════════════════════════════════════════════════════════════════ */
        :root {
            --bp-navy: {{ setting('primary_color', '#1a1a1a') }};
            --bp-navy-dark: #9b0c23;
            --bp-navy-light: #e50013;
            --bp-navy-soft: #fdecee;
            --bp-green: #099139;
            --bp-green-dark: #077a2e;
            --bp-green-soft: #e6f5eb;
            --bp-red: #d32f2f;
            --bp-orange: #ff6d00;
            --bp-radius: 16px;
            --bp-shadow: 0 8px 22px rgba(0,0,0,.06);
            --bp-shadow-hover: 0 12px 32px rgba(0,0,0,.12);
        }

        /* ── Base ── */
        .bp-page {
            font-size: 15px;
            -webkit-tap-highlight-color: transparent;
            -webkit-text-size-adjust: 100%;
        }

        /* ══════════ HERO BANNER ══════════ */
        .bp-hero {
            background: linear-gradient(135deg, var(--bp-navy) 0%, var(--bp-navy-light) 100%);
            color: #fff;
            border-radius: var(--bp-radius);
            padding: 32px 36px 28px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(200, 16, 46,.25);
        }
        .bp-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 20h40M20 0v40' stroke='rgba(255,255,255,0.06)' stroke-width='1' fill='none'/%3E%3C/svg%3E");
            background-repeat: repeat;
            pointer-events: none;
        }
        .bp-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -15%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
            pointer-events: none;
        }
        .bp-hero-inner {
            position: relative;
            z-index: 1;
        }
        .bp-breadcrumb a {
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .9rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            transition: color .2s;
        }
        .bp-breadcrumb a:hover { color: #fff; }
        .bp-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(6px);
            border-radius: 999px;
            padding: .4rem 1rem;
            font-size: .85rem;
            font-weight: 600;
            color: #fff;
        }

        /* ══════════ SECTION HEADINGS ══════════ */
        .bp-section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #141414;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .bp-section-title i {
            font-size: 1.3rem;
        }

        /* ══════════ CARDS ══════════ */
        .bp-card {
            border: 0;
            border-radius: var(--bp-radius);
            box-shadow: var(--bp-shadow);
            transition: transform .2s ease, box-shadow .2s ease;
            overflow: hidden;
        }
        .bp-card-hover:hover {
            transform: translateY(-3px);
            box-shadow: var(--bp-shadow-hover);
        }

        /* ══════════ LIVE MEETING HEADER STRIP ══════════ */
        .bp-live-strip {
            background: linear-gradient(135deg, var(--bp-red) 0%, var(--bp-orange) 100%);
            color: #fff;
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }
        .bp-live-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                90deg,
                rgba(255,255,255,.04) 0px,
                rgba(255,255,255,.04) 2px,
                transparent 2px,
                transparent 8px
            );
            pointer-events: none;
        }
        .bp-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.2);
            backdrop-filter: blur(4px);
            border-radius: 999px;
            padding: .35rem .9rem;
            font-weight: 800;
            font-size: .8rem;
            letter-spacing: 1px;
        }
        .bp-live-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #fff;
            animation: bpPulse 1.4s ease-in-out infinite;
        }
        @keyframes bpPulse {
            0%   { box-shadow: 0 0 0 0 rgba(255,255,255,.5); }
            70%  { box-shadow: 0 0 0 10px rgba(255,255,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }
        .bp-elapsed {
            font-size: .8rem;
            opacity: .8;
        }

        /* ══════════ STAT BADGES ══════════ */
        .bp-stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 10px;
            padding: .35rem .75rem;
            font-size: .82rem;
            font-weight: 600;
        }
        .bp-stat-badge-navy { background: var(--bp-navy-soft); color: var(--bp-navy); }
        .bp-stat-badge-green { background: var(--bp-green-soft); color: var(--bp-green); }
        .bp-stat-badge-orange { background: #fff3e0; color: #e65100; }

        /* ══════════ TABS ══════════ */
        .bp-tabs {
            border: 0;
            gap: 4px;
        }
        .bp-tabs .nav-link {
            border: 0;
            border-radius: 10px;
            color: #555;
            font-weight: 600;
            font-size: .9rem;
            padding: .55rem 1.1rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .2s ease;
        }
        .bp-tabs .nav-link:hover {
            background: #f4f4f4;
            color: var(--bp-navy);
        }
        .bp-tabs .nav-link.active {
            background: var(--bp-navy);
            color: #fff;
        }
        .bp-tab-content {
            padding: 20px 0 0;
        }

        /* ══════════ AGENDA ACCORDION ══════════ */
        .bp-accordion .accordion-item {
            border: none;
            border-radius: 12px !important;
            margin-bottom: .75rem;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0,0,0,.05);
        }
        .bp-accordion .accordion-button {
            background: #fff;
            font-weight: 600;
            font-size: .95rem;
            padding: 1rem 1.2rem;
            min-height: 56px;
            gap: 10px;
        }
        .bp-accordion .accordion-button:not(.collapsed) {
            background: var(--bp-navy-soft);
            color: var(--bp-navy);
            box-shadow: none;
        }
        .bp-accordion .accordion-button::after {
            flex-shrink: 0;
            width: 1rem;
            height: 1rem;
        }
        .bp-accordion .accordion-body {
            padding: 1.1rem 1.2rem;
            background: #fff;
        }
        .bp-item-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--bp-navy);
            color: #fff;
            font-weight: 700;
            font-size: .88rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .bp-source-badge {
            border-radius: 999px;
            padding: .2rem .6rem;
            font-size: .75rem;
            font-weight: 600;
        }
        .bp-source-notice { background: #f0f0f0; color: #2b2b2b; }
        .bp-source-paper { background: #e2f8ea; color: #1c9245; }
        .bp-source-cs_added { background: #fff3e0; color: #e65100; }
        .bp-source-ddia_added { background: #e8f5e9; color: #2e7d32; }

        /* ══════════ PAPER GRID CARDS ══════════ */
        .bp-paper-card {
            border: 1px solid #f4f4f4;
            border-radius: 14px;
            padding: 1.1rem;
            transition: all .2s ease;
            cursor: pointer;
            min-height: 44px;
        }
        .bp-paper-card:hover {
            border-color: var(--bp-navy);
            box-shadow: 0 6px 20px rgba(200, 16, 46,.12);
            transform: translateY(-2px);
        }
        .bp-paper-number {
            background: var(--bp-navy);
            color: #fff;
            border-radius: 8px;
            padding: .25rem .65rem;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .3px;
        }
        .bp-ceo-stamp {
            background: var(--bp-green-soft);
            color: var(--bp-green);
            border-radius: 999px;
            padding: .2rem .6rem;
            font-size: .75rem;
            font-weight: 600;
        }

        /* ══════════ ATTENDEE CARDS ══════════ */
        .bp-attendee-card {
            border: 1px solid #f4f4f4;
            border-radius: 12px;
            padding: .85rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: border-color .2s;
        }
        .bp-attendee-card:hover {
            border-color: var(--bp-navy);
        }
        .bp-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e9ecef;
            flex-shrink: 0;
        }
        .bp-avatar-initials {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bp-navy-soft);
            color: var(--bp-navy);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            flex-shrink: 0;
        }
        .bp-conflict-yes {
            background: #ffebee;
            color: #c62828;
            border-radius: 999px;
            padding: .15rem .5rem;
            font-size: .72rem;
            font-weight: 600;
        }
        .bp-conflict-no {
            background: var(--bp-green-soft);
            color: var(--bp-green);
            border-radius: 999px;
            padding: .15rem .5rem;
            font-size: .72rem;
            font-weight: 600;
        }

        /* ══════════ JOIN MEETING BUTTON ══════════ */
        .bp-btn-join {
            background: linear-gradient(135deg, var(--bp-green) 0%, #0cb348 100%);
            border: none;
            color: #fff;
            border-radius: 12px;
            min-height: 52px;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: .3px;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(9,145,57,.3);
        }
        .bp-btn-join:hover {
            background: linear-gradient(135deg, var(--bp-green-dark) 0%, #099139 100%);
            color: #fff;
            box-shadow: 0 6px 20px rgba(9,145,57,.4);
            transform: translateY(-1px);
        }

        /* ══════════ PAPER READING MODAL ══════════ */
        .bp-reading-modal .modal-content {
            border: 0;
            border-radius: 0;
        }
        .bp-reading-modal .modal-header {
            background: var(--bp-navy);
            color: #fff;
            border-radius: 0;
            padding: .85rem 1.25rem;
            min-height: 56px;
        }
        .bp-reading-modal .modal-header .btn-close {
            filter: invert(1);
            opacity: .8;
            min-width: 44px;
            min-height: 44px;
        }
        .bp-reading-modal .modal-header .btn-close:hover {
            opacity: 1;
        }
        .bp-reading-body {
            display: flex;
            min-height: calc(100vh - 56px);
        }
        .bp-reading-main {
            flex: 0 0 70%;
            max-width: 70%;
            padding: 2.5rem 3rem;
            overflow-y: auto;
            background: #fff;
        }
        .bp-reading-sidebar {
            flex: 0 0 30%;
            max-width: 30%;
            background: #fafafa;
            border-left: 1px solid #e9ecef;
            padding: 1.5rem;
            overflow-y: auto;
            position: sticky;
            top: 0;
            height: calc(100vh - 56px);
        }
        .bp-reading-content {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.05rem;
            line-height: 1.7;
            color: #333;
        }
        .bp-reading-content h1,
        .bp-reading-content h2,
        .bp-reading-content h3,
        .bp-reading-content h4,
        .bp-reading-content h5,
        .bp-reading-content h6 {
            color: var(--bp-navy);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin-top: 1.5rem;
            margin-bottom: .75rem;
        }
        .bp-reading-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .bp-reading-content table th,
        .bp-reading-content table td {
            border: 1px solid #dee2e6;
            padding: .5rem .75rem;
            font-size: .95rem;
        }
        .bp-reading-content table thead th {
            background: var(--bp-navy-soft);
            color: var(--bp-navy);
            font-weight: 600;
        }
        .bp-reading-content ul,
        .bp-reading-content ol {
            padding-left: 1.5rem;
        }
        .bp-reading-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* CEO watermark */
        .bp-ceo-watermark {
            border-top: 2px dashed #e0e0e0;
            margin-top: 2rem;
            padding-top: 1.5rem;
            text-align: center;
            color: #999;
        }
        .bp-ceo-watermark img {
            max-height: 60px;
            opacity: .6;
        }

        /* ══════════ SIDEBAR COMPONENTS ══════════ */
        .bp-sidebar-card {
            background: #fff;
            border: 1px solid #f4f4f4;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .bp-sidebar-title {
            font-size: .92rem;
            font-weight: 700;
            color: #141414;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .bp-info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .35rem 0;
            border-bottom: 1px dashed #f4f4f4;
            font-size: .85rem;
        }
        .bp-info-row:last-child { border-bottom: 0; }
        .bp-info-label { color: #6c757d; }
        .bp-info-value { font-weight: 600; color: #333; text-align: right; max-width: 55%; }

        /* Annexure items */
        .bp-annexure-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: .6rem .8rem;
            transition: border-color .2s;
            margin-bottom: .5rem;
        }
        .bp-annexure-item:hover {
            border-color: var(--bp-navy);
        }
        .bp-annexure-preview {
            width: 100%;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            min-height: 300px;
            margin-top: .5rem;
        }

        /* Sticky notes */
        .bp-note-item {
            border-radius: 10px;
            padding: .7rem .85rem;
            margin-bottom: .5rem;
            position: relative;
            font-size: .88rem;
            line-height: 1.5;
            min-height: 44px;
        }
        .bp-note-delete {
            position: absolute;
            top: 4px;
            right: 6px;
            background: none;
            border: none;
            color: #999;
            font-size: 1rem;
            padding: 4px;
            cursor: pointer;
            min-height: 36px;
            min-width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all .2s;
        }
        .bp-note-delete:hover {
            background: rgba(0,0,0,.06);
            color: #dc3545;
        }
        .bp-color-selector .bp-color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .bp-color-selector .bp-color-option:hover {
            transform: scale(1.15);
        }
        .bp-color-selector .bp-color-option.active {
            border-color: var(--bp-navy);
            box-shadow: 0 0 0 2px rgba(200, 16, 46,.25);
        }

        /* ══════════ SCHEDULED CARD ══════════ */
        .bp-scheduled-card {
            border-left: 4px solid var(--bp-navy);
            opacity: .9;
        }
        .bp-not-started-badge {
            background: #fff3e0;
            color: #e65100;
            border-radius: 999px;
            padding: .3rem .75rem;
            font-weight: 700;
            font-size: .8rem;
        }

        /* ══════════ EMPTY STATE ══════════ */
        .bp-empty-icon {
            font-size: 72px;
            color: #dd3c49;
        }
        .bp-btn-outline-navy {
            border: 2px solid var(--bp-navy);
            color: var(--bp-navy);
            border-radius: 10px;
            min-height: 44px;
            font-weight: 600;
            transition: all .2s;
        }
        .bp-btn-outline-navy:hover {
            background: var(--bp-navy);
            color: #fff;
        }

        /* ══════════ VIEW PAPER BUTTON ══════════ */
        .bp-btn-view-paper {
            background: var(--bp-navy-soft);
            color: var(--bp-navy);
            border: 1px solid transparent;
            border-radius: 8px;
            padding: .4rem .85rem;
            font-size: .85rem;
            font-weight: 600;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .2s;
            cursor: pointer;
        }
        .bp-btn-view-paper:hover {
            background: var(--bp-navy);
            color: #fff;
        }

        /* ══════════ RESPONSIVE ══════════ */
        @media (max-width: 991px) {
            .bp-reading-body {
                flex-direction: column;
            }
            .bp-reading-main {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 1.5rem;
            }
            .bp-reading-sidebar {
                flex: 0 0 100%;
                max-width: 100%;
                position: relative;
                height: auto;
                border-left: none;
                border-top: 1px solid #e9ecef;
            }
        }
        @media (max-width: 767px) {
            .bp-hero {
                padding: 20px 18px 16px;
            }
            .bp-live-strip {
                padding: 14px 16px;
            }
            .bp-reading-main {
                padding: 1rem;
            }
            .bp-reading-content {
                font-size: .98rem;
            }
            .bp-stat-chip {
                padding: .3rem .7rem;
                font-size: .8rem;
            }
        }
        @media (min-width: 768px) and (max-width: 1200px) {
            .bp-reading-main {
                padding: 2rem;
            }
            .btn, .form-control, .form-select {
                min-height: 44px;
                font-size: 15px;
            }
        }

        /* ══════════ SMOOTH TRANSITIONS ══════════ */
        .tab-pane {
            animation: bpFadeIn .25s ease-in-out;
        }
        @keyframes bpFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="content bp-page">
        <div class="container-fluid">

            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  1. HERO BANNER                                             ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            <div class="bp-hero mb-4">
                <div class="bp-hero-inner">
                    <div class="bp-breadcrumb mb-2">
                        <a href="{{ route('board.portal.dashboard') }}">
                            <i class="mdi mdi-arrow-left me-1"></i> Board Portal
                        </a>
                    </div>
                    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                        <div>
                            <h2 class="fw-bold mb-1" style="font-size:1.6rem;">
                                <i class="mdi mdi-video-outline me-2" style="opacity:.85;"></i>Active Meetings
                            </h2>
                            <p class="mb-0" style="opacity:.75; font-size:.95rem;">
                                View live meetings, agenda items, and board papers in real time
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($liveCount > 0)
                                <span class="bp-stat-chip">
                                    <span class="bp-live-dot"></span>
                                    {{ $liveCount }} Live
                                </span>
                            @endif
                            @if($scheduledCount > 0)
                                <span class="bp-stat-chip">
                                    <i class="mdi mdi-clock-outline"></i>
                                    {{ $scheduledCount }} Scheduled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  2. LIVE MEETINGS                                           ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            @if($activeMeetings->isNotEmpty())
                <div class="bp-section-title">
                    <i class="mdi mdi-broadcast text-danger"></i>
                    Live Meetings
                </div>

                @foreach($activeMeetings as $mIdx => $meeting)
                    @php
                        $started        = Carbon::parse($meeting->started_at);
                        $committeeName  = optional($meeting->boardCommittee)->name ?? 'Board Meeting';
                        $attendeeCount  = $meeting->attendance->count();
                        $paperCount     = $meeting->boardPapers->count();
                        $agendaCount    = $meeting->agendaItems->count();
                        $meetingKey     = 'meeting-' . $meeting->id;
                        $declarations   = $meeting->conflictDeclarations;
                    @endphp

                    @php
                        $myDecl = $myDeclarations[$meeting->id] ?? null;
                        $hasCompletedDeclaration = $myDecl && $myDecl->isComplete();
                    @endphp

                    <div class="bp-card mb-4">
                        {{-- Red-Orange header strip --}}
                        <div class="bp-live-strip">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative" style="z-index:1;">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <span class="bp-live-badge">
                                            <span class="bp-live-dot"></span> LIVE
                                        </span>
                                        <span class="badge rounded-pill px-2 py-1" style="background:rgba(255,255,255,.2); color:#fff; font-size:.78rem;">
                                            <i class="mdi mdi-account-group-outline me-1"></i>{{ $committeeName }}
                                        </span>
                                    </div>
                                    <h4 class="fw-bold mb-1" style="font-size:1.25rem;">{{ $meeting->title }}</h4>
                                    <div style="font-size:.88rem; opacity:.85;">
                                        <i class="mdi mdi-calendar me-1"></i>{{ Carbon::parse($meeting->meeting_date)->isoFormat('ddd, D MMM YYYY') }}
                                        @if($meeting->meeting_time)
                                            <span class="mx-1">&middot;</span>
                                            <i class="mdi mdi-clock-outline me-1"></i>{{ Carbon::parse($meeting->meeting_time)->format('g:i A') }}
                                        @endif
                                        @if($meeting->venue)
                                            <span class="mx-1">&middot;</span>
                                            <i class="mdi mdi-map-marker-outline me-1"></i>{{ $meeting->venue }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="bp-elapsed" data-started="{{ $started->toIso8601String() }}">
                                        <i class="mdi mdi-clock-start me-1"></i>
                                        Started <span class="bp-elapsed-text">{{ $started->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Meeting body --}}
                        <div class="card-body p-3 p-md-4">

                            @if($hasCompletedDeclaration)
                                {{-- ── FULL MEETING CONTENT (only after joining + conflict declaration) ── --}}

                                {{-- Stat badges --}}
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="bp-stat-badge bp-stat-badge-navy">
                                        <i class="mdi mdi-account-check-outline"></i> {{ $attendeeCount }} Attendees
                                    </span>
                                    <span class="bp-stat-badge bp-stat-badge-green">
                                        <i class="mdi mdi-file-document-outline"></i> {{ $paperCount }} Papers
                                    </span>
                                    <span class="bp-stat-badge bp-stat-badge-orange">
                                        <i class="mdi mdi-format-list-numbered"></i> {{ $agendaCount }} Agenda Items
                                    </span>
                                </div>

                                {{-- Tabs navigation --}}
                                <ul class="nav bp-tabs mb-0" id="tabs-{{ $meetingKey }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active"
                                                id="agenda-tab-{{ $meetingKey }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#agenda-pane-{{ $meetingKey }}"
                                                data-meeting="{{ $meeting->id }}"
                                                data-tab="agenda"
                                                type="button" role="tab"
                                                aria-controls="agenda-pane-{{ $meetingKey }}"
                                                aria-selected="true">
                                            <i class="mdi mdi-format-list-numbered"></i> Agenda
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link"
                                                id="papers-tab-{{ $meetingKey }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#papers-pane-{{ $meetingKey }}"
                                                data-meeting="{{ $meeting->id }}"
                                                data-tab="papers"
                                                type="button" role="tab"
                                                aria-controls="papers-pane-{{ $meetingKey }}"
                                                aria-selected="false">
                                            <i class="mdi mdi-file-document-multiple-outline"></i> Papers
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link"
                                                id="attendance-tab-{{ $meetingKey }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#attendance-pane-{{ $meetingKey }}"
                                                data-meeting="{{ $meeting->id }}"
                                                data-tab="attendance"
                                                type="button" role="tab"
                                                aria-controls="attendance-pane-{{ $meetingKey }}"
                                                aria-selected="false">
                                            <i class="mdi mdi-account-group-outline"></i> Attendance
                                        </button>
                                    </li>
                                </ul>

                                {{-- Tab content --}}
                                <div class="tab-content bp-tab-content" id="tabContent-{{ $meetingKey }}">

                                    {{-- ── AGENDA TAB ── --}}
                                    <div class="tab-pane fade show active"
                                         id="agenda-pane-{{ $meetingKey }}" role="tabpanel"
                                         aria-labelledby="agenda-tab-{{ $meetingKey }}">

                                        @if($meeting->agendaItems->isEmpty())
                                            <div class="text-center text-muted py-4">
                                                <i class="mdi mdi-clipboard-text-off-outline d-block mb-2" style="font-size:2rem;"></i>
                                                No agenda items have been added to this meeting yet.
                                            </div>
                                        @else
                                            <div class="accordion bp-accordion" id="accordion-{{ $meetingKey }}">
                                                @foreach($meeting->agendaItems as $aIdx => $item)
                                                    @php
                                                        $itemKey = $meetingKey . '-item-' . $item->id;
                                                        $sourceClass = 'bp-source-notice';
                                                        if($item->source === 'cs_added') $sourceClass = 'bp-source-cs_added';
                                                        elseif($item->source === 'ddia_added') $sourceClass = 'bp-source-ddia_added';
                                                        elseif($item->source === 'paper') $sourceClass = 'bp-source-paper';
                                                    @endphp
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading-{{ $itemKey }}">
                                                            <button class="accordion-button {{ $aIdx > 0 ? 'collapsed' : '' }}"
                                                                    type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse-{{ $itemKey }}"
                                                                    aria-expanded="{{ $aIdx === 0 ? 'true' : 'false' }}"
                                                                    aria-controls="collapse-{{ $itemKey }}">
                                                                <span class="bp-item-number">{{ $item->item_number }}</span>
                                                                <span class="flex-grow-1">{{ $item->title }}</span>
                                                                @if($item->source)
                                                                    <span class="bp-source-badge {{ $sourceClass }} ms-2">
                                                                        {{ ucfirst(str_replace('_', ' ', $item->source)) }}
                                                                    </span>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-{{ $itemKey }}"
                                                             class="accordion-collapse collapse {{ $aIdx === 0 ? 'show' : '' }}"
                                                             aria-labelledby="heading-{{ $itemKey }}"
                                                             data-bs-parent="#accordion-{{ $meetingKey }}">
                                                            <div class="accordion-body">
                                                                @if($item->description)
                                                                    <div class="text-muted mb-3" style="font-size:.93rem; line-height:1.6;">
                                                                        {!! nl2br(e($item->description)) !!}
                                                                    </div>
                                                                @endif

                                                                @if($item->boardPaper)
                                                                    <div class="d-flex flex-wrap align-items-center gap-2 p-3 rounded-3" style="background:#fafafa;">
                                                                        <i class="mdi mdi-file-document-outline" style="font-size:1.3rem; color:var(--bp-navy);"></i>
                                                                        <div class="flex-grow-1">
                                                                            <div class="fw-semibold" style="font-size:.9rem;">{{ $item->boardPaper->title }}</div>
                                                                            <div class="text-muted" style="font-size:.8rem;">{{ $item->boardPaper->paper_number }}</div>
                                                                        </div>
                                                                        <button type="button"
                                                                                class="bp-btn-view-paper"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#paperModal-{{ $item->boardPaper->id }}">
                                                                            <i class="mdi mdi-book-open-page-variant-outline"></i> View Paper
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ── PAPERS TAB ── --}}
                                    <div class="tab-pane fade"
                                         id="papers-pane-{{ $meetingKey }}" role="tabpanel"
                                         aria-labelledby="papers-tab-{{ $meetingKey }}">

                                        @if($meeting->boardPapers->isEmpty())
                                            <div class="text-center text-muted py-4">
                                                <i class="mdi mdi-file-document-off-outline d-block mb-2" style="font-size:2rem;"></i>
                                                No CEO-approved papers available for this meeting.
                                            </div>
                                        @else
                                            <div class="row g-3">
                                                @foreach($meeting->boardPapers as $paper)
                                                    @php
                                                        $annexureCount = $paper->annexures ? $paper->annexures->count() : 0;
                                                    @endphp
                                                    <div class="col-sm-6 col-xl-4">
                                                        <div class="bp-paper-card h-100"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#paperModal-{{ $paper->id }}">
                                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                                <span class="bp-paper-number">{{ $paper->paper_number ?? 'N/A' }}</span>
                                                                @if($paper->ceo_signature)
                                                                    <span class="bp-ceo-stamp">
                                                                        <i class="mdi mdi-check-decagram me-1"></i> CEO Approved
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <h6 class="fw-bold mb-2" style="font-size:.92rem; color:#141414; line-height:1.4;">
                                                                {{ $paper->title }}
                                                            </h6>
                                                            <div class="d-flex flex-wrap align-items-center gap-2 text-muted" style="font-size:.8rem;">
                                                                <span>
                                                                    <i class="mdi mdi-account-outline me-1"></i>{{ optional($paper->author)->name ?? 'Unknown' }}
                                                                </span>
                                                                @if($annexureCount > 0)
                                                                    <span>
                                                                        <i class="mdi mdi-paperclip me-1"></i>{{ $annexureCount }} annexure{{ $annexureCount > 1 ? 's' : '' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="mt-2 text-end">
                                                                <span class="bp-btn-view-paper" style="font-size:.8rem; padding:.3rem .65rem;">
                                                                    <i class="mdi mdi-book-open-page-variant-outline"></i> Read
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ── ATTENDANCE TAB ── --}}
                                    <div class="tab-pane fade"
                                         id="attendance-pane-{{ $meetingKey }}" role="tabpanel"
                                         aria-labelledby="attendance-tab-{{ $meetingKey }}">

                                        @if($declarations->isEmpty())
                                            <div class="text-center text-muted py-4">
                                                <i class="mdi mdi-account-off-outline d-block mb-2" style="font-size:2rem;"></i>
                                                No conflict declarations have been submitted yet.
                                            </div>
                                        @else
                                            @php
                                                $signedMembers = $declarations->filter(function($d) { return $d->signed_at; });
                                                $unsignedMembers = $declarations->filter(function($d) { return !$d->signed_at; });
                                            @endphp

                                            @if($signedMembers->isNotEmpty())
                                                <div class="mb-3">
                                                    <h6 class="fw-semibold text-muted mb-2" style="font-size:.85rem;">
                                                        <i class="mdi mdi-check-circle text-success me-1"></i>
                                                        Present ({{ $signedMembers->count() }})
                                                    </h6>
                                                    <div class="row g-2">
                                                        @foreach($signedMembers as $decl)
                                                            @php
                                                                $dm = $decl->boardMember;
                                                                $names = optional($dm)->names ?? 'Unknown';
                                                                $initials = collect(explode(' ', $names))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                                                $photo = optional($dm)->photo;
                                                                $designation = optional($dm)->designation ?? '';
                                                            @endphp
                                                            <div class="col-sm-6 col-lg-4">
                                                                <div class="bp-attendee-card">
                                                                    @if($photo)
                                                                        <img src="{{ asset('storage/' . $photo) }}" alt="{{ $names }}" class="bp-avatar">
                                                                    @else
                                                                        <span class="bp-avatar-initials">{{ $initials }}</span>
                                                                    @endif
                                                                    <div class="flex-grow-1 min-width-0">
                                                                        <div class="fw-semibold text-truncate" style="font-size:.88rem;">{{ $names }}</div>
                                                                        @if($designation)
                                                                            <div class="text-muted text-truncate" style="font-size:.78rem;">{{ $designation }}</div>
                                                                        @endif
                                                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                                            @if($decl->has_conflict)
                                                                                <span class="bp-conflict-yes">
                                                                                    <i class="mdi mdi-alert-circle-outline me-1"></i>Conflict
                                                                                </span>
                                                                            @else
                                                                                <span class="bp-conflict-no">
                                                                                    <i class="mdi mdi-check-circle-outline me-1"></i>No Conflict
                                                                                </span>
                                                                            @endif
                                                                            @if($decl->signed_at)
                                                                                <span class="text-muted" style="font-size:.72rem;">
                                                                                    {{ Carbon::parse($decl->signed_at)->format('g:i A') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($unsignedMembers->isNotEmpty())
                                                <div>
                                                    <h6 class="fw-semibold text-muted mb-2" style="font-size:.85rem;">
                                                        <i class="mdi mdi-clock-outline text-warning me-1"></i>
                                                        Not Yet Signed ({{ $unsignedMembers->count() }})
                                                    </h6>
                                                    <div class="row g-2">
                                                        @foreach($unsignedMembers as $decl)
                                                            @php
                                                                $dm = $decl->boardMember;
                                                                $names = optional($dm)->names ?? 'Unknown';
                                                                $initials = collect(explode(' ', $names))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                                                $photo = optional($dm)->photo;
                                                                $designation = optional($dm)->designation ?? '';
                                                            @endphp
                                                            <div class="col-sm-6 col-lg-4">
                                                                <div class="bp-attendee-card" style="opacity:.6;">
                                                                    @if($photo)
                                                                        <img src="{{ asset('storage/' . $photo) }}" alt="{{ $names }}" class="bp-avatar">
                                                                    @else
                                                                        <span class="bp-avatar-initials">{{ $initials }}</span>
                                                                    @endif
                                                                    <div class="flex-grow-1 min-width-0">
                                                                        <div class="fw-semibold text-truncate" style="font-size:.88rem;">{{ $names }}</div>
                                                                        @if($designation)
                                                                            <div class="text-muted text-truncate" style="font-size:.78rem;">{{ $designation }}</div>
                                                                        @endif
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-warning text-dark" style="font-size:.72rem;">Awaiting</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                </div>{{-- end tab-content --}}

                            @else
                                {{-- ── GATED: Member has not joined / declared conflict yet ── --}}
                                <div class="text-center py-5">
                                    <i class="mdi mdi-shield-lock-outline" style="font-size: 64px; color: #ff9800;"></i>
                                    <h5 class="fw-bold mt-3 mb-2">Join Meeting Required</h5>
                                    <p class="text-muted mb-0" style="max-width: 480px; margin: 0 auto; line-height: 1.6;">
                                        You must join this meeting and complete your conflict of interest declaration
                                        before you can view the agenda, board papers, and attendance register.
                                    </p>
                                </div>
                            @endif

                            {{-- Join Meeting button --}}
                            <div class="mt-4 pt-3" style="border-top:1px solid #f4f4f4;">
                                <a href="{{ route('board.portal.meeting.live', $meeting) }}"
                                   class="btn bp-btn-join btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i class="mdi mdi-login-variant mdi-24px"></i>
                                    {{ $hasCompletedDeclaration ? 'View Meeting' : 'Join Meeting' }}
                                </a>
                            </div>
                        </div>
                    </div>{{-- end bp-card --}}

                    {{-- ══════════ PAPER READING MODALS (only if declaration complete) ══════════ --}}
                    @if($hasCompletedDeclaration)
                    @foreach($meeting->boardPapers as $paper)
                        @php
                            $paperNotes = isset($myPaperNotes[$paper->id]) ? $myPaperNotes[$paper->id] : collect([]);
                            $annexures  = $paper->annexures ?? collect([]);
                        @endphp
                        <div class="modal fade bp-reading-modal"
                             id="paperModal-{{ $paper->id }}"
                             tabindex="-1"
                             aria-labelledby="paperModalLabel-{{ $paper->id }}"
                             aria-hidden="true">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    {{-- Modal header --}}
                                    <div class="modal-header">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge rounded-pill px-2 py-1" style="background:rgba(255,255,255,.2); font-size:.8rem;">
                                                {{ $paper->paper_number ?? 'N/A' }}
                                            </span>
                                            <h5 class="modal-title mb-0 fw-bold text-truncate" id="paperModalLabel-{{ $paper->id }}" style="font-size:1.05rem; max-width:60vw;">
                                                {{ $paper->title }}
                                            </h5>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    {{-- Modal body: split layout --}}
                                    <div class="modal-body p-0">
                                        <div class="bp-reading-body">

                                            {{-- LEFT: Paper content (70%) --}}
                                            <div class="bp-reading-main">
                                                {{-- Paper number badge --}}
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                                    <span class="bp-paper-number" style="font-size:.85rem;">{{ $paper->paper_number ?? 'N/A' }}</span>
                                                    @if($paper->ceo_signature)
                                                        <span class="bp-ceo-stamp">
                                                            <i class="mdi mdi-check-decagram me-1"></i> CEO Approved
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Title --}}
                                                <h3 class="fw-bold mb-2" style="color:#141414; font-size:1.4rem;">
                                                    {{ $paper->title }}
                                                </h3>

                                                {{-- Author line --}}
                                                <div class="d-flex flex-wrap gap-3 text-muted mb-4" style="font-size:.9rem;">
                                                    <span>
                                                        <i class="mdi mdi-account-outline me-1"></i>{{ optional($paper->author)->name ?? 'Unknown Author' }}
                                                    </span>
                                                    @if($paper->ceo_approved_at)
                                                        <span>
                                                            <i class="mdi mdi-calendar-check me-1"></i>Approved {{ Carbon::parse($paper->ceo_approved_at)->isoFormat('D MMM YYYY') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <hr class="mb-4">

                                                {{-- Rendered HTML content --}}
                                                <div class="bp-reading-content">
                                                    {!! $paper->rendered_content !!}
                                                </div>

                                                {{-- CEO signature watermark --}}
                                                @if($paper->ceo_signature)
                                                    <div class="bp-ceo-watermark">
                                                        <div class="text-muted mb-2" style="font-size:.82rem; text-transform:uppercase; letter-spacing:1px;">
                                                            <i class="mdi mdi-seal me-1"></i> CEO Approval
                                                        </div>
                                                        <img src="{{ $paper->ceo_signature }}" alt="CEO Signature">
                                                        @if($paper->ceo_approved_at)
                                                            <div class="mt-1" style="font-size:.78rem; color:#aaa;">
                                                                Approved on {{ Carbon::parse($paper->ceo_approved_at)->isoFormat('dddd, D MMMM YYYY [at] h:mm A') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- RIGHT: Sidebar (30%) --}}
                                            <div class="bp-reading-sidebar">

                                                {{-- Paper Details --}}
                                                <div class="bp-sidebar-card">
                                                    <div class="bp-sidebar-title">
                                                        <i class="mdi mdi-information-outline" style="color:var(--bp-navy);"></i> Paper Details
                                                    </div>
                                                    <div class="bp-info-row">
                                                        <span class="bp-info-label">Paper No.</span>
                                                        <span class="bp-info-value">{{ $paper->paper_number ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="bp-info-row">
                                                        <span class="bp-info-label">Author</span>
                                                        <span class="bp-info-value">{{ optional($paper->author)->name ?? '—' }}</span>
                                                    </div>
                                                    @if($paper->ceo_approved_at)
                                                        <div class="bp-info-row">
                                                            <span class="bp-info-label">Approved</span>
                                                            <span class="bp-info-value">{{ Carbon::parse($paper->ceo_approved_at)->isoFormat('D MMM YYYY') }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="bp-info-row">
                                                        <span class="bp-info-label">Status</span>
                                                        <span class="bp-info-value">
                                                            @if($paper->ceo_signature)
                                                                <span class="text-success"><i class="mdi mdi-check-circle me-1"></i>CEO Approved</span>
                                                            @else
                                                                <span class="text-warning"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Annexures --}}
                                                @if($annexures->isNotEmpty())
                                                    <div class="bp-sidebar-card">
                                                        <div class="bp-sidebar-title">
                                                            <i class="mdi mdi-paperclip" style="color:var(--bp-navy);"></i>
                                                            Annexures
                                                            <span class="badge rounded-pill ms-auto" style="background:var(--bp-navy-soft); color:var(--bp-navy); font-size:.75rem;">
                                                                {{ $annexures->count() }}
                                                            </span>
                                                        </div>
                                                        @foreach($annexures as $axIdx => $annexure)
                                                            @php
                                                                $fileIcon = 'mdi-file-outline';
                                                                $ext = strtolower(pathinfo($annexure->file_name ?? '', PATHINFO_EXTENSION));
                                                                if(in_array($ext, ['pdf'])) $fileIcon = 'mdi-file-pdf-box';
                                                                elseif(in_array($ext, ['doc','docx'])) $fileIcon = 'mdi-file-word-box';
                                                                elseif(in_array($ext, ['xls','xlsx'])) $fileIcon = 'mdi-file-excel-box';
                                                                elseif(in_array($ext, ['ppt','pptx'])) $fileIcon = 'mdi-file-powerpoint-box';
                                                                elseif(in_array($ext, ['png','jpg','jpeg','gif'])) $fileIcon = 'mdi-file-image';
                                                                $isPdf = ($ext === 'pdf');
                                                                $annexureKey = 'annexure-' . $paper->id . '-' . $axIdx;
                                                            @endphp
                                                            <div class="bp-annexure-item">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center gap-2 min-width-0">
                                                                        <i class="mdi {{ $fileIcon }}" style="font-size:1.3rem; color:var(--bp-navy); flex-shrink:0;"></i>
                                                                        <div class="min-width-0">
                                                                            <div class="fw-semibold text-truncate" style="font-size:.85rem;">{{ $annexure->title }}</div>
                                                                            <div class="text-muted" style="font-size:.75rem;">
                                                                                {{ $annexure->file_name ?? 'Document' }}
                                                                                @if($annexure->file_size)
                                                                                    ({{ number_format($annexure->file_size / 1024, 0) }} KB)
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if($annexure->file_path)
                                                                        <div class="d-flex gap-1 flex-shrink-0">
                                                                            @if($isPdf)
                                                                                <button type="button"
                                                                                        class="btn btn-sm btn-outline-primary bp-toggle-preview"
                                                                                        data-target="#preview-{{ $annexureKey }}"
                                                                                        style="min-height:36px; min-width:36px; border-radius:8px; font-size:.8rem;">
                                                                                    <i class="mdi mdi-eye-outline"></i>
                                                                                </button>
                                                                            @endif
                                                                            <a href="{{ asset('storage/' . $annexure->file_path) }}" target="_blank"
                                                                               class="btn btn-sm btn-outline-secondary"
                                                                               style="min-height:36px; min-width:36px; border-radius:8px; font-size:.8rem;">
                                                                                <i class="mdi mdi-download"></i>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if($isPdf && $annexure->file_path)
                                                                    <div id="preview-{{ $annexureKey }}" style="display:none;">
                                                                        <iframe src="{{ asset('storage/' . $annexure->file_path) }}#toolbar=0"
                                                                                class="bp-annexure-preview"
                                                                                loading="lazy"></iframe>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- Sticky Notes --}}
                                                <div class="bp-sidebar-card">
                                                    <div class="bp-sidebar-title">
                                                        <i class="mdi mdi-note-multiple" style="color:var(--bp-navy);"></i> My Notes
                                                    </div>

                                                    {{-- Existing notes --}}
                                                    <div class="bp-notes-list" data-paper-id="{{ $paper->id }}">
                                                        @forelse($paperNotes as $note)
                                                            <div class="bp-note-item" id="bp-note-{{ $note->id }}"
                                                                 style="background: {{ $noteColors[$note->color] ?? $noteColors['yellow'] }};">
                                                                <button class="bp-note-delete" onclick="bpDeleteNote({{ $note->id }})" title="Delete note">
                                                                    <i class="mdi mdi-close"></i>
                                                                </button>
                                                                <div class="pe-4" style="white-space:pre-wrap;">{{ $note->content }}</div>
                                                                <div class="d-flex align-items-center gap-2 mt-1" style="font-size:.72rem; color:#888;">
                                                                    {{ $note->created_at ? Carbon::parse($note->created_at)->diffForHumans() : '' }}
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="bp-no-notes text-muted text-center py-3" style="font-size:.88rem;">
                                                                <i class="mdi mdi-note-off-outline d-block mb-1" style="font-size:1.3rem;"></i>
                                                                No notes yet
                                                            </div>
                                                        @endforelse
                                                    </div>

                                                    <hr class="my-2">

                                                    {{-- Add note form --}}
                                                    <div class="bp-note-form" data-paper-id="{{ $paper->id }}">
                                                        <textarea class="form-control bp-note-textarea mb-2" rows="2"
                                                                  placeholder="Type a note..."
                                                                  style="border-radius:8px; font-size:.88rem; min-height:60px; resize:vertical;"></textarea>

                                                        <div class="bp-color-selector d-flex gap-2 mb-2">
                                                            @foreach($noteColors as $colorName => $colorHex)
                                                                <div class="bp-color-option {{ $colorName === 'yellow' ? 'active' : '' }}"
                                                                     data-color="{{ $colorName }}"
                                                                     style="background:{{ $colorHex }};"
                                                                     title="{{ ucfirst($colorName) }}">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <button type="button"
                                                                class="btn w-100 bp-save-note-btn"
                                                                style="background:var(--bp-navy); color:#fff; border-radius:8px; min-height:44px; font-weight:600; font-size:.88rem;"
                                                                data-paper-id="{{ $paper->id }}"
                                                                data-url="{{ route('board.portal.paper.note.store', $paper->id) }}">
                                                            <i class="mdi mdi-content-save me-1"></i> Save Note
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>{{-- end sidebar --}}

                                        </div>{{-- end bp-reading-body --}}
                                    </div>{{-- end modal-body --}}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endif

                @endforeach
            @endif


            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  3. SCHEDULED FOR TODAY                                     ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            @if($todaysMeetings->isNotEmpty())
                <div class="bp-section-title mt-2">
                    <i class="mdi mdi-clock-outline" style="color:var(--bp-navy);"></i>
                    Scheduled for Today
                </div>

                <div class="row g-3 mb-4">
                    @foreach($todaysMeetings as $meeting)
                        @php
                            $committeeName = optional($meeting->boardCommittee)->name ?? 'Board Meeting';
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="bp-card bp-card-hover bp-scheduled-card h-100">
                                <div class="card-body d-flex flex-column p-3 p-md-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="bp-not-started-badge">
                                            <i class="mdi mdi-clock-alert-outline me-1"></i> Not Started
                                        </span>
                                        <span class="bp-stat-badge bp-stat-badge-navy" style="font-size:.78rem;">
                                            <i class="mdi mdi-account-group-outline"></i> {{ $committeeName }}
                                        </span>
                                    </div>

                                    <h5 class="fw-bold mb-2" style="font-size:1.05rem; color:#141414;">
                                        {{ $meeting->title }}
                                    </h5>

                                    <div class="text-muted small mb-1">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        {{ Carbon::parse($meeting->meeting_date)->isoFormat('ddd, D MMM YYYY') }}
                                        @if($meeting->meeting_time)
                                            <span class="mx-1">&middot;</span>
                                            <i class="mdi mdi-clock-outline me-1"></i>{{ Carbon::parse($meeting->meeting_time)->format('g:i A') }}
                                        @endif
                                    </div>

                                    @if($meeting->venue)
                                        <div class="text-muted small mb-2">
                                            <i class="mdi mdi-map-marker-outline me-1"></i> {{ $meeting->venue }}
                                        </div>
                                    @endif

                                    <div class="alert mb-0 mt-auto py-2 px-3 d-flex align-items-start gap-2"
                                         style="background:#f2f2f2; border:0; border-radius:10px; font-size:.88rem; color:#141414;">
                                        <i class="mdi mdi-information-outline mt-1" style="flex-shrink:0;"></i>
                                        <div>
                                            This meeting has not been started yet. Please wait for the Corporation Secretary to initiate the session.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif


            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  4. EMPTY STATE                                             ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            @if($activeMeetings->isEmpty() && $todaysMeetings->isEmpty())
                <div class="bp-card">
                    <div class="card-body text-center py-5 px-4">
                        <div class="bp-empty-icon mb-3">
                            <i class="mdi mdi-calendar-blank-outline"></i>
                        </div>
                        <h4 class="fw-bold mb-2" style="color:#141414;">No Active Meetings</h4>
                        <p class="text-muted mb-4" style="max-width:420px; margin:0 auto; font-size:.95rem; line-height:1.6;">
                            There are no live or scheduled meetings at this time. When the Corporation Secretary starts a meeting, it will appear here.
                        </p>
                        <a href="{{ route('board.portal.dashboard') }}" class="btn bp-btn-outline-navy px-4">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════════
         ║  JAVASCRIPT
         ╚══════════════════════════════════════════════════════════════════════ --}}
    <script>
    (function($) {
        'use strict';

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var noteColors = @json($noteColors);

        /* ══════════════════════════════════════════════
           1. TAB MEMORY — remember last active tab per meeting
        ══════════════════════════════════════════════ */
        function getTabStorageKey(meetingId) {
            return 'bp_active_tab_meeting_' + meetingId;
        }

        // Restore saved tabs on page load
        $('[data-bs-toggle="tab"]').each(function() {
            var meetingId = $(this).data('meeting');
            if (!meetingId) return;
            var saved = sessionStorage.getItem(getTabStorageKey(meetingId));
            if (saved && $(this).data('tab') === saved) {
                var tabEl = new bootstrap.Tab(this);
                tabEl.show();
            }
        });

        // Save tab selection
        $(document).on('shown.bs.tab', '[data-bs-toggle="tab"]', function() {
            var meetingId = $(this).data('meeting');
            var tabName = $(this).data('tab');
            if (meetingId && tabName) {
                sessionStorage.setItem(getTabStorageKey(meetingId), tabName);
            }
        });

        /* ══════════════════════════════════════════════
           2. LIVE ELAPSED TIME COUNTER
        ══════════════════════════════════════════════ */
        function updateElapsed() {
            $('.bp-elapsed').each(function() {
                var startedStr = $(this).data('started');
                if (!startedStr) return;
                var started = new Date(startedStr);
                var now = new Date();
                var diffMs = now - started;
                if (diffMs < 0) diffMs = 0;
                var diffMins = Math.floor(diffMs / 60000);
                var text;
                if (diffMins < 1) {
                    text = 'just now';
                } else if (diffMins === 1) {
                    text = '1 min ago';
                } else if (diffMins < 60) {
                    text = diffMins + ' mins ago';
                } else {
                    var hours = Math.floor(diffMins / 60);
                    var mins = diffMins % 60;
                    if (hours === 1) {
                        text = '1 hr ' + (mins > 0 ? mins + ' min' : '') + ' ago';
                    } else {
                        text = hours + ' hrs ' + (mins > 0 ? mins + ' min' : '') + ' ago';
                    }
                }
                $(this).find('.bp-elapsed-text').text(text);
            });
        }
        updateElapsed();
        setInterval(updateElapsed, 60000);

        /* ══════════════════════════════════════════════
           3. STICKY NOTES — AJAX SAVE
        ══════════════════════════════════════════════ */
        // Color selector within note forms
        $(document).on('click', '.bp-color-option', function() {
            $(this).closest('.bp-color-selector').find('.bp-color-option').removeClass('active');
            $(this).addClass('active');
        });

        // Save note
        $(document).on('click', '.bp-save-note-btn', function() {
            var $btn = $(this);
            var paperId = $btn.data('paper-id');
            var url = $btn.data('url');
            var $form = $btn.closest('.bp-note-form');
            var $textarea = $form.find('.bp-note-textarea');
            var content = $.trim($textarea.val());
            var color = $form.find('.bp-color-option.active').data('color') || 'yellow';

            if (!content) {
                toastr.warning('Please type a note before saving.');
                return;
            }

            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Saving...');

            $.ajax({
                url: url,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { content: content, color: color },
                success: function(res) {
                    if (res.success && res.note) {
                        // Remove the "no notes" placeholder for this paper's notes list
                        var $list = $btn.closest('.bp-sidebar-card').find('.bp-notes-list');
                        $list.find('.bp-no-notes').remove();

                        var bgColor = noteColors[res.note.color] || noteColors['yellow'];
                        var safeContent = $('<div>').text(res.note.content).html();

                        var noteHtml =
                            '<div class="bp-note-item" id="bp-note-' + res.note.id + '" style="background:' + bgColor + ';">' +
                                '<button class="bp-note-delete" onclick="bpDeleteNote(' + res.note.id + ')" title="Delete note">' +
                                    '<i class="mdi mdi-close"></i>' +
                                '</button>' +
                                '<div class="pe-4" style="white-space:pre-wrap;">' + safeContent + '</div>' +
                                '<div class="d-flex align-items-center gap-2 mt-1" style="font-size:.72rem; color:#888;">' +
                                    'just now' +
                                '</div>' +
                            '</div>';

                        $list.append(noteHtml);
                        $textarea.val('');
                        toastr.success('Note saved.');
                    } else {
                        toastr.error(res.message || 'Failed to save note.');
                    }
                },
                error: function() {
                    toastr.error('Failed to save note. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i> Save Note');
                }
            });
        });

        /* ══════════════════════════════════════════════
           4. STICKY NOTES — AJAX DELETE
        ══════════════════════════════════════════════ */
        window.bpDeleteNote = function(noteId) {
            Swal.fire({
                title: 'Delete Note?',
                text: 'This note will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/board/portal/notes/' + noteId,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(res) {
                            if (res.success) {
                                var $note = $('#bp-note-' + noteId);
                                var $list = $note.closest('.bp-notes-list');
                                $note.fadeOut(300, function() {
                                    $(this).remove();
                                    // Show empty placeholder if no notes left
                                    if ($list.find('.bp-note-item').length === 0) {
                                        $list.html(
                                            '<div class="bp-no-notes text-muted text-center py-3" style="font-size:.88rem;">' +
                                                '<i class="mdi mdi-note-off-outline d-block mb-1" style="font-size:1.3rem;"></i>' +
                                                'No notes yet' +
                                            '</div>'
                                        );
                                    }
                                });
                                toastr.success('Note deleted.');
                            } else {
                                toastr.error(res.message || 'Failed to delete note.');
                            }
                        },
                        error: function() {
                            toastr.error('Could not delete note. Please try again.');
                        }
                    });
                }
            });
        };

        /* ══════════════════════════════════════════════
           5. ANNEXURE PDF PREVIEW TOGGLE
        ══════════════════════════════════════════════ */
        $(document).on('click', '.bp-toggle-preview', function() {
            var target = $(this).data('target');
            var $preview = $(target);
            $preview.slideToggle(250);
            var $icon = $(this).find('i');
            if ($icon.hasClass('mdi-eye-outline')) {
                $icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
            } else {
                $icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
            }
        });

        /* ══════════════════════════════════════════════
           6. AUTO-SCROLL TO FIRST OPEN ACCORDION
        ══════════════════════════════════════════════ */
        $(document).on('shown.bs.collapse', '.bp-accordion .accordion-collapse', function() {
            var $el = $(this);
            // Small delay to let animation finish
            setTimeout(function() {
                var offset = $el.closest('.accordion-item').offset();
                if (offset) {
                    $('html, body').animate({
                        scrollTop: offset.top - 120
                    }, 300);
                }
            }, 150);
        });

    })(jQuery);
    </script>

@endsection
