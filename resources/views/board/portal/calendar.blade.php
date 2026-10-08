@extends('admin_dashboard')

@section('admin')

@push('styles')
<style>
    /* ===== Board Calendar Styles ===== */
    :root {
        --twwda-navy: {{ setting('primary_color', '#1a1a1a') }};
        --twwda-navy-dark: #9b0c23;
        --twwda-navy-light: #b8000f;
        --twwda-green: #099139;
        --twwda-green-light: #0cb348;
        --twwda-green-soft: #e8f5ed;
        --twwda-navy-soft: #fdecee;
        --twwda-red: #dc3545;
        --twwda-orange: #f27c1a;
        --twwda-radius: 14px;
        --twwda-shadow: 0 6px 20px rgba(0,0,0,.07);
        --twwda-base-font: 15px;
    }

    .twwda-calendar-page {
        font-size: var(--twwda-base-font);
        -webkit-tap-highlight-color: transparent;
        -webkit-text-size-adjust: 100%;
    }

    /* ---- Back Link ---- */
    .twwda-back-link {
        color: var(--twwda-navy);
        text-decoration: none;
        font-weight: 600;
        font-size: .92rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-height: 44px;
        padding: 8px 0;
        transition: color .2s;
    }
    .twwda-back-link:hover {
        color: var(--twwda-navy-dark);
        text-decoration: none;
    }

    /* ---- Page Header ---- */
    .twwda-calendar-header {
        background: linear-gradient(135deg, var(--twwda-navy) 0%, var(--twwda-navy-light) 100%);
        color: #fff;
        border-radius: var(--twwda-radius);
        padding: 28px 30px;
        margin-bottom: 24px;
        box-shadow: var(--twwda-shadow);
        position: relative;
        overflow: hidden;
    }
    .twwda-calendar-header::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
        pointer-events: none;
    }
    .twwda-calendar-header-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .twwda-calendar-header-sub {
        font-size: .95rem;
        opacity: .85;
    }
    .twwda-calendar-header-count {
        background: rgba(255,255,255,.18);
        color: #fff;
        border-radius: 999px;
        padding: 6px 18px;
        font-size: .88rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(4px);
    }

    /* ---- Jump-to-Month Nav ---- */
    .twwda-month-nav {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid #e9ecef;
        padding: 10px 0;
        margin-bottom: 24px;
        margin-left: -12px;
        margin-right: -12px;
        padding-left: 12px;
        padding-right: 12px;
    }
    .twwda-month-nav-inner {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: x mandatory;
    }
    .twwda-month-nav-inner::-webkit-scrollbar {
        height: 4px;
    }
    .twwda-month-nav-inner::-webkit-scrollbar-thumb {
        background: #dd3c49;
        border-radius: 4px;
    }
    .twwda-month-pill {
        flex-shrink: 0;
        scroll-snap-align: start;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 600;
        color: var(--twwda-navy);
        background: var(--twwda-navy-soft);
        text-decoration: none !important;
        border: 2px solid transparent;
        transition: all .2s;
        min-height: 44px;
        white-space: nowrap;
        cursor: pointer;
    }
    .twwda-month-pill:hover {
        background: var(--twwda-navy);
        color: #fff;
        border-color: var(--twwda-navy);
    }
    .twwda-month-pill.twwda-month-pill-current {
        background: var(--twwda-green);
        color: #fff;
        border-color: var(--twwda-green);
    }
    .twwda-month-pill-count {
        background: rgba(255,255,255,.3);
        border-radius: 999px;
        padding: 1px 8px;
        font-size: .72rem;
        font-weight: 700;
    }
    .twwda-month-pill.twwda-month-pill-current .twwda-month-pill-count {
        background: rgba(255,255,255,.3);
    }
    .twwda-month-pill:hover .twwda-month-pill-count {
        background: rgba(255,255,255,.3);
    }

    /* ---- Month Group Section ---- */
    .twwda-month-group {
        margin-bottom: 32px;
        scroll-margin-top: 80px;
    }
    .twwda-month-group-header {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--twwda-navy);
        padding-bottom: 10px;
        margin-bottom: 16px;
        border-bottom: 2px solid var(--twwda-navy);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .twwda-month-group-count {
        background: var(--twwda-navy-soft);
        color: var(--twwda-navy);
        border-radius: 999px;
        padding: 2px 10px;
        font-size: .75rem;
        font-weight: 700;
        margin-left: auto;
    }

    /* ---- Schedule Card ---- */
    .twwda-schedule-card {
        background: #fff;
        border-radius: var(--twwda-radius);
        box-shadow: var(--twwda-shadow);
        border: 0;
        border-left: 4px solid var(--twwda-navy);
        padding: 16px;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }
    .twwda-schedule-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 26px rgba(0,0,0,.1);
    }

    /* Today card */
    .twwda-schedule-card.twwda-schedule-today {
        border-left-color: var(--twwda-green);
        background: var(--twwda-green-soft);
        box-shadow: 0 4px 16px rgba(9,145,57,.12);
    }

    /* Past card */
    .twwda-schedule-card.twwda-schedule-past {
        opacity: .6;
        border-left-color: #ced4da;
    }
    .twwda-schedule-card.twwda-schedule-past:hover {
        opacity: .8;
    }

    /* Date block (left side) */
    .twwda-date-block {
        width: 68px;
        min-height: 68px;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--twwda-navy);
        color: #fff;
        text-align: center;
        padding: 8px 4px;
    }
    .twwda-date-block-num {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
    }
    .twwda-date-block-day {
        font-size: .7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-top: 2px;
    }

    /* Today date block */
    .twwda-schedule-today .twwda-date-block {
        background: var(--twwda-green);
        box-shadow: 0 4px 12px rgba(9,145,57,.3);
    }
    /* Past date block */
    .twwda-schedule-past .twwda-date-block {
        background: #adb5bd;
    }

    /* Card content (right side) */
    .twwda-schedule-content {
        flex: 1;
        min-width: 0;
    }
    .twwda-schedule-top-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 6px;
    }
    .twwda-committee-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--twwda-navy-soft);
        color: var(--twwda-navy);
        border-radius: 999px;
        padding: 3px 10px;
        font-weight: 600;
        font-size: .78rem;
        white-space: nowrap;
    }
    .twwda-schedule-today .twwda-committee-badge {
        background: rgba(9,145,57,.12);
        color: #066e29;
    }
    .twwda-today-badge {
        background: var(--twwda-green);
        color: #fff;
        border-radius: 999px;
        padding: 2px 10px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .twwda-past-badge {
        background: #e9ecef;
        color: #6c757d;
        border-radius: 999px;
        padding: 2px 10px;
        font-size: .72rem;
        font-weight: 600;
    }
    .twwda-schedule-purpose {
        font-weight: 600;
        font-size: .95rem;
        color: #212529;
        margin-bottom: 6px;
        line-height: 1.35;
    }
    .twwda-schedule-meta {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .twwda-schedule-meta-item {
        font-size: .82rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 1.4;
    }
    .twwda-schedule-meta-item i {
        font-size: 14px;
        flex-shrink: 0;
        width: 18px;
        text-align: center;
    }
    .twwda-fy-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #fef4e8;
        color: var(--twwda-orange);
        border-radius: 999px;
        padding: 2px 8px;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ---- Empty State ---- */
    .twwda-empty-state {
        text-align: center;
        padding: 60px 24px;
    }
    .twwda-empty-state-icon {
        font-size: 72px;
        color: #ced4da;
        display: block;
        margin-bottom: 16px;
    }
    .twwda-empty-state h5 {
        font-weight: 700;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .twwda-empty-state p {
        color: #9aa5b4;
        max-width: 400px;
        margin: 0 auto 20px;
        font-size: .92rem;
    }
    .twwda-empty-state .btn {
        min-height: 44px;
        font-weight: 600;
    }

    /* ---- Responsive / iPad ---- */
    @media (max-width: 991.98px) {
        .twwda-calendar-header {
            padding: 22px 20px;
        }
        .twwda-calendar-header-title {
            font-size: 1.3rem;
        }
        .twwda-date-block {
            width: 60px;
            min-height: 60px;
        }
        .twwda-date-block-num {
            font-size: 1.4rem;
        }
    }
    @media (max-width: 767.98px) {
        .twwda-calendar-header {
            padding: 18px 16px;
        }
        .twwda-calendar-header-title {
            font-size: 1.15rem;
        }
        .twwda-date-block {
            width: 54px;
            min-height: 54px;
        }
        .twwda-date-block-num {
            font-size: 1.25rem;
        }
        .twwda-schedule-card {
            padding: 14px;
            gap: 12px;
        }
        .twwda-month-nav {
            margin-left: -8px;
            margin-right: -8px;
            padding-left: 8px;
            padding-right: 8px;
        }
    }
</style>
@endpush

@php
    use Carbon\Carbon;

    $today = Carbon::today();

    // Group schedules by month
    $grouped = $schedules->groupBy(function($s) {
        return Carbon::parse($s->meeting_date)->format('Y-m');
    })->sortKeys();

    // Determine the current month key for highlighting the nav pill
    $currentMonthKey = $today->format('Y-m');
@endphp

<div class="content">
    <div class="container-fluid twwda-calendar-page">

        {{-- ============================================================ --}}
        {{-- BACK LINK --}}
        {{-- ============================================================ --}}
        <div class="mb-3">
            <a href="{{ route('board.portal.dashboard') }}" class="twwda-back-link">
                <i class="mdi mdi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================================ --}}
        <div class="twwda-calendar-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="twwda-calendar-header-title">
                        <i class="mdi mdi-calendar-month me-1"></i> Board Calendar
                    </div>
                    <div class="twwda-calendar-header-sub">
                        Schedule overview for {{ optional($member->assignsalutation)->name }} {{ $member->names }}
                    </div>
                </div>
                <div>
                    <span class="twwda-calendar-header-count">
                        <i class="mdi mdi-calendar-check"></i>
                        {{ $schedules->count() }} {{ Str::plural('Schedule', $schedules->count()) }}
                    </span>
                </div>
            </div>
        </div>

        @if($schedules->isEmpty())
            {{-- ============================================================ --}}
            {{-- EMPTY STATE --}}
            {{-- ============================================================ --}}
            <div class="card" style="border:0;border-radius:var(--twwda-radius);box-shadow:var(--twwda-shadow);">
                <div class="card-body twwda-empty-state">
                    <i class="mdi mdi-calendar-blank-outline twwda-empty-state-icon"></i>
                    <h5>No Scheduled Meetings</h5>
                    <p>The board almanac calendar is currently empty. Schedules will appear here once they are published.</p>
                    <a href="{{ route('board.portal.dashboard') }}"
                       class="btn btn-sm rounded-pill px-4"
                       style="background:var(--twwda-navy);color:#fff;">
                        <i class="mdi mdi-arrow-left me-1"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @else
            {{-- ============================================================ --}}
            {{-- JUMP-TO-MONTH STICKY NAV --}}
            {{-- ============================================================ --}}
            @if($grouped->count() > 1)
                <div class="twwda-month-nav">
                    <div class="twwda-month-nav-inner">
                        @foreach($grouped as $monthKey => $monthSchedules)
                            @php
                                $monthLabel = Carbon::createFromFormat('Y-m', $monthKey)->format('M Y');
                                $isCurrent = ($monthKey === $currentMonthKey);
                            @endphp
                            <a href="#month-{{ $monthKey }}"
                               class="twwda-month-pill {{ $isCurrent ? 'twwda-month-pill-current' : '' }}">
                                {{ $monthLabel }}
                                <span class="twwda-month-pill-count">{{ $monthSchedules->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- TIMELINE: GROUPED BY MONTH --}}
            {{-- ============================================================ --}}
            @foreach($grouped as $monthKey => $monthSchedules)
                @php
                    $monthLabel = Carbon::createFromFormat('Y-m', $monthKey)->format('F Y');
                    $sortedSchedules = $monthSchedules->sortBy(function($s) {
                        return Carbon::parse($s->meeting_date)->timestamp;
                    });
                @endphp

                <div class="twwda-month-group" id="month-{{ $monthKey }}">
                    {{-- Month Header --}}
                    <div class="twwda-month-group-header">
                        <i class="mdi mdi-calendar-month"></i>
                        {{ $monthLabel }}
                        <span class="twwda-month-group-count">
                            {{ $monthSchedules->count() }} {{ Str::plural('meeting', $monthSchedules->count()) }}
                        </span>
                    </div>

                    {{-- Schedule Cards Grid --}}
                    <div class="row g-3">
                        @foreach($sortedSchedules as $schedule)
                            @php
                                $meetDate = Carbon::parse($schedule->meeting_date);
                                $isToday = $meetDate->isSameDay($today);
                                $isPast = $meetDate->lt($today) && !$isToday;

                                $cardClass = '';
                                if ($isToday) {
                                    $cardClass = 'twwda-schedule-today';
                                } elseif ($isPast) {
                                    $cardClass = 'twwda-schedule-past';
                                }

                                $committeeName = optional($schedule->boardCommittee)->name ?? 'Board';
                                $almanacTitle = optional($schedule->almanac)->title;
                                $financialYear = optional($schedule->almanac)->financial_year;
                            @endphp

                            <div class="col-md-6 col-12">
                                <div class="twwda-schedule-card {{ $cardClass }}">
                                    {{-- Date Block (Left) --}}
                                    <div class="twwda-date-block">
                                        <div class="twwda-date-block-num">{{ $meetDate->format('d') }}</div>
                                        <div class="twwda-date-block-day">{{ $meetDate->format('D') }}</div>
                                    </div>

                                    {{-- Content (Right) --}}
                                    <div class="twwda-schedule-content">
                                        {{-- Top row: badges --}}
                                        <div class="twwda-schedule-top-row">
                                            <span class="twwda-committee-badge">
                                                <i class="mdi mdi-account-group-outline"></i>
                                                {{ $committeeName }}
                                            </span>
                                            @if($isToday)
                                                <span class="twwda-today-badge">
                                                    <i class="mdi mdi-circle-medium"></i> Today
                                                </span>
                                            @elseif($isPast)
                                                <span class="twwda-past-badge">Past</span>
                                            @endif
                                        </div>

                                        {{-- Purpose / Title --}}
                                        <div class="twwda-schedule-purpose">
                                            {{ $schedule->purpose ?? 'Board Meeting' }}
                                        </div>

                                        {{-- Meta details --}}
                                        <div class="twwda-schedule-meta">
                                            {{-- Venue --}}
                                            @if($schedule->venue)
                                                <div class="twwda-schedule-meta-item">
                                                    <i class="mdi mdi-map-marker-outline"></i>
                                                    <span>{{ $schedule->venue }}</span>
                                                </div>
                                            @endif

                                            {{-- Time range --}}
                                            @if($schedule->start_time || $schedule->end_time)
                                                <div class="twwda-schedule-meta-item">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    <span>
                                                        @if($schedule->start_time && $schedule->end_time)
                                                            {{ Carbon::parse($schedule->start_time)->format('g:i A') }}
                                                            &ndash;
                                                            {{ Carbon::parse($schedule->end_time)->format('g:i A') }}
                                                        @elseif($schedule->start_time)
                                                            {{ Carbon::parse($schedule->start_time)->format('g:i A') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Financial Year / Almanac --}}
                                            @if($financialYear || $almanacTitle)
                                                <div class="twwda-schedule-meta-item">
                                                    <i class="mdi mdi-book-open-variant-outline"></i>
                                                    <span>
                                                        @if($financialYear)
                                                            <span class="twwda-fy-badge">
                                                                <i class="mdi mdi-calendar-range"></i> FY {{ $financialYear }}
                                                            </span>
                                                        @endif
                                                        @if($almanacTitle)
                                                            {{ $almanacTitle }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // ========== SMOOTH SCROLL FOR MONTH PILLS ==========
    var monthPills = document.querySelectorAll('.twwda-month-pill');
    monthPills.forEach(function(pill) {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('href');
            var targetEl = document.querySelector(targetId);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

                // Update URL hash without jump
                if (history.replaceState) {
                    history.replaceState(null, null, targetId);
                }
            }
        });
    });

    // ========== HIGHLIGHT ACTIVE MONTH PILL ON SCROLL ==========
    var monthGroups = document.querySelectorAll('.twwda-month-group');
    if (monthGroups.length > 0 && monthPills.length > 0) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var activeId = '#' + entry.target.id;
                    monthPills.forEach(function(pill) {
                        if (pill.getAttribute('href') === activeId) {
                            pill.classList.add('twwda-month-pill-current');
                        } else {
                            pill.classList.remove('twwda-month-pill-current');
                        }
                    });

                    // Scroll the active pill into view within the nav
                    var activePill = document.querySelector('.twwda-month-pill[href="' + activeId + '"]');
                    if (activePill) {
                        activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                }
            });
        }, {
            rootMargin: '-80px 0px -60% 0px',
            threshold: 0
        });

        monthGroups.forEach(function(group) {
            observer.observe(group);
        });
    }
})();
</script>
@endpush
