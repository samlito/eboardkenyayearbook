@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- ═══════════════════════════════════════════════════════════
             WELCOME BANNER — Modern glassmorphism hero
             ═══════════════════════════════════════════════════════════ --}}
        <div class="twwda-hero-banner mb-4">
            <div class="twwda-hero-bg"></div>
            <div class="twwda-hero-content">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="twwda-hero-greeting mb-1">
                            @php
                                $hour = now()->format('H');
                                $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                            @endphp
                            {{ $greeting }},
                        </p>
                        <h3 class="twwda-hero-name mb-2">{{ Auth::user()->name }}</h3>
                        <div class="twwda-hero-meta">
                            <span class="twwda-hero-badge">
                                <i class="mdi mdi-shield-account-outline"></i>
                                {{ Auth::user()->roles->pluck('name')->implode(', ') ?? 'User' }}
                            </span>
                            @if($userDivision)
                                <span class="twwda-hero-badge">
                                    <i class="mdi mdi-office-building-outline"></i>
                                    {{ $userDivision->name }}
                                </span>
                            @endif
                            @if($userDirectorate)
                                <span class="twwda-hero-badge">
                                    <i class="mdi mdi-domain"></i>
                                    {{ $userDirectorate->name }}
                                </span>
                            @endif
                            <span class="twwda-hero-badge">
                                <i class="mdi mdi-calendar-outline"></i>
                                {{ now()->format('l, d F Y') }}
                            </span>
                        </div>
                        {{-- Reporting line indicator --}}
                        <div class="twwda-hero-reporting mt-2">
                            @if($reportsDirectlyToCeo)
                                <span class="twwda-reporting-badge twwda-reporting-ceo">
                                    <i class="mdi mdi-arrow-up-bold-circle-outline"></i>
                                    Reports directly to <strong>CEO</strong>
                                </span>
                            @elseif($userDirector)
                                <span class="twwda-reporting-badge twwda-reporting-director">
                                    <i class="mdi mdi-account-tie-outline"></i>
                                    Reports to <strong>{{ $userDirector->name }}</strong>
                                    <span class="twwda-reporting-via">— {{ $userDirectorate->name }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        @if($isCeo && $ceoPending['total'] > 0)
                            <button class="twwda-hero-btn twwda-hero-btn-alert" onclick="showPendingActions()">
                                <span class="twwda-pulse-dot"></span>
                                <i class="mdi mdi-alert-circle-outline"></i>
                                <span>{{ $ceoPending['total'] }} Pending</span>
                            </button>
                        @endif
                        @if($isDirector && $directorPending['total'] > 0)
                            <a href="{{ route('workflow.director.papers') }}" class="twwda-hero-btn twwda-hero-btn-alert">
                                <span class="twwda-pulse-dot"></span>
                                <i class="mdi mdi-file-eye-outline"></i>
                                <span>{{ $directorPending['total'] }} {{ Str::plural('Paper', $directorPending['total']) }} to Review</span>
                            </a>
                        @endif
                        <a href="{{ route('workflow.meetings.almanac') }}" class="twwda-hero-btn">
                            <i class="mdi mdi-rocket-launch-outline"></i>
                            <span>New Meeting</span>
                        </a>
                        <a href="{{ route('workflow.notifications') }}" class="twwda-hero-btn twwda-hero-btn-icon position-relative">
                            <i class="mdi mdi-bell-outline"></i>
                            @if($unreadCount > 0)
                                <span class="twwda-notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             KEY METRICS — 6 modern glass cards with animated counters
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">
            @php
                $metrics = [
                    ['value' => array_sum($meetingCounts), 'label' => 'Total Meetings', 'icon' => 'mdi-calendar-multiple-check', 'tone' => 'dark', 'route' => 'workflow.meetings.almanac'],
                    ['value' => $upcomingMeetings->count(), 'label' => 'Upcoming', 'icon' => 'mdi-calendar-clock', 'tone' => 'green', 'route' => 'workflow.meetings.almanac'],
                    ['value' => $totalPapers, 'label' => 'Board Papers', 'icon' => 'mdi-file-document-multiple', 'tone' => 'dark', 'route' => null],
                    ['value' => array_sum($noticeCounts), 'label' => 'Internal Notices', 'icon' => 'mdi-file-sign', 'tone' => 'amber', 'route' => null],
                    ['value' => $assignmentStats['total'], 'label' => 'Assignments', 'icon' => 'mdi-account-group', 'tone' => 'dark', 'route' => null],
                    ['value' => array_sum($boardNoticeCounts), 'label' => 'Board Notices', 'icon' => 'mdi-bell-ring', 'tone' => 'red', 'route' => null],
                ];
            @endphp
            @foreach($metrics as $i => $m)
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="twwda-glass-card" data-aos-delay="{{ $i * 80 }}"
                     @if($m['route']) onclick="window.location='{{ route($m['route']) }}'" style="cursor:pointer;" @endif>
                    <div class="twwda-glass-icon tone-{{ $m['tone'] }}">
                        <i class="mdi {{ $m['icon'] }}"></i>
                    </div>
                    <div class="twwda-glass-value" data-count="{{ $m['value'] }}">0</div>
                    <div class="twwda-glass-label">{{ $m['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             CEO PENDING ACTIONS (modern card with SweetAlert)
             ═══════════════════════════════════════════════════════════ --}}
        @if($isCeo && $ceoPending['total'] > 0)
        <div class="twwda-pending-card mb-4">
            <div class="twwda-pending-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="twwda-pending-icon-wrap">
                        <i class="mdi mdi-alert-decagram"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Items Requiring Your Action</h6>
                        <small>{{ $ceoPending['total'] }} {{ Str::plural('item', $ceoPending['total']) }} pending review</small>
                    </div>
                </div>
                <span class="twwda-pending-count-badge">{{ $ceoPending['total'] }}</span>
            </div>
            <div class="twwda-pending-body">
                <div class="twwda-pending-grid">
                    @if($ceoPending['almanacs']->count() > 0)
                    <a href="{{ route('workflow.ceo.almanac') }}" class="twwda-pending-tile twwda-pending-tile-amber">
                        <div class="twwda-pending-tile-icon"><i class="mdi mdi-book-open-page-variant-outline"></i></div>
                        <div class="twwda-pending-tile-count">{{ $ceoPending['almanacs']->count() }}</div>
                        <div class="twwda-pending-tile-label">Almanac {{ Str::plural('Approval', $ceoPending['almanacs']->count()) }}</div>
                    </a>
                    @endif
                    @if($ceoPending['internal_notices']->count() > 0)
                    <a href="{{ route('workflow.ceo.internal-notices') }}" class="twwda-pending-tile twwda-pending-tile-blue">
                        <div class="twwda-pending-tile-icon"><i class="mdi mdi-file-document-check-outline"></i></div>
                        <div class="twwda-pending-tile-count">{{ $ceoPending['internal_notices']->count() }}</div>
                        <div class="twwda-pending-tile-label">Internal {{ Str::plural('Notice', $ceoPending['internal_notices']->count()) }}</div>
                    </a>
                    @endif
                    @if($ceoPending['board_papers']->count() > 0)
                    <a href="{{ route('workflow.ceo.papers') }}" class="twwda-pending-tile twwda-pending-tile-purple">
                        <div class="twwda-pending-tile-icon"><i class="mdi mdi-file-document-outline"></i></div>
                        <div class="twwda-pending-tile-count">{{ $ceoPending['board_papers']->count() }}</div>
                        <div class="twwda-pending-tile-label">Board {{ Str::plural('Paper', $ceoPending['board_papers']->count()) }}</div>
                    </a>
                    @endif
                    @if($ceoPending['board_notices']->count() > 0)
                    <a href="{{ route('workflow.ceo.board-notices') }}" class="twwda-pending-tile twwda-pending-tile-green">
                        <div class="twwda-pending-tile-icon"><i class="mdi mdi-bell-check-outline"></i></div>
                        <div class="twwda-pending-tile-count">{{ $ceoPending['board_notices']->count() }}</div>
                        <div class="twwda-pending-tile-label">Board {{ Str::plural('Notice', $ceoPending['board_notices']->count()) }}</div>
                    </a>
                    @endif
                </div>

                {{-- Detailed pending items --}}
                <div class="twwda-pending-details mt-3">
                    @foreach($ceoPending['almanacs'] as $almanac)
                    <div class="twwda-action-item">
                        <div class="twwda-action-icon twwda-action-icon-amber">
                            <i class="mdi mdi-book-open-page-variant"></i>
                        </div>
                        <div class="twwda-action-info">
                            <span class="twwda-action-title">{{ $almanac->serialno }}</span>
                            <span class="twwda-action-meta">
                                FY {{ $almanac->financialyear->year ?? '-' }}
                                <span class="mx-1">&middot;</span>
                                KES {{ number_format($almanac->budget, 2) }}
                            </span>
                        </div>
                        <a href="{{ route('workflow.ceo.almanac.show', $almanac->id) }}" class="twwda-action-btn">
                            <i class="mdi mdi-eye-outline me-1"></i> Review
                        </a>
                    </div>
                    @endforeach

                    @foreach($ceoPending['internal_notices'] as $notice)
                    <div class="twwda-action-item">
                        <div class="twwda-action-icon twwda-action-icon-blue">
                            <i class="mdi mdi-file-check-outline"></i>
                        </div>
                        <div class="twwda-action-info">
                            <span class="twwda-action-title">{{ Str::limit($notice->subject, 55) }}</span>
                            <span class="twwda-action-meta">
                                {{ $notice->meeting->boardCommittee->name ?? '-' }}
                                @if($notice->submitted_at)
                                    <span class="mx-1">&middot;</span>
                                    {{ \Carbon\Carbon::parse($notice->submitted_at)->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('workflow.ceo.internal-notice.review', $notice->id) }}" class="twwda-action-btn">
                            <i class="mdi mdi-eye-outline me-1"></i> Review
                        </a>
                    </div>
                    @endforeach

                    @foreach($ceoPending['board_papers'] as $paper)
                    <div class="twwda-action-item">
                        <div class="twwda-action-icon twwda-action-icon-purple">
                            <i class="mdi mdi-file-document"></i>
                        </div>
                        <div class="twwda-action-info">
                            <span class="twwda-action-title">{{ Str::limit($paper->title, 55) }}</span>
                            <span class="twwda-action-meta">
                                {{ $paper->meeting->boardCommittee->name ?? '-' }}
                                <span class="mx-1">&middot;</span>
                                by {{ $paper->author->name ?? ($paper->assignment->user->name ?? '-') }}
                            </span>
                        </div>
                        <a href="{{ route('workflow.ceo.paper.review', $paper->id) }}" class="twwda-action-btn">
                            <i class="mdi mdi-eye-outline me-1"></i> Review
                        </a>
                    </div>
                    @endforeach

                    @foreach($ceoPending['board_notices'] as $bNotice)
                    <div class="twwda-action-item">
                        <div class="twwda-action-icon twwda-action-icon-green">
                            <i class="mdi mdi-bell-check"></i>
                        </div>
                        <div class="twwda-action-info">
                            <span class="twwda-action-title">{{ Str::limit($bNotice->subject, 55) }}</span>
                            <span class="twwda-action-meta">
                                {{ $bNotice->meeting->boardCommittee->name ?? '-' }}
                                @if($bNotice->submitted_at)
                                    <span class="mx-1">&middot;</span>
                                    {{ \Carbon\Carbon::parse($bNotice->submitted_at)->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('workflow.ceo.board-notice.review', $bNotice->id) }}" class="twwda-action-btn">
                            <i class="mdi mdi-eye-outline me-1"></i> Review
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @elseif($isCeo)
        <div class="twwda-allclear-card mb-4">
            <div class="twwda-allclear-icon">
                <i class="mdi mdi-check-all"></i>
            </div>
            <h6>All Clear</h6>
            <p>No items pending your approval at this time.</p>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             DIRECTOR PENDING ACTIONS
             ═══════════════════════════════════════════════════════════ --}}
        @if($isDirector && $directorPending['total'] > 0)
        <div class="twwda-director-card mb-4">
            <div class="twwda-director-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="twwda-director-icon-wrap">
                        <i class="mdi mdi-account-tie"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Board Papers Awaiting Your Review</h6>
                        <small>
                            {{ $directorPending['total'] }} {{ Str::plural('paper', $directorPending['total']) }} pending
                            @if($directorPending['directorate'])
                                — {{ $directorPending['directorate']->directorate->name ?? '' }}
                            @endif
                        </small>
                    </div>
                </div>
                <span class="twwda-director-count-badge">{{ $directorPending['total'] }}</span>
            </div>
            <div class="twwda-director-body">
                {{-- Summary tiles --}}
                <div class="twwda-director-grid">
                    <div class="twwda-director-tile twwda-director-tile-amber">
                        <div class="twwda-director-tile-icon"><i class="mdi mdi-clock-alert-outline"></i></div>
                        <div class="twwda-director-tile-count">{{ $directorPending['total'] }}</div>
                        <div class="twwda-director-tile-label">Pending Review</div>
                    </div>
                    <div class="twwda-director-tile twwda-director-tile-green">
                        <div class="twwda-director-tile-icon"><i class="mdi mdi-check-circle-outline"></i></div>
                        <div class="twwda-director-tile-count">{{ $directorPending['approved_count'] }}</div>
                        <div class="twwda-director-tile-label">Approved</div>
                    </div>
                    <div class="twwda-director-tile twwda-director-tile-red">
                        <div class="twwda-director-tile-icon"><i class="mdi mdi-close-circle-outline"></i></div>
                        <div class="twwda-director-tile-count">{{ $directorPending['rejected_count'] }}</div>
                        <div class="twwda-director-tile-label">Rejected</div>
                    </div>
                    <div class="twwda-director-tile twwda-director-tile-blue">
                        <div class="twwda-director-tile-icon"><i class="mdi mdi-account-group-outline"></i></div>
                        <div class="twwda-director-tile-count">{{ $directorPending['total_assignments'] }}</div>
                        <div class="twwda-director-tile-label">Assignments</div>
                    </div>
                </div>

                {{-- Detailed pending papers --}}
                <div class="twwda-director-details mt-3">
                    @foreach($directorPending['pending_papers'] as $paper)
                    <div class="twwda-action-item">
                        <div class="twwda-action-icon twwda-action-icon-amber">
                            <i class="mdi mdi-file-document-edit-outline"></i>
                        </div>
                        <div class="twwda-action-info">
                            <span class="twwda-action-title">{{ Str::limit($paper->title, 55) }}</span>
                            <span class="twwda-action-meta">
                                {{ $paper->meeting->boardCommittee->name ?? '—' }}
                                <span class="mx-1">&middot;</span>
                                by {{ $paper->author->name ?? ($paper->assignment->user->name ?? '—') }}
                                @if($paper->submitted_at)
                                    <span class="mx-1">&middot;</span>
                                    {{ \Carbon\Carbon::parse($paper->submitted_at)->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('workflow.director.paper.show', $paper->id) }}" class="twwda-action-btn">
                            <i class="mdi mdi-eye-outline me-1"></i> Review
                        </a>
                    </div>
                    @endforeach
                </div>

                {{-- View all link --}}
                @if($directorPending['total'] > 5)
                <div class="twwda-view-all-link">
                    <a href="{{ route('workflow.director.papers') }}">
                        View all papers <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @elseif($isDirector)
        <div class="twwda-director-clear-card mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="twwda-director-clear-icon">
                    <i class="mdi mdi-check-decagram"></i>
                </div>
                <div>
                    <h6 class="mb-1">No Papers Pending Review</h6>
                    <p class="mb-0">All board papers under your directorate have been reviewed.</p>
                </div>
                <div class="ms-auto d-flex gap-3">
                    @if($directorPending['approved_count'] > 0 || $directorPending['rejected_count'] > 0)
                    <div class="text-center">
                        <span class="d-block fw-bold text-success" style="font-size: 20px;">{{ $directorPending['approved_count'] }}</span>
                        <small class="text-muted">Approved</small>
                    </div>
                    <div class="text-center">
                        <span class="d-block fw-bold text-danger" style="font-size: 20px;">{{ $directorPending['rejected_count'] }}</span>
                        <small class="text-muted">Rejected</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             WORKFLOW PIPELINE — Modern animated visual
             ═══════════════════════════════════════════════════════════ --}}
        <div class="twwda-modern-card mb-4">
            <div class="twwda-modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="twwda-card-icon">
                        <i class="mdi mdi-chart-timeline-variant-shimmer"></i>
                    </div>
                    <h6 class="mb-0">Workflow Pipeline</h6>
                </div>
                <span class="twwda-card-badge">{{ array_sum($stageCounts) }} Active</span>
            </div>
            <div class="twwda-modern-card-body px-4 py-4">
                @php
                    $stages = [
                        1 => ['label' => 'Initiated', 'icon' => 'mdi-rocket-launch', 'color' => '#1a1a1a'],
                        2 => ['label' => 'Internal Notice', 'icon' => 'mdi-file-sign', 'color' => '#1a1a1a'],
                        3 => ['label' => 'CEO Approval', 'icon' => 'mdi-check-decagram', 'color' => '#1a1a1a'],
                        4 => ['label' => 'Officers Assigned', 'icon' => 'mdi-account-multiple-plus', 'color' => '#1a1a1a'],
                        5 => ['label' => 'Board Papers', 'icon' => 'mdi-file-document-multiple', 'color' => '#1a1a1a'],
                        6 => ['label' => 'Director Review', 'icon' => 'mdi-account-tie', 'color' => '#1a1a1a'],
                        7 => ['label' => 'CEO Review', 'icon' => 'mdi-shield-check', 'color' => '#1a1a1a'],
                        8 => ['label' => 'Preparation', 'icon' => 'mdi-clipboard-check', 'color' => '#1a1a1a'],
                        9 => ['label' => 'Meeting Day', 'icon' => 'mdi-presentation', 'color' => '#1a1a1a'],
                    ];
                @endphp
                <div class="twwda-pipeline-v2">
                    @foreach($stages as $num => $stage)
                        @php $count = $stageCounts[$num] ?? 0; @endphp
                        <div class="twwda-pipe-stage {{ $count > 0 ? 'active' : '' }}"
                             onclick="showStageDetail({{ $num }}, '{{ $stage['label'] }}', {{ $count }}, '{{ $stage['color'] }}')">
                            <div class="twwda-pipe-node" style="{{ $count > 0 ? 'background:'.$stage['color'].'; border-color:'.$stage['color'].'; color:#fff; box-shadow: 0 4px 14px '.$stage['color'].'40;' : '' }}">
                                @if($count > 0)
                                    <span class="twwda-pipe-count">{{ $count }}</span>
                                @else
                                    <i class="mdi {{ $stage['icon'] }}"></i>
                                @endif
                            </div>
                            <span class="twwda-pipe-label">{{ $stage['label'] }}</span>
                            @if($num < 9)
                                <div class="twwda-pipe-line {{ $count > 0 ? 'filled' : '' }}" style="{{ $count > 0 ? 'background:'.$stage['color'].';' : '' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             CHARTS ROW — Meeting Trend + Status Donut + Committee Bar
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">
            {{-- Monthly Meetings Trend --}}
            <div class="col-xl-5 col-lg-6 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-chart-line"></i>
                            </div>
                            <h6 class="mb-0">Meeting Trend</h6>
                        </div>
                        <span class="twwda-card-badge">Last 12 months</span>
                    </div>
                    <div class="twwda-modern-card-body">
                        <div id="meetings-trend-chart" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Meeting Status Donut --}}
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-chart-donut"></i>
                            </div>
                            <h6 class="mb-0">Meeting Status</h6>
                        </div>
                    </div>
                    <div class="twwda-modern-card-body d-flex align-items-center justify-content-center">
                        <div id="meeting-status-chart" style="min-height: 260px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            {{-- Committee Distribution --}}
            <div class="col-xl-4 col-lg-12 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-account-supervisor-circle"></i>
                            </div>
                            <h6 class="mb-0">By Committee</h6>
                        </div>
                    </div>
                    <div class="twwda-modern-card-body">
                        <div id="committee-chart" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             STATUS BREAKDOWN — Papers + Notices side-by-side
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">
            {{-- Board Papers Status --}}
            <div class="col-xl-6 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-file-document-multiple"></i>
                            </div>
                            <h6 class="mb-0">Board Papers</h6>
                        </div>
                        <span class="twwda-card-count">{{ $totalPapers }}</span>
                    </div>
                    <div class="twwda-modern-card-body p-0">
                        @php
                            $paperStatuses = [
                                'draft' => ['label' => 'Draft', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => 'mdi-pencil-outline'],
                                'submitted' => ['label' => 'Submitted', 'color' => '#374151', 'bg' => '#f3f4f6', 'icon' => 'mdi-send-check'],
                                'director_review' => ['label' => 'Director Review', 'color' => '#b45309', 'bg' => '#fffbeb', 'icon' => 'mdi-account-eye'],
                                'director_approved' => ['label' => 'Director Approved', 'color' => '#23803a', 'bg' => '#e9f6eb', 'icon' => 'mdi-check-circle'],
                                'rejected_by_director' => ['label' => 'Rejected (Director)', 'color' => '#cc1f2b', 'bg' => '#fdeced', 'icon' => 'mdi-close-circle'],
                                'ceo_review' => ['label' => 'CEO Review', 'color' => '#92400e', 'bg' => '#fef6e4', 'icon' => 'mdi-shield-account'],
                                'ceo_approved' => ['label' => 'CEO Approved', 'color' => '#19662c', 'bg' => '#e9f6eb', 'icon' => 'mdi-check-decagram'],
                                'rejected_by_ceo' => ['label' => 'Rejected (CEO)', 'color' => '#a3161f', 'bg' => '#fdeced', 'icon' => 'mdi-cancel'],
                            ];
                        @endphp
                        @forelse($paperStatuses as $key => $ps)
                            @if(isset($paperCounts[$key]) && $paperCounts[$key] > 0)
                            <div class="twwda-status-item">
                                <div class="twwda-status-item-icon" style="background: {{ $ps['bg'] }};">
                                    <i class="mdi {{ $ps['icon'] }}" style="color: {{ $ps['color'] }};"></i>
                                </div>
                                <div class="twwda-status-item-info">
                                    <span class="twwda-status-item-label">{{ $ps['label'] }}</span>
                                    <div class="twwda-progress-track">
                                        <div class="twwda-progress-fill" style="width: {{ $totalPapers > 0 ? ($paperCounts[$key] / $totalPapers * 100) : 0 }}%; background: {{ $ps['color'] }};"></div>
                                    </div>
                                </div>
                                <div class="twwda-status-item-count" style="color: {{ $ps['color'] }};">{{ $paperCounts[$key] }}</div>
                            </div>
                            @endif
                        @empty
                        @endforelse
                        @if($totalPapers === 0)
                            <div class="twwda-empty-state">
                                <i class="mdi mdi-file-document-outline"></i>
                                <p>No board papers yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notices & Assignments --}}
            <div class="col-xl-6 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-file-sign"></i>
                            </div>
                            <h6 class="mb-0">Notices & Assignments</h6>
                        </div>
                    </div>
                    <div class="twwda-modern-card-body p-0">
                        {{-- Internal Notices --}}
                        <div class="twwda-subsection">
                            <div class="twwda-subsection-header">
                                <i class="mdi mdi-file-document-check-outline me-1"></i> Internal Notices
                            </div>
                            @php
                                $intStatuses = [
                                    'draft' => ['label' => 'Draft', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
                                    'submitted' => ['label' => 'Pending', 'color' => '#b45309', 'bg' => '#fef6e4'],
                                    'approved' => ['label' => 'Approved', 'color' => '#23803a', 'bg' => '#e9f6eb'],
                                    'special_approved' => ['label' => 'Special', 'color' => '#19662c', 'bg' => '#e9f6eb'],
                                    'rejected' => ['label' => 'Rejected', 'color' => '#cc1f2b', 'bg' => '#fdeced'],
                                ];
                            @endphp
                            <div class="twwda-chip-grid">
                                @foreach($intStatuses as $key => $is)
                                    @if(isset($noticeCounts[$key]) && $noticeCounts[$key] > 0)
                                    <div class="twwda-modern-chip" style="background: {{ $is['bg'] }}; border-color: {{ $is['color'] }}25;">
                                        <span class="twwda-modern-chip-num" style="color: {{ $is['color'] }};">{{ $noticeCounts[$key] }}</span>
                                        <span class="twwda-modern-chip-text" style="color: {{ $is['color'] }};">{{ $is['label'] }}</span>
                                    </div>
                                    @endif
                                @endforeach
                                @if(array_sum($noticeCounts) === 0)
                                    <span class="twwda-no-data-text">No internal notices yet</span>
                                @endif
                            </div>
                        </div>

                        {{-- Board Notices --}}
                        <div class="twwda-subsection">
                            <div class="twwda-subsection-header">
                                <i class="mdi mdi-bell-ring-outline me-1"></i> Board Notices
                            </div>
                            @php
                                $bnStatuses = [
                                    'draft' => ['label' => 'Draft', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
                                    'submitted' => ['label' => 'Submitted', 'color' => '#374151', 'bg' => '#f3f4f6'],
                                    'approved' => ['label' => 'Approved', 'color' => '#23803a', 'bg' => '#e9f6eb'],
                                    'rejected' => ['label' => 'Rejected', 'color' => '#cc1f2b', 'bg' => '#fdeced'],
                                    'sent' => ['label' => 'Sent', 'color' => '#19662c', 'bg' => '#e9f6eb'],
                                ];
                            @endphp
                            <div class="twwda-chip-grid">
                                @foreach($bnStatuses as $key => $bs)
                                    @if(isset($boardNoticeCounts[$key]) && $boardNoticeCounts[$key] > 0)
                                    <div class="twwda-modern-chip" style="background: {{ $bs['bg'] }}; border-color: {{ $bs['color'] }}25;">
                                        <span class="twwda-modern-chip-num" style="color: {{ $bs['color'] }};">{{ $boardNoticeCounts[$key] }}</span>
                                        <span class="twwda-modern-chip-text" style="color: {{ $bs['color'] }};">{{ $bs['label'] }}</span>
                                    </div>
                                    @endif
                                @endforeach
                                @if(array_sum($boardNoticeCounts) === 0)
                                    <span class="twwda-no-data-text">No board notices yet</span>
                                @endif
                            </div>
                        </div>

                        {{-- Officer Assignments --}}
                        <div class="twwda-subsection twwda-subsection-last">
                            <div class="twwda-subsection-header">
                                <i class="mdi mdi-account-group-outline me-1"></i> Officer Assignments
                            </div>
                            <div class="twwda-chip-grid">
                                <div class="twwda-modern-chip" style="background: #f3f4f6; border-color: #11111115;">
                                    <span class="twwda-modern-chip-num" style="color: #1a1a1a;">{{ $assignmentStats['total'] }}</span>
                                    <span class="twwda-modern-chip-text" style="color: #374151;">Total</span>
                                </div>
                                <div class="twwda-modern-chip" style="background: #e9f6eb; border-color: #23803a25;">
                                    <span class="twwda-modern-chip-num" style="color: #23803a;">{{ $assignmentStats['with_papers'] }}</span>
                                    <span class="twwda-modern-chip-text" style="color: #23803a;">With Papers</span>
                                </div>
                                <div class="twwda-modern-chip" style="background: #fef6e4; border-color: #b4530925;">
                                    <span class="twwda-modern-chip-num" style="color: #b45309;">{{ $assignmentStats['total'] - $assignmentStats['with_papers'] }}</span>
                                    <span class="twwda-modern-chip-text" style="color: #b45309;">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             UPCOMING MEETINGS TABLE — Modern card rows
             ═══════════════════════════════════════════════════════════ --}}
        <div class="twwda-modern-card mb-4">
            <div class="twwda-modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="twwda-card-icon">
                        <i class="mdi mdi-calendar-star"></i>
                    </div>
                    <h6 class="mb-0">Upcoming Meetings</h6>
                </div>
                <a href="{{ route('workflow.meetings.almanac') }}" class="twwda-view-all-btn">View All <i class="mdi mdi-arrow-right"></i></a>
            </div>
            <div class="twwda-modern-card-body p-0">
                @if($upcomingMeetings->isEmpty())
                    <div class="twwda-empty-state py-5">
                        <i class="mdi mdi-calendar-blank-outline"></i>
                        <p>No upcoming meetings scheduled</p>
                    </div>
                @else
                    @foreach($upcomingMeetings->take(6) as $meeting)
                        @php $daysLeft = now()->diffInDays($meeting->meeting_date, false); @endphp
                        <div class="twwda-meeting-item" onclick="showMeetingDetail({{ $meeting->id }}, '{{ addslashes($meeting->title) }}', '{{ $meeting->boardCommittee->name ?? '-' }}', {{ $meeting->current_stage }}, '{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}', {{ (int)$daysLeft }})">
                            <div class="twwda-meeting-date-block">
                                <span class="twwda-meeting-day">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d') }}</span>
                                <span class="twwda-meeting-month">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M') }}</span>
                            </div>
                            <div class="twwda-meeting-info">
                                <span class="twwda-meeting-title">{{ $meeting->title }}</span>
                                <div class="twwda-meeting-meta">
                                    <span><i class="mdi mdi-account-supervisor-circle me-1"></i>{{ $meeting->boardCommittee->name ?? '-' }}</span>
                                    <span class="twwda-stage-pill">Stage {{ $meeting->current_stage }}</span>
                                </div>
                            </div>
                            <div class="twwda-meeting-right">
                                @if($daysLeft < 0)
                                    <span class="twwda-days-badge twwda-days-overdue">{{ abs($daysLeft) }}d overdue</span>
                                @elseif($daysLeft <= 3)
                                    <span class="twwda-days-badge twwda-days-urgent">{{ $daysLeft }}d left</span>
                                @elseif($daysLeft <= 7)
                                    <span class="twwda-days-badge twwda-days-soon">{{ $daysLeft }} days</span>
                                @else
                                    <span class="twwda-days-text">{{ $daysLeft }} days</span>
                                @endif
                                <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="twwda-meeting-view-btn" onclick="event.stopPropagation();">
                                    <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             ACTIVITY + NOTIFICATIONS
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">
            {{-- Recent Activity --}}
            <div class="col-xl-8 col-lg-7 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-history"></i>
                            </div>
                            <h6 class="mb-0">Recent Activity</h6>
                        </div>
                    </div>
                    <div class="twwda-modern-card-body p-0">
                        @if($recentActivity->isEmpty())
                            <div class="twwda-empty-state py-5">
                                <i class="mdi mdi-timeline-clock-outline"></i>
                                <p>No recent activity</p>
                            </div>
                        @else
                            <div class="twwda-activity-timeline">
                                @foreach($recentActivity->take(8) as $log)
                                    <div class="twwda-timeline-item">
                                        <div class="twwda-timeline-dot
                                            @switch($log->action ?? '')
                                                @case('created') @case('meeting_initiated') twwda-dot-green @break
                                                @case('approved') @case('stage_advanced') twwda-dot-green @break
                                                @case('rejected') twwda-dot-red @break
                                                @case('submitted') twwda-dot-blue @break
                                                @default twwda-dot-gray
                                            @endswitch">
                                            @switch($log->action ?? '')
                                                @case('created') @case('meeting_initiated') <i class="mdi mdi-plus"></i> @break
                                                @case('approved') @case('stage_advanced') <i class="mdi mdi-check"></i> @break
                                                @case('rejected') <i class="mdi mdi-close"></i> @break
                                                @case('submitted') <i class="mdi mdi-send"></i> @break
                                                @default <i class="mdi mdi-circle-medium"></i>
                                            @endswitch
                                        </div>
                                        <div class="twwda-timeline-content">
                                            <p class="twwda-timeline-text">{{ $log->description }}</p>
                                            <div class="twwda-timeline-meta">
                                                <span><i class="mdi mdi-account-outline me-1"></i>{{ $log->user->name ?? 'System' }}</span>
                                                <span><i class="mdi mdi-clock-outline me-1"></i>{{ $log->created_at->diffForHumans() }}</span>
                                                @if($log->module)
                                                    <span class="twwda-module-tag">{{ $log->module }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="col-xl-4 col-lg-5 mb-3">
                <div class="twwda-modern-card h-100">
                    <div class="twwda-modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="twwda-card-icon">
                                <i class="mdi mdi-bell-ring-outline"></i>
                            </div>
                            <h6 class="mb-0">Notifications</h6>
                            @if($unreadCount > 0)
                                <span class="twwda-notif-count-badge">{{ $unreadCount }}</span>
                            @endif
                        </div>
                        @if($unreadCount > 0)
                            <button type="button" class="twwda-mark-read-btn" onclick="confirmMarkAllRead()">
                                <i class="mdi mdi-check-all me-1"></i> Mark all
                            </button>
                        @endif
                    </div>
                    <div class="twwda-modern-card-body p-0">
                        @if($notifications->isEmpty())
                            <div class="twwda-empty-state py-5">
                                <i class="mdi mdi-bell-check-outline"></i>
                                <p>All caught up!</p>
                            </div>
                        @else
                            @foreach($notifications->take(8) as $notification)
                                <a href="{{ $notification->link ?? '#' }}"
                                   class="twwda-notif-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                    <div class="twwda-notif-icon-wrap">
                                        <i class="mdi {{ $notification->icon ?? 'mdi-bell-outline' }}"></i>
                                    </div>
                                    <div class="twwda-notif-info">
                                        <p class="twwda-notif-title {{ is_null($notification->read_at) ? 'fw-semibold' : '' }}">
                                            {{ $notification->title ?? 'Notification' }}
                                        </p>
                                        <small class="twwda-notif-time">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(is_null($notification->read_at))
                                        <span class="twwda-unread-dot"></span>
                                    @endif
                                </a>
                            @endforeach
                            @if($notifications->count() > 8)
                                <div class="twwda-view-all-link">
                                    <a href="{{ route('workflow.notifications') }}">
                                        View all notifications <i class="mdi mdi-arrow-right"></i>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Hidden form for mark all read --}}
