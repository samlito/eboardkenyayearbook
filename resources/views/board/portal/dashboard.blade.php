@extends('admin_dashboard')

@section('admin')

@push('styles')
<style>
    /* Board Portal Dashboard — clean, logo-balanced (black / red / green on white) */
    :root {
        --bp-navy: #1a1a1a;
        --bp-navy-soft: #f1f2f3;
        --bp-green: #23803a;
        --bp-green-soft: #e9f6eb;
        --bp-red: #cc1f2b;
        --bp-red-soft: #fdeced;
        --bp-orange: #b45309;
        --bp-orange-soft: #fef6e4;
        --bp-purple: #1a1a1a;
        --bp-ink: #111827;
        --bp-body: #374151;
        --bp-muted: #6b7280;
        --bp-line: #eef0f2;
        --bp-soft: #f7f8f9;
        --bp-radius: 14px;
    }

    .bp-dashboard { font-size: 15px; padding-bottom: 32px; }

    /* ---- Shared card look ---- */
    .bp-hero, .bp-stat-card, .bp-calendar-section, .bp-card, .bp-countdown-box, .bp-profile-card {
        background: #fff;
        border: 1px solid rgba(17, 17, 17, .06) !important;
        border-radius: var(--bp-radius) !important;
        box-shadow: 0 1px 2px rgba(17, 17, 17, .04) !important;
    }
    .bp-card { overflow: hidden; margin-bottom: 1.25rem !important; }
    .bp-card .card-header { background: #fff !important; border-bottom: 1px solid var(--bp-line) !important; padding: 16px 20px !important; box-shadow: none; }
    .bp-card .card-body { padding: 8px 20px 16px !important; }

    .bp-section-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .bp-section-title { display: flex; align-items: center; gap: 8px; margin: 0; font-size: 15px !important; font-weight: 700 !important; color: var(--bp-ink) !important; padding-left: 0 !important; }
    .bp-section-title::before { display: none !important; }
    .bp-section-title i { width: 30px; height: 30px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; background: var(--bp-navy-soft); color: var(--bp-navy); font-size: 16px; }
    .bp-section-link { font-size: 13px; font-weight: 600; color: var(--bp-red); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
    .bp-section-link:hover { color: #a3161f; }

    /* ---- Live meeting alert ---- */
    .bp-alert-banner {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        padding: 14px 18px; margin-bottom: 16px; border-radius: var(--bp-radius);
        background: #fff; border: 1px solid #f5c2c6; border-left: 4px solid var(--bp-red);
    }
    .bp-alert-icon { position: relative; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--bp-red-soft); color: var(--bp-red); font-size: 20px; }
    .bp-alert-icon::after { content: ""; position: absolute; inset: -4px; border-radius: 50%; border: 2px solid var(--bp-red); opacity: 0; animation: bp-ping 1.8s infinite; }
    @keyframes bp-ping { 0% { transform: scale(.85); opacity: .6; } 100% { transform: scale(1.25); opacity: 0; } }
    .bp-alert-text-label { font-size: 11.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--bp-red); }
    .bp-alert-text-title { font-size: 15px; font-weight: 700; color: var(--bp-ink); }
    .bp-alert-join { background: var(--bp-red) !important; border-color: var(--bp-red) !important; color: #fff !important; font-weight: 600 !important; border-radius: 10px !important; }
    .bp-alert-join:hover { background: #a3161f !important; border-color: #a3161f !important; }

    /* ---- Welcome header ---- */
    .bp-hero { position: relative; overflow: hidden; margin-bottom: 20px; }
    .bp-hero::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px;
        background: linear-gradient(180deg, #1a1a1a 0 33.33%, #cc1f2b 33.33% 66.66%, #3ca848 66.66% 100%); }
    .bp-hero-inner { padding: 22px 26px 20px 30px; }
    .bp-hero-avatar, .bp-hero-avatar-fallback { width: 60px; height: 60px; border-radius: 50%; flex-shrink: 0; border: 3px solid #fff; box-shadow: 0 0 0 1px var(--bp-line), 0 4px 12px rgba(17, 17, 17, .08); }
    .bp-hero-avatar { object-fit: cover; }
    .bp-hero-avatar-fallback { display: flex; align-items: center; justify-content: center; background: var(--bp-navy); color: #fff; font-size: 24px; font-weight: 800; }
    .bp-hero-name { font-size: 1.45rem; font-weight: 800; color: var(--bp-ink); letter-spacing: -.02em; line-height: 1.25; }
    .bp-hero-designation { font-size: 13.5px; color: var(--bp-muted); margin-top: 2px; }
    .bp-hero-chip { display: inline-flex; align-items: center; gap: 5px; margin: 0 6px 6px 0; padding: 4px 11px; border-radius: 999px; background: var(--bp-soft); border: 1px solid var(--bp-line); color: var(--bp-body); font-size: 12.5px; font-weight: 500; }
    .bp-hero-chip i { color: var(--bp-green); }
    .bp-hero-clock { font-size: 1.6rem; font-weight: 800; color: var(--bp-ink); letter-spacing: -.01em; font-variant-numeric: tabular-nums; line-height: 1.2; }
    .bp-hero-date { font-size: 13px; font-weight: 500; color: var(--bp-muted); margin-top: 2px; }

    /* ---- Stat cards ---- */
    .bp-stat-card { display: flex; align-items: center; gap: 14px; height: 100%; padding: 16px 18px; text-decoration: none; transition: transform .2s, box-shadow .2s, border-color .2s; }
    .bp-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(17, 17, 17, .07) !important; border-color: rgba(17, 17, 17, .1) !important; }
    .bp-stat-icon { width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 21px; }
    .bp-stat-count { font-size: 1.6rem; font-weight: 800; line-height: 1.1; letter-spacing: -.02em; color: var(--bp-ink) !important; font-variant-numeric: tabular-nums; }
    .bp-stat-label { font-size: 13px; font-weight: 500; color: var(--bp-muted); margin-top: 2px; }
    .bp-stat-active { border-color: #f5c2c6 !important; }
    .bp-stat-active .bp-stat-count { color: var(--bp-red) !important; }

    /* ---- Calendar strip ---- */
    .bp-calendar-section { padding: 18px 20px; margin-bottom: 20px; }
    .bp-calendar-strip { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 4px; scroll-snap-type: x mandatory; }
    .bp-cal-card { flex: 0 0 170px; scroll-snap-align: start; padding: 14px; border-radius: 12px; border: 1px solid var(--bp-line); background: var(--bp-soft); transition: border-color .15s, background .15s; }
    .bp-cal-card:hover { background: #fff; border-color: rgba(17, 17, 17, .14); }
    .bp-cal-date { font-size: 1.6rem; font-weight: 800; color: var(--bp-ink); line-height: 1; }
    .bp-cal-day { font-size: 12px; font-weight: 600; color: var(--bp-red); margin-top: 4px; text-transform: uppercase; letter-spacing: .03em; }
    .bp-cal-divider { height: 1px; background: var(--bp-line); margin: 10px 0; }
    .bp-cal-committee { font-size: 13px; font-weight: 600; color: var(--bp-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bp-cal-purpose { font-size: 12.5px; color: var(--bp-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
    .bp-cal-today { background: #fff; border-color: var(--bp-green); box-shadow: inset 0 3px 0 var(--bp-green); }
    .bp-cal-today .bp-cal-day { color: var(--bp-green); }

    /* ---- Notices ---- */
    .bp-notice-row { display: flex; align-items: center; gap: 12px; padding: 13px 0; border-bottom: 1px solid var(--bp-line); }
    .bp-notice-row:last-child { border-bottom: 0; }
    .bp-notice-icon-wrap { width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; align-items: center; justify-content: center; background: var(--bp-navy-soft); color: var(--bp-navy); font-size: 18px; }
    .bp-notice-subject { font-size: 14.5px; font-weight: 600; color: var(--bp-ink); }
    .bp-notice-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 12px; margin-top: 3px; font-size: 12.5px; color: var(--bp-muted); }
    .bp-notice-badge { font-size: 11.5px; font-weight: 600; padding: 2px 8px; border-radius: 999px; background: var(--bp-green-soft); color: var(--bp-green); }
    .bp-notice-btn { flex-shrink: 0; background: #fff !important; color: var(--bp-ink) !important; border: 1px solid var(--bp-line) !important; border-radius: 9px !important; font-weight: 600 !important; }
    .bp-notice-btn:hover { background: var(--bp-navy) !important; border-color: var(--bp-navy) !important; color: #fff !important; }

    /* ---- Quick actions ---- */
    .bp-quick-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding-top: 8px; }
    .bp-quick-btn { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 14px 8px; border-radius: 12px; border: 1px solid var(--bp-line); background: var(--bp-soft); color: var(--bp-body); font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s; }
    .bp-quick-btn i { font-size: 22px; color: var(--bp-navy); transition: color .15s; }
    .bp-quick-btn:hover { background: #fff; border-color: var(--bp-red); color: var(--bp-ink); }
    .bp-quick-btn:hover i { color: var(--bp-red); }

    /* ---- Next meeting countdown ---- */
    .bp-countdown-box { padding: 18px 20px; margin-bottom: 1.25rem; background: var(--bp-navy) !important; border-color: var(--bp-navy) !important; color: #fff; position: relative; overflow: hidden; }
    .bp-countdown-box::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 3px;
        background: linear-gradient(90deg, #1a1a1a 0 33.33%, #cc1f2b 33.33% 66.66%, #3ca848 66.66% 100%); }
    .bp-countdown-label { font-size: 11.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: rgba(255, 255, 255, .6); }
    .bp-countdown-meeting { font-size: 15.5px; font-weight: 700; color: #fff; margin: 4px 0 14px; line-height: 1.35; }
    .bp-countdown-digits { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .bp-countdown-unit { text-align: center; padding: 10px 4px; border-radius: 10px; background: rgba(255, 255, 255, .07); border: 1px solid rgba(255, 255, 255, .08); }
    .bp-countdown-num { font-size: 1.5rem; font-weight: 800; color: #fff; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .bp-countdown-lbl { font-size: 11px; font-weight: 600; color: rgba(255, 255, 255, .6); text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }
    .bp-countdown-meta { margin-top: 14px; font-size: 13px; color: rgba(255, 255, 255, .8); line-height: 1.7; }
    .bp-countdown-meta i { color: #7fd38a; }

    /* ---- Profile completeness ---- */
    .bp-profile-card { padding: 16px 20px; }
    .bp-profile-label { font-size: 14px; font-weight: 600; color: var(--bp-ink); display: inline-flex; align-items: center; gap: 6px; }
    .bp-profile-label i { color: var(--bp-muted); font-size: 17px; }
    .bp-profile-score { font-size: 1.1rem; font-weight: 800; }
    .bp-profile-bar { height: 8px; border-radius: 999px; background: #f1f2f3; overflow: hidden; margin: 10px 0 8px; }
    .bp-profile-bar-fill { height: 100%; border-radius: 999px; transition: width .6s ease; }
    .bp-profile-link { font-size: 13px; font-weight: 600; color: var(--bp-red); text-decoration: none; }
    .bp-profile-link:hover { color: #a3161f; }

    /* ---- Empty ---- */
    .bp-empty { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 28px 16px; text-align: center; font-size: 13.5px; color: #9ca3af; }
    .bp-empty i { font-size: 32px; opacity: .6; }

    /* ---- Responsive ---- */
    @media (max-width: 767.98px) {
        .bp-hero-inner { padding: 18px 16px 16px 20px; }
        .bp-hero-name { font-size: 1.2rem; }
        .bp-hero-clock { font-size: 1.25rem; }
        .bp-stat-card { padding: 14px; gap: 10px; }
        .bp-stat-icon { width: 38px; height: 38px; font-size: 18px; }
        .bp-stat-count { font-size: 1.3rem; }
        .bp-calendar-section { padding: 16px; }
        .bp-cal-card { flex-basis: 150px; }
    }
    @media (max-width: 400px) {
        .bp-quick-grid { grid-template-columns: repeat(2, 1fr); }
        .bp-notice-row { flex-wrap: wrap; }
    }
    @media (prefers-reduced-motion: reduce) {
        .bp-alert-icon::after { animation: none; }
        .bp-stat-card, .bp-quick-btn { transition: none; }
    }
</style>
@endpush

<div class="content">
    <div class="container-fluid bp-dashboard">

        {{-- ============================================================
             1. ACTIVE MEETING ALERT BANNER
             ============================================================ --}}
        @if($activeMeetings->count() > 0)
            @foreach($activeMeetings as $activeMeeting)
                <div class="bp-alert-banner">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bp-alert-icon">
                            <i class="mdi mdi-broadcast"></i>
                        </div>
                        <div>
                            <div class="bp-alert-text-label">Meeting in Progress</div>
                            <div class="bp-alert-text-title">{{ $activeMeeting->title }}</div>
                        </div>
                    </div>
                    <a href="{{ route('board.portal.meeting.live', $activeMeeting) }}"
                       class="btn bp-alert-join">
                        <i class="mdi mdi-arrow-right-bold-circle"></i> Join Now
                    </a>
                </div>
            @endforeach
        @endif

        {{-- ============================================================
             2. HERO WELCOME BANNER
             ============================================================ --}}
        <div class="bp-hero">
            <div class="bp-hero-inner">
                <div class="row align-items-center">
                    {{-- Left: avatar + info --}}
                    <div class="col-lg-8 col-md-7">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}"
                                     alt="{{ $member->names }}"
                                     class="bp-hero-avatar">
                            @else
                                <div class="bp-hero-avatar-fallback">
                                    {{ strtoupper(substr($member->names, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="bp-hero-name">
                                    {{ optional($member->assignsalutation)->name }} {{ $member->names }}
                                </div>
                                <div class="bp-hero-designation">
                                    {{ $member->designation }}
                                    @if($member->director_type)
                                        &middot; {{ $member->director_type }}
                                    @endif
                                    @if($member->role_type)
                                        &middot; {{ $member->role_type }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($member->committees && $member->committees->count())
                            <div class="mt-2 d-flex flex-wrap">
                                @foreach($member->committees as $committee)
                                    <span class="bp-hero-chip">
                                        <i class="mdi mdi-account-group" style="font-size:14px;"></i>
                                        {{ $committee->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Right: clock + date --}}
                    <div class="col-lg-4 col-md-5 text-md-end mt-3 mt-md-0">
                        <div class="bp-hero-clock" id="bp-live-clock">--:--:-- --</div>
                        <div class="bp-hero-date" id="bp-live-date"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             3. QUICK STATS ROW
             ============================================================ --}}
        <div class="row g-3 mb-4">
            {{-- Upcoming Meetings --}}
            <div class="col-lg-3 col-md-6 col-6">
                <a href="{{ route('board.portal.calendar') }}" class="bp-stat-card">
                    <div class="bp-stat-icon" style="background:var(--bp-navy-soft);color:var(--bp-navy);">
                        <i class="mdi mdi-calendar-clock"></i>
                    </div>
                    <div>
                        <div class="bp-stat-count" style="color:var(--bp-navy);">
                            {{ $upcomingMeetings->count() }}
                        </div>
                        <div class="bp-stat-label">Upcoming Meetings</div>
                    </div>
                </a>
            </div>

            {{-- Active Meetings --}}
            <div class="col-lg-3 col-md-6 col-6">
                <a href="{{ route('board.portal.active') }}"
                   class="bp-stat-card {{ $activeMeetings->count() > 0 ? 'bp-stat-active' : '' }}">
                    <div class="bp-stat-icon"
                         style="background:{{ $activeMeetings->count() > 0 ? 'var(--bp-red-soft)' : 'var(--bp-orange-soft)' }};color:{{ $activeMeetings->count() > 0 ? 'var(--bp-red)' : 'var(--bp-orange)' }};">
                        <i class="mdi mdi-broadcast"></i>
                    </div>
                    <div>
                        <div class="bp-stat-count"
                             style="color:{{ $activeMeetings->count() > 0 ? 'var(--bp-red)' : 'var(--bp-orange)' }};">
                            {{ $activeMeetings->count() }}
                        </div>
                        <div class="bp-stat-label">Active Meetings</div>
                    </div>
                </a>
            </div>

            {{-- Papers to Review --}}
            <div class="col-lg-3 col-md-6 col-6">
                <a href="{{ route('board.portal.papers') }}" class="bp-stat-card">
                    <div class="bp-stat-icon" style="background:var(--bp-green-soft);color:var(--bp-green);">
                        <i class="mdi mdi-file-document-edit-outline"></i>
                    </div>
                    <div>
                        <div class="bp-stat-count" style="color:var(--bp-green);">
                            {{ $papersToReview }}
                        </div>
                        <div class="bp-stat-label">Papers to Review</div>
                    </div>
                </a>
            </div>

            {{-- Unread Notifications --}}
            <div class="col-lg-3 col-md-6 col-6">
                <a href="{{ route('workflow.notifications') }}" class="bp-stat-card">
                    <div class="bp-stat-icon" style="background:var(--bp-orange-soft);color:var(--bp-orange);">
                        <i class="mdi mdi-bell-ring-outline"></i>
                    </div>
                    <div>
                        <div class="bp-stat-count" style="color:var(--bp-orange);">
                            {{ $unreadCount }}
                        </div>
                        <div class="bp-stat-label">Unread Notifications</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ============================================================
             4. BOARD CALENDAR STRIP
             ============================================================ --}}
        <div class="bp-calendar-section">
            <div class="bp-section-header">
                <h5 class="bp-section-title">
                    <i class="mdi mdi-calendar-month"></i> Board Calendar
                </h5>
                <a href="{{ route('board.portal.calendar') }}" class="bp-section-link">
                    View Full Calendar <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>

            @if($calendarSchedules->count() > 0)
                <div class="bp-calendar-strip">
                    @foreach($calendarSchedules->take(8) as $schedule)
                        @php
                            $schedDate = \Carbon\Carbon::parse($schedule->meeting_date);
                            $isToday = $schedDate->isToday();
                        @endphp
                        <div class="bp-cal-card {{ $isToday ? 'bp-cal-today' : '' }}">
                            <div class="bp-cal-date">{{ $schedDate->format('d') }}</div>
                            <div class="bp-cal-day">
                                {{ $schedDate->format('D') }} &middot; {{ $schedDate->format('M Y') }}
                            </div>
                            <div class="bp-cal-divider"></div>
                            <div class="bp-cal-committee" title="{{ optional($schedule->boardCommittee)->name }}">
                                {{ optional($schedule->boardCommittee)->name ?? 'General' }}
                            </div>
                            <div class="bp-cal-purpose" title="{{ $schedule->purpose }}">
                                {{ $schedule->purpose ?? 'Board Meeting' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bp-empty">
                    <i class="mdi mdi-calendar-blank-outline"></i>
                    No upcoming calendar events scheduled.
                </div>
            @endif
        </div>

        {{-- ============================================================
             5. TWO-COLUMN: NOTICES + RIGHT SIDEBAR
             ============================================================ --}}
        <div class="row g-4 mb-4">

            {{-- LEFT COLUMN: Recent Official Notices --}}
            <div class="col-lg-7">
                <div class="card bp-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="bp-section-title mb-0">
                            <i class="mdi mdi-email-outline"></i> Recent Official Notices
                        </h5>
                        <a href="{{ route('board.portal.notices') }}" class="bp-section-link">
                            View All <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentNotices->count() > 0)
                            @foreach($recentNotices->take(5) as $notice)
                                <div class="bp-notice-row">
                                    <div class="bp-notice-icon-wrap d-none d-sm-flex">
                                        <i class="mdi mdi-file-document-outline"></i>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="bp-notice-subject text-truncate">
                                            {{ $notice->subject }}
                                        </div>
                                        <div class="bp-notice-meta">
                                            @if(optional(optional($notice->meeting)->boardCommittee)->name)
                                                <span class="bp-notice-badge">
                                                    {{ $notice->meeting->boardCommittee->name }}
                                                </span>
                                            @endif
                                            @if($notice->reference_number)
                                                <span>
                                                    <i class="mdi mdi-tag-outline" style="font-size:13px;"></i>
                                                    {{ $notice->reference_number }}
                                                </span>
                                            @endif
                                            @if($notice->sent_at)
                                                <span>
                                                    <i class="mdi mdi-clock-outline" style="font-size:13px;"></i>
                                                    {{ \Carbon\Carbon::parse($notice->sent_at)->format('d M Y, h:i A') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('board.portal.notice.show', $notice) }}"
                                       class="btn btn-sm btn-outline-primary bp-notice-btn">
                                        <i class="mdi mdi-eye-outline"></i> View
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="bp-empty">
                                <i class="mdi mdi-email-off-outline"></i>
                                No recent notices at this time.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Quick Actions + Countdown + Profile --}}
            <div class="col-lg-5">

                {{-- Quick Actions --}}
                <div class="card bp-card">
                    <div class="card-header">
                        <h5 class="bp-section-title mb-0">
                            <i class="mdi mdi-lightning-bolt"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="bp-quick-grid">
                            <a href="{{ route('board.portal.notices') }}" class="bp-quick-btn">
                                <i class="mdi mdi-email-outline"></i>
                                Notices
                            </a>
                            <a href="{{ route('board.portal.active') }}" class="bp-quick-btn">
                                <i class="mdi mdi-presentation"></i>
                                Meetings
                            </a>
                            <a href="{{ route('board.portal.papers') }}" class="bp-quick-btn">
                                <i class="mdi mdi-file-document-edit-outline"></i>
                                Papers
                            </a>
                            <a href="{{ route('board.portal.committees') }}" class="bp-quick-btn">
                                <i class="mdi mdi-account-group-outline"></i>
                                Committees
                            </a>
                            <a href="{{ route('board.portal.calendar') }}" class="bp-quick-btn">
                                <i class="mdi mdi-calendar-month-outline"></i>
                                Calendar
                            </a>
                            <a href="{{ route('board.profile.show', $member->id) }}" class="bp-quick-btn">
                                <i class="mdi mdi-account-circle-outline"></i>
                                Profile
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Next Meeting Countdown --}}
                @if($upcomingMeetings->count() > 0)
                    @php
                        $nextMeeting = $upcomingMeetings->sortBy('meeting_date')->first();
                        $meetingDateTime = \Carbon\Carbon::parse(
                            \Carbon\Carbon::parse($nextMeeting->meeting_date)->format('Y-m-d') . ' ' . ($nextMeeting->meeting_time ?? '09:00')
                        );
                    @endphp
                    <div class="bp-countdown-box" id="bp-countdown-box">
                        <div class="bp-countdown-label">Next Meeting</div>
                        <div class="bp-countdown-meeting">
                            {{ $nextMeeting->title }}
                        </div>
                        <div class="bp-countdown-digits" id="bp-countdown">
                            <div class="bp-countdown-unit">
                                <div class="bp-countdown-num" id="bp-cd-days">--</div>
                                <div class="bp-countdown-lbl">Days</div>
                            </div>
                            <div class="bp-countdown-unit">
                                <div class="bp-countdown-num" id="bp-cd-hours">--</div>
                                <div class="bp-countdown-lbl">Hours</div>
                            </div>
                            <div class="bp-countdown-unit">
                                <div class="bp-countdown-num" id="bp-cd-mins">--</div>
                                <div class="bp-countdown-lbl">Mins</div>
                            </div>
                            <div class="bp-countdown-unit">
                                <div class="bp-countdown-num" id="bp-cd-secs">--</div>
                                <div class="bp-countdown-lbl">Secs</div>
                            </div>
                        </div>
                        <div class="bp-countdown-meta">
                            <i class="mdi mdi-calendar-clock"></i>
                            {{ $meetingDateTime->format('l, d M Y \\a\\t h:i A') }}
                            @if($nextMeeting->venue)
                                <br>
                                <i class="mdi mdi-map-marker-outline"></i>
                                {{ $nextMeeting->venue }}
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Profile Completeness --}}
                <div class="bp-profile-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="bp-profile-label">
                            <i class="mdi mdi-account-check-outline"></i> Profile Completeness
                        </span>
                        <span class="bp-profile-score"
                              style="color:{{ $profileScore >= 80 ? 'var(--bp-green)' : ($profileScore >= 50 ? 'var(--bp-orange)' : 'var(--bp-red)') }};">
                            {{ $profileScore }}%
                        </span>
                    </div>
                    <div class="bp-profile-bar">
                        <div class="bp-profile-bar-fill"
                             style="width:{{ $profileScore }}%;background-color:{{ $profileScore >= 80 ? 'var(--bp-green)' : ($profileScore >= 50 ? 'var(--bp-orange)' : 'var(--bp-red)') }};">
                        </div>
                    </div>
                    @if($profileScore < 100)
                        <a href="{{ route('board.profile.show', $member->id) }}" class="bp-profile-link">
                            Complete your profile <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function($) {
    'use strict';

    /* ==========================================================================
       LIVE CLOCK (12-hour, updates every second)
       ========================================================================== */
    function bpUpdateClock() {
        var now = new Date();

        var h = now.getHours();
        var m = now.getMinutes();
        var s = now.getSeconds();
        var ampm = h >= 12 ? 'PM' : 'AM';
        var h12 = h % 12 || 12;

        var timeStr = String(h12).padStart(2, '0') + ':' +
                      String(m).padStart(2, '0') + ':' +
                      String(s).padStart(2, '0') + ' ' + ampm;

        var $clock = $('#bp-live-clock');
        if ($clock.length) {
            $clock.text(timeStr);
        }

        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var months = ['January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];
        var dateStr = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();

        var $date = $('#bp-live-date');
        if ($date.length) {
            $date.text(dateStr);
        }
    }

    bpUpdateClock();
    setInterval(bpUpdateClock, 1000);

    /* ==========================================================================
       NEXT MEETING COUNTDOWN
       ========================================================================== */
    @if($upcomingMeetings->count() > 0)
    (function() {
        var targetDate = new Date(@json($meetingDateTime->toIso8601String()));

        function bpUpdateCountdown() {
            var now = new Date();
            var diff = targetDate - now;

            if (diff <= 0) {
                $('#bp-cd-days').text('0');
                $('#bp-cd-hours').text('00');
                $('#bp-cd-mins').text('00');
                $('#bp-cd-secs').text('00');
                return;
            }

            var d = Math.floor(diff / (1000 * 60 * 60 * 24));
            var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var s = Math.floor((diff % (1000 * 60)) / 1000);

            $('#bp-cd-days').text(d);
            $('#bp-cd-hours').text(String(h).padStart(2, '0'));
            $('#bp-cd-mins').text(String(m).padStart(2, '0'));
            $('#bp-cd-secs').text(String(s).padStart(2, '0'));
        }

        bpUpdateCountdown();
        setInterval(bpUpdateCountdown, 1000);
    })();
    @endif

})(jQuery);
</script>
@endpush
