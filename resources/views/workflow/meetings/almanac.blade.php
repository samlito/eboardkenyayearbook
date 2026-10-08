@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- ═══════════════════════════════════════════════════════════
             PAGE HEADER — Hero-style with almanac info & stats
             ═══════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-body p-0">
                {{-- Top banner --}}
                <div class="px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #212121 0%, {{ setting('primary_color', '#1a1a1a') }} 60%, #b8000f 100%);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('workflow.dashboard') }}"
                               class="d-flex align-items-center justify-content-center rounded-2"
                               style="width: 38px; height: 38px; background: rgba(255,255,255,0.15); color: #fff;" title="Dashboard">
                                <i class="mdi mdi-arrow-left" style="font-size: 18px;"></i>
                            </a>
                            <div>
                                <h4 class="mb-1 text-white fw-bold">Meeting Initiation</h4>
                                <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 13px;">
                                    <i class="mdi mdi-book-open-page-variant-outline me-1"></i>
                                    @if($activeAlmanac)
                                        Almanac: {{ $activeAlmanac->financial_year ?? $activeAlmanac->title ?? 'Current Year' }}
                                        <span class="badge ms-1" style="background: rgba(255,255,255,0.2); font-size: 10.5px;">Active</span>
                                    @else
                                        No Active Almanac
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('workflow.dashboard') }}" class="btn btn-sm text-white" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);">
                                <i class="mdi mdi-view-dashboard-outline me-1"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stat strip --}}
                @if($activeAlmanac)
                <div class="d-flex flex-wrap border-top" style="background: #fafafa;">
                    <div class="flex-fill px-4 py-3 border-end text-center">
                        <div class="fw-bold mb-0" style="font-size: 22px; color: {{ setting('primary_color', '#1a1a1a') }};">{{ $stats['total'] ?? 0 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10.5px; letter-spacing: 0.05em;">Total Scheduled</small>
                    </div>
                    <div class="flex-fill px-4 py-3 border-end text-center">
                        <div class="fw-bold mb-0" style="font-size: 22px; color: #099139;">{{ $stats['initiated'] ?? 0 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10.5px; letter-spacing: 0.05em;">Initiated</small>
                    </div>
                    <div class="flex-fill px-4 py-3 border-end text-center">
                        <div class="fw-bold mb-0" style="font-size: 22px; color: #e6a817;">{{ $stats['pending'] ?? 0 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10.5px; letter-spacing: 0.05em;">Pending</small>
                    </div>
                    <div class="flex-fill px-4 py-3 text-center">
                        <div class="fw-bold mb-0" style="font-size: 22px; color: #c50010;">{{ $stats['by_committee'] ?? 0 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10.5px; letter-spacing: 0.05em;">Committees</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             MAIN CONTENT
             ═══════════════════════════════════════════════════════════ --}}
        @if($activeAlmanac)

            {{-- Committee Cards --}}
            @foreach($schedules as $committeeName => $committeeSchedules)
                @php
                    $committeeInitiated = $committeeSchedules->filter(fn($s) => isset($existingMeetings[$s->id]))->count();
                    $committeeTotal = $committeeSchedules->count();
                    $committeePct = $committeeTotal > 0 ? round(($committeeInitiated / $committeeTotal) * 100) : 0;
                    $committeeBudget = $committeeSchedules->sum('budget');
                    $accordionId = 'committee-' . Str::slug($committeeName);
                @endphp

                <div class="card border-0 shadow-sm mb-3 twwda-committee-card">
                    {{-- Committee Header --}}
                    <div class="card-header p-0 border-0 bg-white" id="heading-{{ $accordionId }}">
                        <button class="btn w-100 text-start p-0 border-0 bg-transparent shadow-none twwda-committee-toggle"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ $accordionId }}"
                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center px-4 py-3">
                                {{-- Committee icon --}}
                                <div class="flex-shrink-0 me-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-2"
                                         style="width: 42px; height: 42px; background: linear-gradient(135deg, #f2f2f2, #e5e5e5);">
                                        <i class="mdi mdi-account-group-outline" style="font-size: 20px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                    </div>
                                </div>

                                {{-- Committee name & progress --}}
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 fw-semibold" style="font-size: 14px; color: #141414;">{{ $committeeName }}</h6>
                                        <span class="badge" style="background: #f2f2f2; color: {{ setting('primary_color', '#1a1a1a') }}; font-size: 11px;">
                                            {{ $committeeTotal }} meeting{{ $committeeTotal !== 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                    {{-- Mini progress bar + budget --}}
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 120px;">
                                                <div style="height: 4px; background: #e9ecef; border-radius: 2px; overflow: hidden;">
                                                    <div style="height: 100%; width: {{ $committeePct }}%; background: {{ $committeePct == 100 ? '#099139' : setting('primary_color', '#1a1a1a') }}; border-radius: 2px; transition: width 0.4s;"></div>
                                                </div>
                                            </div>
                                            <small class="text-muted fw-medium" style="font-size: 11px;">{{ $committeeInitiated }}/{{ $committeeTotal }} initiated</small>
                                        </div>
                                        @if($committeeBudget > 0)
                                        <small class="fw-semibold" style="font-size: 11px; color: #099139;">
                                            <i class="mdi mdi-cash me-1"></i>KES {{ number_format($committeeBudget, 0) }}
                                        </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Chevron --}}
                                <div class="flex-shrink-0 ms-3">
                                    <i class="mdi mdi-chevron-down twwda-committee-chevron text-muted" style="font-size: 22px; transition: transform 0.25s;"></i>
                                </div>
                            </div>
                        </button>
                    </div>

                    {{-- Committee Schedules --}}
                    <div id="collapse-{{ $accordionId }}"
                         class="collapse {{ $loop->first ? 'show' : '' }}">
                        <div class="card-body px-4 py-3 border-top" style="background: #f9fafb;">
                            @foreach($committeeSchedules as $index => $schedule)
                                @php
                                    $existingMeeting = $existingMeetings[$schedule->id] ?? null;
                                    $canInit = $canInitiate[$schedule->id] ?? false;
                                    $daysLeft = now()->diffInDays($schedule->meeting_date, false);
                                    $isPast = $daysLeft < 0;
                                    $isUrgent = !$isPast && $daysLeft <= 7;
                                    $meetingDate = \Carbon\Carbon::parse($schedule->meeting_date);
                                @endphp

                                <div class="almanac-meeting-card mb-3 {{ $existingMeeting ? 'is-initiated' : '' }} {{ $isPast && !$existingMeeting ? 'is-overdue' : '' }}">
                                    <div class="d-flex">
                                        {{-- ── Date Block ─────────────── --}}
                                        <div class="almanac-date-block flex-shrink-0 text-center"
                                             style="width: 72px; padding: 14px 8px;
                                                    {{ $existingMeeting
                                                        ? 'background: linear-gradient(135deg, #099139 0%, #0ba746 100%);'
                                                        : ($isPast
                                                            ? 'background: linear-gradient(135deg, #dc3545 0%, #e85d6a 100%);'
                                                            : ($isUrgent
                                                                ? 'background: linear-gradient(135deg, #e6a817 0%, #f0bc2e 100%);'
                                                                : 'background: linear-gradient(135deg, ' . setting('primary_color', '#1a1a1a') . ' 0%, #b41724 100%);'
                                                            )
                                                        ) }}
                                                    border-radius: 8px 0 0 8px;">
                                            <div class="text-white fw-bold" style="font-size: 24px; line-height: 1;">{{ $meetingDate->format('d') }}</div>
                                            <div class="text-uppercase fw-semibold" style="font-size: 11px; color: rgba(255,255,255,0.9); letter-spacing: 0.05em;">{{ $meetingDate->format('M') }}</div>
                                            <div style="font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 2px;">{{ $meetingDate->format('Y') }}</div>
                                            <div style="font-size: 10px; color: rgba(255,255,255,0.7); margin-top: 1px;">{{ $meetingDate->format('D') }}</div>
                                        </div>

                                        {{-- ── Card Body ──────────────── --}}
                                        <div class="flex-grow-1 d-flex flex-column" style="min-width: 0; background: #fff; border-radius: 0 8px 8px 0; border: 1px solid #e5e9ef; border-left: none;">
                                            {{-- Top section: title + status --}}
                                            <div class="d-flex align-items-start justify-content-between px-3 pt-3 pb-2">
                                                <div class="flex-grow-1 min-w-0">
                                                    {{-- Meeting title --}}
                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                        <span class="fw-bold" style="font-size: 15px; color: #0f0f0f;">
                                                            Meeting #{{ $index + 1 }}
                                                        </span>
                                                        <span class="badge" style="background: #f2f2f2; color: {{ setting('primary_color', '#1a1a1a') }}; font-size: 11px; font-weight: 600; padding: 3px 9px;">
                                                            {{ $committeeName }}
                                                        </span>
                                                        @if($existingMeeting)
                                                            <span class="badge" style="background: #e6f7ed; color: #099139; font-size: 11px; border: 1px solid #b8e6c8; padding: 3px 9px;">
                                                                <i class="mdi mdi-check-circle me-1" style="font-size: 11px;"></i>Initiated
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Purpose / Agenda --}}
                                                    @if($schedule->purpose)
                                                    <p class="mb-0" style="font-size: 13.5px; color: #3d4852; line-height: 1.55;">
                                                        {{ Str::limit($schedule->purpose, 180) }}
                                                    </p>
                                                    @else
                                                    <p class="mb-0 fst-italic" style="font-size: 13px; color: #9ca3af;">
                                                        No agenda description provided
                                                    </p>
                                                    @endif
                                                </div>

                                                {{-- Status badge (top right) --}}
                                                <div class="flex-shrink-0 ms-3 text-end">
                                                    @if($existingMeeting)
                                                        <div class="d-flex flex-column align-items-end gap-1">
                                                            <span class="badge" style="background: #f8f8f8; color: #292929; font-size: 11px; padding: 4px 10px;">
                                                                Stage {{ $existingMeeting->current_stage ?? 1 }}
                                                            </span>
                                                        </div>
                                                    @elseif($isPast)
                                                        <span class="badge" style="background: #fff5f5; color: #c92a2a; font-size: 11px; padding: 4px 10px; border: 1px solid #f5c6cb;">
                                                            <i class="mdi mdi-alert-circle me-1" style="font-size: 11px;"></i>{{ abs($daysLeft) }}d overdue
                                                        </span>
                                                    @elseif($isUrgent)
                                                        <span class="badge" style="background: #fff8dd; color: #946200; font-size: 11px; padding: 4px 10px; border: 1px solid #ffe69c;">
                                                            <i class="mdi mdi-clock-alert me-1" style="font-size: 11px;"></i>{{ $daysLeft }}d left
                                                        </span>
                                                    @else
                                                        <span class="badge" style="background: #f5f6f8; color: #555e68; font-size: 11px; padding: 4px 10px;">
                                                            <i class="mdi mdi-clock-outline me-1" style="font-size: 11px;"></i>{{ $daysLeft }}d away
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Bottom bar: meta details + action --}}
                                            <div class="d-flex align-items-center justify-content-between px-3 py-2 mt-auto" style="background: #fbfbfb; border-top: 1px solid #f0f2f5; border-radius: 0 0 8px 0;">
                                                <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size: 12.5px;">
                                                    {{-- Full date --}}
                                                    <span class="d-flex align-items-center" style="color: #4a5568;">
                                                        <i class="mdi mdi-calendar-month-outline me-1" style="font-size: 14px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                                        {{ $meetingDate->format('l, d F Y') }}
                                                    </span>
                                                    {{-- Venue --}}
                                                    <span class="d-flex align-items-center" style="color: #4a5568;">
                                                        <i class="mdi mdi-map-marker-outline me-1" style="font-size: 14px; color: #0f9f41;"></i>
                                                        {{ $schedule->venue ?? 'TBD' }}
                                                    </span>
                                                    {{-- Budget --}}
                                                    @if($schedule->budget)
                                                    <span class="d-flex align-items-center fw-semibold" style="color: #077a2e;">
                                                        <i class="mdi mdi-cash me-1" style="font-size: 14px;"></i>
                                                        KES {{ number_format($schedule->budget, 0) }}
                                                    </span>
                                                    @endif
                                                    {{-- Addition to schedule --}}
                                                    @if($schedule->addition_to_schedule)
                                                    <span class="badge" style="background: #fff3cd; color: #744210; font-size: 11px; padding: 3px 7px;">
                                                        <i class="mdi mdi-plus-circle-outline me-1"></i>Added
                                                    </span>
                                                    @endif
                                                </div>

                                                {{-- Action button --}}
                                                <div class="flex-shrink-0 ms-2">
                                                    @if($existingMeeting)
                                                        <a href="{{ route('workflow.meetings.show', $existingMeeting->id) }}"
                                                           class="btn btn-sm d-flex align-items-center gap-1 almanac-btn-view">
                                                            <i class="mdi mdi-eye-outline" style="font-size: 14px;"></i>
                                                            <span>View Meeting</span>
                                                        </a>
                                                    @elseif($canInit)
                                                        <form action="{{ route('workflow.meetings.initiate', $schedule->id) }}" method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Initiate meeting for {{ $committeeName }} on {{ $meetingDate->format('d M Y') }}?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm d-flex align-items-center gap-1 almanac-btn-initiate">
                                                                <i class="mdi mdi-rocket-launch-outline" style="font-size: 14px;"></i>
                                                                <span>Initiate</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="d-flex align-items-center gap-1 almanac-btn-locked">
                                                            <i class="mdi mdi-lock-outline" style="font-size: 14px;"></i>
                                                            <span>No access</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

        @else
            {{-- ═══ Empty State ═══ --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                             style="width: 80px; height: 80px; background: #f5f6f8;">
                            <i class="mdi mdi-book-open-blank-variant" style="font-size: 36px; color: #a0a8b4;"></i>
                        </div>
                    </div>
                    <h5 class="mb-2 fw-semibold" style="color: #141414;">No Active Almanac Found</h5>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 380px; font-size: 13px;">
                        There is no active almanac schedule for the current financial year.
                        Contact the administrator to create or activate one.
                    </p>
                    <a href="{{ route('workflow.dashboard') }}" class="btn btn-sm" style="background: {{ setting('primary_color', '#1a1a1a') }}; color: #fff; padding: 8px 24px; border-radius: 6px;">
                        <i class="mdi mdi-arrow-left me-1"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Almanac Page Styles --}}