<form id="markAllReadForm" action="{{ route('workflow.notifications.markread') }}" method="POST" class="d-none">
    @csrf
</form>

{{-- ═══════════════════════════════════════════════════════════
     DASHBOARD STYLES — Modern glassmorphism & micro-interactions
     ═══════════════════════════════════════════════════════════ --}}
<style>
/* Dashboard — clean, logo-balanced (black / red / green on white) */
.content { --d-ink: #111827; --d-body: #374151; --d-muted: #6b7280; --d-line: #eef0f2; --d-soft: #f7f8f9;
           --d-red: #cc1f2b; --d-red-soft: #fdeced; --d-green: #23803a; --d-green-soft: #e9f6eb;
           --d-amber: #b45309; --d-amber-soft: #fef6e4; --d-black: #1a1a1a; }

/* ── Welcome header ───────────────────────────────────────── */
.twwda-hero-banner {
    position: relative; overflow: hidden;
    background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 16px;
    box-shadow: 0 1px 2px rgba(17,17,17,.04), 0 6px 20px rgba(17,17,17,.04);
}
.twwda-hero-bg { position: absolute; left: 0; top: 0; bottom: 0; width: 5px;
    background: linear-gradient(180deg, #1a1a1a 0 33.33%, #cc1f2b 33.33% 66.66%, #3ca848 66.66% 100%); }
.twwda-hero-content { position: relative; padding: 24px 28px 22px 32px; }
.twwda-hero-greeting { font-size: 13.5px; font-weight: 600; color: var(--d-muted); letter-spacing: .01em; }
.twwda-hero-name { font-size: 1.6rem !important; font-weight: 800 !important; color: var(--d-ink) !important; letter-spacing: -.02em; }
.twwda-hero-meta { display: flex; flex-wrap: wrap; gap: 8px; }
.twwda-hero-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 999px;
    background: var(--d-soft); border: 1px solid var(--d-line); color: var(--d-body); font-size: 12.5px; font-weight: 500; }
.twwda-hero-badge i { color: var(--d-muted); font-size: 14px; }
.twwda-reporting-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--d-body); }
.twwda-reporting-badge i { color: var(--d-green); font-size: 16px; }
.twwda-reporting-badge strong { color: var(--d-ink); }
.twwda-reporting-via { color: var(--d-muted); }

.twwda-hero-btn { display: inline-flex; align-items: center; gap: 7px; height: 42px; padding: 0 18px; border-radius: 11px;
    background: var(--d-red); color: #fff !important; font-size: 14px; font-weight: 600; border: 0; text-decoration: none;
    transition: background .15s, transform .15s, box-shadow .15s; box-shadow: 0 4px 12px rgba(204,31,43,.22); }
.twwda-hero-btn:hover { background: #a3161f; transform: translateY(-1px); }
.twwda-hero-btn i { font-size: 17px; }
.twwda-hero-btn-icon { width: 42px; padding: 0; justify-content: center; background: #fff; color: var(--d-body) !important;
    border: 1px solid var(--d-line); box-shadow: none; }
.twwda-hero-btn-icon:hover { background: var(--d-soft); color: var(--d-ink) !important; }
.twwda-hero-btn-alert { background: #fff; color: var(--d-red) !important; border: 1px solid #f5c2c6; box-shadow: none; }
.twwda-hero-btn-alert:hover { background: var(--d-red-soft); }
.twwda-pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--d-red); animation: d-pulse 2s infinite; }
.twwda-notif-badge { position: absolute; top: -6px; right: -6px; min-width: 20px; height: 20px; padding: 0 5px; border-radius: 999px;
    background: var(--d-red); color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff; }