<style>
    /* ── Committee Card ───────────────────────────────────────────── */
    .twwda-committee-card {
        border-radius: 10px !important;
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .twwda-committee-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.07) !important;
    }

    /* Chevron rotation */
    .twwda-committee-toggle[aria-expanded="true"] .twwda-committee-chevron {
        transform: rotate(180deg);
    }
    .twwda-committee-toggle:focus { box-shadow: none !important; }

    /* ── Meeting Mini-Card ────────────────────────────────────────── */
    .almanac-meeting-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s, transform 0.15s;
    }
    .almanac-meeting-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transform: translateY(-1px);
    }
    .almanac-meeting-card:last-child {
        margin-bottom: 0 !important;
    }

    /* Overdue left-border highlight */
    .almanac-meeting-card.is-overdue {
        box-shadow: 0 1px 4px rgba(220,53,69,0.12);
    }
    .almanac-meeting-card.is-initiated {
        box-shadow: 0 1px 4px rgba(9,145,57,0.10);
    }

    /* ── Action Buttons ───────────────────────────────────────────── */
    .almanac-btn-view {
        background: {{ setting('primary_color', '#1a1a1a') }};
        color: #fff;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: 6px;
        white-space: nowrap;
        font-weight: 500;
        transition: background 0.15s;
    }
    .almanac-btn-view:hover {
        background: #2b2b2b;
        color: #fff;
    }
    .almanac-btn-initiate {
        background: #099139;
        color: #fff;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: 6px;
        white-space: nowrap;
        font-weight: 500;
        transition: background 0.15s;
    }
    .almanac-btn-initiate:hover {
        background: #077a2e;
        color: #fff;
    }
    .almanac-btn-locked {
        font-size: 12.5px;
        color: #8b95a1;
        padding: 6px 10px;
    }

    /* ── Date Block Responsive ────────────────────────────────────── */
    @media (max-width: 575.98px) {
        .almanac-date-block {
            width: 56px !important;
            padding: 10px 6px !important;
        }
        .almanac-date-block .fw-bold {
            font-size: 18px !important;
        }
    }
</style>

@endsection