@keyframes d-pulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }

/* ── KPI cards ────────────────────────────────────────────── */
.twwda-glass-card { position: relative; height: 100%; background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 14px;
    padding: 18px; box-shadow: 0 1px 2px rgba(17,17,17,.04); transition: box-shadow .2s, transform .2s, border-color .2s; }
.twwda-glass-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(17,17,17,.07); border-color: rgba(17,17,17,.1); }
.twwda-glass-icon { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 14px; }
.tone-dark  { background: #f1f2f3; color: var(--d-black); }
.tone-red   { background: var(--d-red-soft); color: var(--d-red); }
.tone-green { background: var(--d-green-soft); color: var(--d-green); }
.tone-amber { background: var(--d-amber-soft); color: var(--d-amber); }
.twwda-glass-value { font-size: 1.75rem; font-weight: 800; color: var(--d-ink); line-height: 1.1; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
.twwda-glass-label { font-size: 13px; font-weight: 500; color: var(--d-muted); margin-top: 4px; }

/* ── Generic card ─────────────────────────────────────────── */
.twwda-modern-card, .twwda-pending-card, .twwda-director-card {
    background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 14px; overflow: hidden;
    box-shadow: 0 1px 2px rgba(17,17,17,.04);
}
.twwda-modern-card-header, .twwda-pending-header, .twwda-director-header {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 20px; border-bottom: 1px solid var(--d-line); background: #fff;
}
.twwda-modern-card-header h6, .twwda-pending-header h6, .twwda-director-header h6 { font-size: 15px !important; font-weight: 700 !important; color: var(--d-ink) !important; }
.twwda-pending-header small, .twwda-director-header small { color: var(--d-muted); font-size: 12.5px; }
.twwda-modern-card-body { padding: 18px 20px; }
.twwda-card-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
    background: #f1f2f3; color: var(--d-black); font-size: 17px; }
.twwda-card-badge { font-size: 12px; font-weight: 600; color: var(--d-muted); background: var(--d-soft); border: 1px solid var(--d-line);
    padding: 4px 10px; border-radius: 999px; }
.twwda-card-count { font-size: 1.25rem; font-weight: 800; color: var(--d-ink); }
.twwda-view-all-btn { font-size: 13px; font-weight: 600; color: var(--d-red); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.twwda-view-all-btn:hover { color: #a3161f; }
.twwda-view-all-link { padding: 12px 20px; border-top: 1px solid var(--d-line); text-align: center; }
.twwda-view-all-link a { font-size: 13px; font-weight: 600; color: var(--d-red); text-decoration: none; }

/* ── Pending (CEO / Director) ─────────────────────────────── */
.twwda-pending-card { border-left: 4px solid var(--d-red); }
.twwda-director-card { border-left: 4px solid var(--d-amber); }
.twwda-pending-icon-wrap, .twwda-director-icon-wrap { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center;
    justify-content: center; font-size: 19px; }
.twwda-pending-icon-wrap { background: var(--d-red-soft); color: var(--d-red); }
.twwda-director-icon-wrap { background: var(--d-amber-soft); color: var(--d-amber); }
.twwda-pending-count-badge, .twwda-director-count-badge { min-width: 30px; height: 30px; padding: 0 9px; border-radius: 999px;
    display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #fff; }
.twwda-pending-count-badge { background: var(--d-red); }
.twwda-director-count-badge { background: var(--d-amber); }
.twwda-pending-body, .twwda-director-body { padding: 18px 20px; }
.twwda-pending-grid, .twwda-director-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
.twwda-pending-tile, .twwda-director-tile { display: block; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--d-line);
    background: var(--d-soft); text-decoration: none; transition: border-color .15s, background .15s; }
.twwda-pending-tile:hover { background: #fff; border-color: rgba(17,17,17,.14); }
.twwda-pending-tile-icon, .twwda-director-tile-icon { font-size: 20px; color: var(--d-muted); }
.twwda-pending-tile-count, .twwda-director-tile-count { font-size: 1.5rem; font-weight: 800; color: var(--d-ink); line-height: 1.2; margin-top: 4px; }
.twwda-pending-tile-label, .twwda-director-tile-label { font-size: 12.5px; font-weight: 500; color: var(--d-muted); }
.twwda-pending-tile-amber .twwda-pending-tile-icon, .twwda-director-tile-amber .twwda-director-tile-icon { color: var(--d-amber); }
.twwda-pending-tile-green .twwda-pending-tile-icon, .twwda-director-tile-green .twwda-director-tile-icon { color: var(--d-green); }
.twwda-director-tile-red .twwda-director-tile-icon { color: var(--d-red); }
.twwda-pending-tile-blue .twwda-pending-tile-icon, .twwda-pending-tile-purple .twwda-pending-tile-icon,
.twwda-director-tile-blue .twwda-director-tile-icon { color: var(--d-black); }

.twwda-action-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--d-line); }
.twwda-action-item:last-child { border-bottom: 0; }
.twwda-action-icon { width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    font-size: 17px; background: #f1f2f3; color: var(--d-black); }
.twwda-action-icon-amber { background: var(--d-amber-soft); color: var(--d-amber); }
.twwda-action-icon-green { background: var(--d-green-soft); color: var(--d-green); }
.twwda-action-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
.twwda-action-title { font-size: 14px; font-weight: 600; color: var(--d-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.twwda-action-meta { font-size: 12.5px; color: var(--d-muted); }
.twwda-action-btn { flex-shrink: 0; display: inline-flex; align-items: center; height: 34px; padding: 0 14px; border-radius: 9px;
    border: 1px solid var(--d-line); background: #fff; color: var(--d-ink); font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s; }
.twwda-action-btn:hover { border-color: var(--d-red); color: var(--d-red); background: var(--d-red-soft); }

.twwda-allclear-card, .twwda-director-clear-card { background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 14px; padding: 18px 20px;
    box-shadow: 0 1px 2px rgba(17,17,17,.04); }
.twwda-allclear-card { display: flex; align-items: center; gap: 14px; }
.twwda-allclear-card h6, .twwda-director-clear-card h6 { margin: 0; font-weight: 700 !important; color: var(--d-ink) !important; }
.twwda-allclear-card p, .twwda-director-clear-card p { margin: 0; font-size: 13.5px; color: var(--d-muted); }
.twwda-allclear-icon, .twwda-director-clear-icon { width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0; display: flex;
    align-items: center; justify-content: center; background: var(--d-green-soft); color: var(--d-green); font-size: 20px; }

/* ── Workflow pipeline ────────────────────────────────────── */
.twwda-pipeline-v2 { display: flex; align-items: flex-start; overflow-x: auto; padding: 6px 0 2px; }
.twwda-pipe-stage { position: relative; display: flex; flex-direction: column; align-items: center; min-width: 92px; flex: 1; cursor: pointer; }
.twwda-pipe-node { position: relative; z-index: 1; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; background: #fff; color: #9ca3af; border: 2px solid #e5e7eb; font-size: 18px; transition: transform .2s; }
.twwda-pipe-stage:hover .twwda-pipe-node { transform: scale(1.08); }
.twwda-pipe-count { font-size: 15px; font-weight: 800; }
.twwda-pipe-label { margin-top: 9px; max-width: 86px; text-align: center; font-size: 12px; font-weight: 600; color: var(--d-muted); line-height: 1.3; }
.twwda-pipe-stage.active .twwda-pipe-label { color: var(--d-ink); }
.twwda-pipe-line { position: absolute; top: 21px; left: 50%; width: 100%; height: 2px; background: #e5e7eb; border-radius: 2px; }
.twwda-pipe-stage:last-child .twwda-pipe-line { display: none; }

/* ── Status list (board papers) ───────────────────────────── */
.twwda-status-item { display: flex; align-items: center; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--d-line); }
.twwda-status-item:last-child { border-bottom: 0; }
.twwda-status-item-icon { width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 17px; }
.twwda-status-item-info { flex: 1; min-width: 0; }
.twwda-status-item-label { display: block; font-size: 13.5px; font-weight: 600; color: var(--d-body); margin-bottom: 6px; }
.twwda-progress-track { height: 6px; background: #f1f2f3; border-radius: 999px; overflow: hidden; }
.twwda-progress-fill { height: 100%; border-radius: 999px; transition: width .6s ease; }
.twwda-status-item-count { font-size: 1.05rem; font-weight: 800; min-width: 28px; text-align: right; }

/* ── Notices & assignments ────────────────────────────────── */
.twwda-subsection { padding: 16px 20px; border-bottom: 1px solid var(--d-line); }
.twwda-subsection-last { border-bottom: 0; }
.twwda-subsection-header { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--d-muted); margin-bottom: 10px; }
.twwda-chip-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.twwda-modern-chip { display: inline-flex; align-items: baseline; gap: 6px; padding: 7px 12px; border-radius: 10px; border: 1px solid transparent; }
.twwda-modern-chip-num { font-size: 1.05rem; font-weight: 800; }
.twwda-modern-chip-text { font-size: 12.5px; font-weight: 600; }
.twwda-no-data-text { font-size: 13px; color: #9ca3af; }

/* ── Upcoming meetings ────────────────────────────────────── */
.twwda-meeting-item { display: flex; align-items: center; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--d-line); cursor: pointer; transition: background .15s; }
.twwda-meeting-item:last-child { border-bottom: 0; }
.twwda-meeting-item:hover { background: #fafafa; }
.twwda-meeting-date-block { width: 50px; height: 54px; flex-shrink: 0; border-radius: 12px; border: 1px solid var(--d-line); overflow: hidden;
    display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fff; }
.twwda-meeting-day { font-size: 1.2rem; font-weight: 800; color: var(--d-ink); line-height: 1; }
.twwda-meeting-month { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--d-red); margin-top: 3px; letter-spacing: .05em; }
.twwda-meeting-info { flex: 1; min-width: 0; }
.twwda-meeting-title { display: block; font-size: 14.5px; font-weight: 600; color: var(--d-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.twwda-meeting-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; margin-top: 3px; font-size: 12.5px; color: var(--d-muted); }
.twwda-stage-pill { font-size: 11.5px; font-weight: 600; color: var(--d-body); background: #f1f2f3; padding: 2px 8px; border-radius: 999px; }
.twwda-meeting-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.twwda-days-badge { font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; }
.twwda-days-overdue { background: var(--d-red-soft); color: var(--d-red); }
.twwda-days-urgent { background: var(--d-amber-soft); color: var(--d-amber); }
.twwda-days-soon { background: var(--d-green-soft); color: var(--d-green); }
.twwda-days-text { font-size: 12.5px; font-weight: 600; color: var(--d-muted); }
.twwda-meeting-view-btn { width: 32px; height: 32px; border-radius: 9px; border: 1px solid var(--d-line); display: flex; align-items: center;
    justify-content: center; color: var(--d-body); text-decoration: none; transition: all .15s; }
.twwda-meeting-view-btn:hover { border-color: var(--d-black); background: var(--d-black); color: #fff; }

/* ── Activity timeline ────────────────────────────────────── */
.twwda-activity-timeline { padding: 8px 20px 12px; }
.twwda-timeline-item { position: relative; display: flex; gap: 12px; padding: 10px 0; }
.twwda-timeline-item:not(:last-child)::before { content: ""; position: absolute; left: 13px; top: 38px; bottom: -6px; width: 2px; background: var(--d-line); }
.twwda-timeline-dot { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; }
.twwda-dot-green { background: var(--d-green-soft); color: var(--d-green); }
.twwda-dot-red { background: var(--d-red-soft); color: var(--d-red); }
.twwda-dot-blue { background: var(--d-amber-soft); color: var(--d-amber); }
.twwda-dot-gray { background: #f1f2f3; color: var(--d-muted); }
.twwda-timeline-content { flex: 1; min-width: 0; }
.twwda-timeline-text { margin: 0; font-size: 13.5px; color: var(--d-body); line-height: 1.5; }
.twwda-timeline-meta { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 3px; font-size: 12px; color: var(--d-muted); }
.twwda-module-tag { font-size: 11px; font-weight: 600; color: var(--d-body); background: #f1f2f3; padding: 1px 7px; border-radius: 999px; }

/* ── Notifications panel ──────────────────────────────────── */
.twwda-notif-count-badge { font-size: 11.5px; font-weight: 700; color: #fff; background: var(--d-red); padding: 2px 8px; border-radius: 999px; }
.twwda-mark-read-btn { border: 0; background: transparent; color: var(--d-green); font-size: 13px; font-weight: 600; padding: 4px 6px; border-radius: 8px; }
.twwda-mark-read-btn:hover { background: var(--d-green-soft); }
.twwda-notif-item { position: relative; display: flex; align-items: flex-start; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--d-line);
    text-decoration: none; transition: background .15s; }
.twwda-notif-item:last-child { border-bottom: 0; }
.twwda-notif-item:hover { background: #fafafa; }
.twwda-notif-icon-wrap { width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    background: #f1f2f3; color: var(--d-body); font-size: 16px; }
.twwda-notif-item.unread .twwda-notif-icon-wrap { background: var(--d-red-soft); color: var(--d-red); }
.twwda-notif-info { flex: 1; min-width: 0; }
.twwda-notif-title { margin: 0; font-size: 13.5px; color: var(--d-ink); line-height: 1.45; }
.twwda-notif-time { font-size: 12px; color: var(--d-muted); }
.twwda-unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--d-red); margin-top: 6px; flex-shrink: 0; }

/* ── Empty states ─────────────────────────────────────────── */
.twwda-empty-state { text-align: center; padding: 32px 20px; color: #9ca3af; }
.twwda-empty-state i { font-size: 36px; opacity: .6; }
.twwda-empty-state p { margin: 6px 0 0; font-size: 13.5px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
    .twwda-hero-content { padding: 20px 18px 18px 22px; }
    .twwda-hero-name { font-size: 1.3rem !important; }
    .twwda-pipe-stage { min-width: 76px; }
    .twwda-pipe-node { width: 38px; height: 38px; font-size: 15px; }
    .twwda-pipe-line { top: 18px; }
    .twwda-pipe-label { font-size: 11px; max-width: 70px; }
    .twwda-meeting-item, .twwda-status-item, .twwda-notif-item { padding-left: 14px; padding-right: 14px; }
    .twwda-director-clear-card .d-flex { flex-wrap: wrap; }
}
@media (prefers-reduced-motion: reduce) {
    .twwda-pulse-dot { animation: none; }
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Animated Counters ──────────────────────────────────────
    document.querySelectorAll('.twwda-glass-value[data-count]').forEach(function(el) {
        var target = parseInt(el.getAttribute('data-count')) || 0;
        if (target === 0) { el.textContent = '0'; return; }
        var duration = 1200;
        var start = 0;
        var startTime = null;

        function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }

        function animate(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var value = Math.floor(easeOutQuart(progress) * target);
            el.textContent = value.toLocaleString();
            if (progress < 1) requestAnimationFrame(animate);
            else el.textContent = target.toLocaleString();
        }
        requestAnimationFrame(animate);
    });

    // ── Animated Progress Bars ─────────────────────────────────
    setTimeout(function() {
        document.querySelectorAll('.twwda-progress-fill').forEach(function(bar) {
            var width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(function() { bar.style.width = width; }, 50);
        });
    }, 300);

    // ── Welcome Toast (SweetAlert2) ────────────────────────────
    var hour = new Date().getHours();
    var greeting = hour < 12 ? 'Good morning' : (hour < 17 ? 'Good afternoon' : 'Good evening');

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'success',
        title: greeting + '! Dashboard loaded successfully.',
        customClass: {
            popup: 'twwda-swal-toast'
        }
    });

    // ── 1. Meeting Status Donut ────────────────────────────────
    var meetingCounts = @json($meetingCounts);
    var labels = [], series = [], colors = [];
    var colorMap = {
        'draft': '#d1d5db', 'initiated': '#1a1a1a', 'in_progress': '#4b5563',
        'notice_created': '#9ca3af', 'notice_submitted': '#f59e0b', 'notice_approved': '#5fb86a',
        'notice_rejected': '#e06470', 'officers_assigned': '#374151',
        'papers_in_progress': '#d1a054', 'papers_submitted': '#f59e0b',
        'papers_approved': '#3ca848', 'ceo_papers_approved': '#23803a',
        'preparations_complete': '#6b7280', 'completed': '#19662c',
        'pending_approval': '#cc1f2b', 'rejected': '#a3161f', 'cancelled': '#e5e7eb'
    };

    for (var key in meetingCounts) {
        if (meetingCounts[key] > 0) {
            labels.push(key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }));
            series.push(meetingCounts[key]);
            colors.push(colorMap[key] || '#9ca3af');
        }
    }
    if (series.length === 0) { series = [1]; labels = ['No Meetings']; colors = ['#e5e7eb']; }

    new ApexCharts(document.querySelector('#meeting-status-chart'), {
        chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif', animations: { enabled: true, speed: 800, animateGradually: { enabled: true, delay: 150 } } },
        series: series, labels: labels, colors: colors,
        legend: { position: 'bottom', fontSize: '12px', fontWeight: 500, labels: { colors: '#374151' }, markers: { width: 10, height: 10, radius: 3 }, itemMargin: { horizontal: 6, vertical: 4 } },
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 600 }, dropShadow: { enabled: false } },
        plotOptions: { pie: { donut: { size: '58%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '14px', fontWeight: 700, color: '#0d0d0d' } } } } },
        stroke: { width: 2, colors: ['#fff'] },
        responsive: [{ breakpoint: 480, options: { chart: { height: 220 }, legend: { position: 'bottom' } } }]
    }).render();

    // ── 2. Monthly Meetings Trend ──────────────────────────────
    var monthlyData = @json($meetingsByMonth);
    var trendCategories = [], trendValues = [];

    for (var i = 11; i >= 0; i--) {
        var d = new Date();
        d.setMonth(d.getMonth() - i);
        var key = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
        var shortMonth = d.toLocaleString('default', { month: 'short' }) + ' ' + String(d.getFullYear()).slice(2);
        trendCategories.push(shortMonth);
        trendValues.push(monthlyData[key] || 0);
    }

    new ApexCharts(document.querySelector('#meetings-trend-chart'), {
        chart: { type: 'area', height: 280, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, animations: { enabled: true, speed: 1000 } },
        series: [{ name: 'Meetings', data: trendValues }],
        xaxis: { categories: trendCategories, labels: { style: { fontSize: '12px', colors: '#6b7280' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { fontSize: '12px', colors: '#6b7280' } }, min: 0, forceNiceScale: true },
        colors: ['#cc1f2b'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 }, background: { enabled: true, borderRadius: 4, padding: 4, borderColor: '#cc1f2b' } },
        stroke: { curve: 'smooth', width: 3 },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        tooltip: { style: { fontSize: '13px' } }
    }).render();

    // ── 3. Committee Distribution Bar ──────────────────────────
    var committeeData = @json($meetingsByCommittee);
    var commLabels = [], commValues = [];
    committeeData.forEach(function(c) {
        commLabels.push(c.name.length > 20 ? c.name.substring(0, 20) + '...' : c.name);
        commValues.push(c.count);
    });

    if (commLabels.length === 0) { commLabels = ['None']; commValues = [0]; }

    new ApexCharts(document.querySelector('#committee-chart'), {
        chart: { type: 'bar', height: 280, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, animations: { enabled: true, speed: 800 } },
        series: [{ name: 'Meetings', data: commValues }],
        xaxis: { categories: commLabels, labels: { style: { fontSize: '11px', colors: '#374151' }, trim: true, maxHeight: 80 } },
        yaxis: { labels: { style: { fontSize: '12px', colors: '#6b7280' } }, min: 0, forceNiceScale: true },
        colors: ['#23803a'],
        plotOptions: { bar: { borderRadius: 8, columnWidth: '50%', dataLabels: { position: 'top' } } },
        dataLabels: { enabled: true, offsetY: -20, style: { fontSize: '13px', fontWeight: 700, colors: ['#0d0d0d'] } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        tooltip: { style: { fontSize: '13px' } }
    }).render();
});

// ── SweetAlert2 Interactions ────────────────────────────────

// Show meeting detail popup
function showMeetingDetail(id, title, committee, stage, date, daysLeft) {
    var daysHtml = '';
    if (daysLeft < 0) {
        daysHtml = '<span style="color:#b91c1c;font-weight:700;">' + Math.abs(daysLeft) + ' days overdue</span>';
    } else if (daysLeft <= 3) {
        daysHtml = '<span style="color:#92400e;font-weight:700;">' + daysLeft + ' days left</span>';
    } else {
        daysHtml = '<span style="color:#059669;font-weight:700;">' + daysLeft + ' days to go</span>';
    }

    Swal.fire({
        html: '<div style="text-align:left;">' +
              '<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">' +
                '<div style="width:48px;height:48px;border-radius:12px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;">' +
                  '<i class="mdi mdi-calendar-star" style="font-size:22px;color:#fff;"></i>' +
                '</div>' +
                '<div>' +
                  '<h5 style="margin:0;font-size:18px;font-weight:700;color:#0d0d0d;">' + title + '</h5>' +
                  '<p style="margin:2px 0 0;font-size:13px;color:#6b7280;">' + committee + '</p>' +
                '</div>' +
              '</div>' +
              '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' +
                '<div style="background:#f8f8f8;padding:14px;border-radius:12px;text-align:center;">' +
                  '<div style="font-size:12px;color:#6b7280;font-weight:600;text-transform:uppercase;">Date</div>' +
                  '<div style="font-size:16px;font-weight:700;color:{{ setting('primary_color', '#1a1a1a') }};margin-top:2px;">' + date + '</div>' +
                '</div>' +
                '<div style="background:#ecfdf5;padding:14px;border-radius:12px;text-align:center;">' +
                  '<div style="font-size:12px;color:#6b7280;font-weight:600;text-transform:uppercase;">Stage</div>' +
                  '<div style="font-size:16px;font-weight:700;color:#23803a;margin-top:2px;">' + stage + ' of 9</div>' +
                '</div>' +
              '</div>' +
              '<div style="text-align:center;margin-top:14px;">' + daysHtml + '</div>' +
            '</div>',
        showCancelButton: true,
        confirmButtonText: '<i class="mdi mdi-eye-outline me-1"></i> View Details',
        cancelButtonText: 'Close',
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'twwda-swal-popup',
            confirmButton: 'twwda-swal-btn',
            cancelButton: 'twwda-swal-btn-cancel'
        },
        showClass: { popup: 'animate__animated animate__fadeInUp animate__faster' },
        hideClass: { popup: 'animate__animated animate__fadeOutDown animate__faster' }
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location = '/workflow/meetings/' + id;
        }
    });
}

// Show workflow stage detail
function showStageDetail(stageNum, stageName, count, color) {
    if (count === 0) {
        Swal.fire({
            icon: 'info',
            title: stageName,
            text: 'No meetings currently at this stage.',
            confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
            customClass: { popup: 'twwda-swal-popup', confirmButton: 'twwda-swal-btn' }
        });
        return;
    }

    Swal.fire({
        html: '<div style="text-align:center;">' +
              '<div style="width:64px;height:64px;border-radius:16px;background:' + color + ';display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 6px 20px ' + color + '40;">' +
                '<span style="font-size:28px;font-weight:800;color:#fff;">' + count + '</span>' +
              '</div>' +
              '<h4 style="margin:0 0 4px;font-size:20px;font-weight:700;color:#0d0d0d;">Stage ' + stageNum + ': ' + stageName + '</h4>' +
              '<p style="font-size:14px;color:#6b7280;margin:0;">' + count + ' meeting' + (count !== 1 ? 's' : '') + ' at this stage</p>' +
            '</div>',
        confirmButtonText: 'OK',
        confirmButtonColor: color,
        customClass: { popup: 'twwda-swal-popup', confirmButton: 'twwda-swal-btn' }
    });
}

// CEO pending actions popup
function showPendingActions() {
    Swal.fire({
        icon: 'warning',
        title: 'Pending Actions',
        html: '<p style="font-size:15px;color:#4b5563;">You have <strong>{{ $isCeo ? ($ceoPending["total"] ?? 0) : 0 }}</strong> items awaiting your review and approval. Scroll down to see all pending items.</p>',
        confirmButtonText: '<i class="mdi mdi-arrow-down me-1"></i> View Items',
        confirmButtonColor: '#cc1f2b',
        customClass: { popup: 'twwda-swal-popup', confirmButton: 'twwda-swal-btn' }
    }).then(function(result) {
        if (result.isConfirmed) {
            document.querySelector('.twwda-pending-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
}

// Confirm mark all notifications as read
function confirmMarkAllRead() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark all your unread notifications as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="mdi mdi-check-all me-1"></i> Yes, mark all',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        cancelButtonColor: '#6b7280',
        customClass: { popup: 'twwda-swal-popup', confirmButton: 'twwda-swal-btn', cancelButton: 'twwda-swal-btn-cancel' }
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('markAllReadForm').submit();
        }
    });
}
</script>

{{-- SweetAlert2 custom styling --}}
<style>
.twwda-swal-popup {
    border-radius: 18px !important;
    padding: 28px !important;
    font-family: 'Inter', sans-serif !important;
}
.twwda-swal-btn {
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    padding: 10px 24px !important;
    font-family: 'Inter', sans-serif !important;
}
.twwda-swal-btn-cancel {
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    padding: 10px 24px !important;
    font-family: 'Inter', sans-serif !important;
}
.twwda-swal-toast {
    border-radius: 12px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
}
.swal2-popup .swal2-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0d0d0d !important;
}
.swal2-popup .swal2-html-container {
    font-size: 15px !important;
    color: #4b5563 !important;
}
</style>
@endpush
