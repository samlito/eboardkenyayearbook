@extends('admin_dashboard')
@section('admin')

@php
    $current = $meeting->current_stage ?? 1;
    $isRejected = str_contains($meeting->status ?? '', 'rejected');

    $stages = [
        1 => [
            'label' => 'Meeting Initiated',
            'role' => 'Corp Secretary / DD Internal Audit',
            'icon' => 'mdi-rocket-launch-outline',
            'description' => 'Meeting initiated from the approved almanac schedule.',
        ],
        2 => [
            'label' => 'Internal Notice Created',
            'role' => 'Corp Secretary / DD Internal Audit',
            'icon' => 'mdi-file-document-edit-outline',
            'description' => 'Prepare and submit the internal notice with agenda items for CEO approval.',
        ],
        3 => [
            'label' => 'CEO Approves Internal Notice',
            'role' => 'CEO',
            'icon' => 'mdi-check-decagram-outline',
            'description' => 'CEO reviews and approves or rejects the submitted internal notice.',
        ],
        4 => [
            'label' => 'Officers Notified & Assigned',
            'role' => 'Corp Secretary / DD Internal Audit',
            'icon' => 'mdi-account-group-outline',
            'description' => 'Assign officers (Directors/Deputy Directors) to agenda items and notify them.',
        ],
        5 => [
            'label' => 'Board Papers Prepared',
            'role' => 'Deputy Directors',
            'icon' => 'mdi-file-multiple-outline',
            'description' => 'Assigned officers prepare and upload board papers with annexures.',
        ],
        6 => [
            'label' => 'Director Reviews & Approves',
            'role' => 'Directors',
            'icon' => 'mdi-clipboard-check-outline',
            'description' => 'Directors review board papers from their deputies and approve or request revisions.',
        ],
        7 => [
            'label' => 'CEO Reviews & Approves Papers',
            'role' => 'CEO',
            'icon' => 'mdi-seal-variant',
            'description' => 'CEO reviews all approved board papers, adds sticky notes, and gives final approval.',
        ],
        8 => [
            'label' => 'Meeting Preparations Finalized',
            'role' => 'Corp Secretary',
            'icon' => 'mdi-format-list-checks',
            'description' => 'Finalize paper ordering, send board notices to members, and confirm logistics.',
        ],
        9 => [
            'label' => 'Meeting Day Proceedings',
            'role' => 'Corp Secretary / Board Members',
            'icon' => 'mdi-video-outline',
            'description' => 'Manage attendance, conflict-of-interest declarations, and live meeting proceedings.',
        ],
    ];
@endphp

<div class="content">
    <div class="container-fluid">

        {{-- ═══ Hero Header ═══ --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="twwda-meeting-hero border-0 mb-0">
                    <div class="twwda-hero-inner">
                        {{-- Decorative pattern --}}
                        <div class="twwda-hero-pattern"></div>

                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 position-relative">
                            <div class="d-flex align-items-start gap-3">
                                <a href="{{ route('workflow.meetings.almanac') }}"
                                   class="twwda-hero-back" title="Back to Almanac">
                                    <i class="mdi mdi-arrow-left"></i>
                                </a>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="twwda-hero-ref">
                                            <i class="mdi mdi-identifier me-1"></i>{{ $meeting->reference_number }}
                                        </span>
                                        <span class="twwda-hero-committee">
                                            <i class="mdi mdi-domain me-1"></i>{{ $meeting->boardCommittee->name ?? '-' }}
                                        </span>
                                    </div>
                                    <h4 class="twwda-hero-title mb-2">{{ $meeting->title }}</h4>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span class="twwda-hero-meta">
                                            <i class="mdi mdi-calendar-outline me-1"></i>
                                            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d M Y') }}
                                        </span>
                                        <span class="twwda-hero-meta">
                                            <i class="mdi mdi-clock-outline me-1"></i>
                                            {{ $meeting->meeting_time ? \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') : 'TBD' }}
                                        </span>
                                        <span class="twwda-hero-meta">
                                            <i class="mdi mdi-map-marker-outline me-1"></i>
                                            {{ $meeting->venue ?? 'Venue TBC' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                @php
                                    $heroStatusColor = match(true) {
                                        str_contains($meeting->status, 'rejected') => '#dc3545',
                                        str_contains($meeting->status, 'approved') || str_contains($meeting->status, 'complete') => '#099139',
                                        str_contains($meeting->status, 'progress') || str_contains($meeting->status, 'assigned') => setting('primary_color', '#1a1a1a'),
                                        default => '#6b7280',
                                    };
                                @endphp
                                <span class="twwda-hero-status" style="background: {{ $heroStatusColor }};">
                                    {{ ucwords(str_replace('_', ' ', $meeting->status)) }}
                                </span>
                                @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
                                <div class="d-flex gap-2 mt-1">
                                    <a href="{{ route('workflow.meetings.timeline', $meeting->id) }}"
                                       class="twwda-hero-btn">
                                        <i class="mdi mdi-timeline-clock-outline me-1"></i> Timeline
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Mini progress bar --}}
                        @php
                            $heroProgress = round((max(0, $current - 1) / 8) * 100);
                        @endphp
                        <div class="twwda-hero-progress mt-3">
                            <div class="twwda-hero-progress-track">
                                <div class="twwda-hero-progress-fill" style="width: {{ $heroProgress }}%;"></div>
                            </div>
                            <small class="twwda-hero-progress-label">Stage {{ $current }} of 9</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Layout: Accordion Steps + Sidebar --}}
        <div class="row">

            {{-- LEFT: Accordion Workflow Steps (Main Content Area) --}}
            <div class="col-xl-8 col-lg-7">

                {{-- Accordion Steps --}}
                <div class="accordion twwda-workflow-accordion" id="workflowAccordion">
                    @foreach($stages as $num => $stage)
                        @php
                            $isComplete = $num < $current;
                            $isCurrent = $num == $current;

                            // Stages 6-8 become accessible once stage 5 is reached
                            // Stage 9 is active for development (normally meeting day only)
                            $isParallelActive = ($current >= 5 && $num >= 6 && $num <= 8) || $num == 9;
                            $isFuture = $num > $current && !$isParallelActive;
                            $stepRejected = $isCurrent && $isRejected;

                            // Determine status text and badge
                            if ($isComplete) {
                                $statusText = 'Complete';
                                $statusBadge = 'twwda-badge-complete';
                            } elseif ($stepRejected) {
                                $statusText = 'Action Required';
                                $statusBadge = 'twwda-badge-rejected';
                            } elseif ($isCurrent) {
                                $statusText = 'In Progress';
                                $statusBadge = 'twwda-badge-current';
                            } elseif ($isParallelActive && !$isComplete) {
                                $statusText = 'Active';
                                $statusBadge = 'twwda-badge-active';
                            } else {
                                $statusText = 'Pending';
                                $statusBadge = 'twwda-badge-pending';
                            }
                        @endphp

                        <div class="card border-0 shadow-sm mb-2 twwda-step-card
                            {{ ($isCurrent || ($isParallelActive && !$isComplete)) ? 'twwda-step-active' : '' }}
                            {{ $isComplete ? 'twwda-step-complete' : '' }}
                            {{ $stepRejected ? 'twwda-step-rejected' : '' }}
                            {{ $isFuture ? 'twwda-step-future' : '' }}"
                            id="step-{{ $num }}">

                            {{-- Accordion Header --}}
                            <div class="card-header p-0 border-0 bg-transparent" id="heading-{{ $num }}">
                                <button class="btn w-100 text-start p-0 border-0 bg-transparent shadow-none"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $num }}"
                                        aria-expanded="{{ $isCurrent ? 'true' : 'false' }}"
                                        aria-controls="collapse-{{ $num }}"
                                        {{ $isFuture ? 'disabled' : '' }}>
                                    <div class="d-flex align-items-center px-3 py-3">

                                        {{-- Step Icon Circle --}}
                                        <div class="flex-shrink-0 me-3">
                                            @if($isComplete)
                                                <div class="twwda-step-circle twwda-circle-complete">
                                                    <i class="mdi mdi-check"></i>
                                                </div>
                                            @elseif($stepRejected)
                                                <div class="twwda-step-circle twwda-circle-rejected">
                                                    <i class="mdi mdi-alert-outline"></i>
                                                </div>
                                            @elseif($isCurrent || $isParallelActive)
                                                <div class="twwda-step-circle twwda-circle-current">
                                                    <i class="mdi {{ $stage['icon'] }}"></i>
                                                </div>
                                            @else
                                                <div class="twwda-step-circle twwda-circle-future">
                                                    <span>{{ $num }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Step Title, Stage # & Role --}}
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="twwda-step-num">{{ $num }}</span>
                                                <h6 class="mb-0 fw-semibold {{ $isFuture ? 'text-muted' : '' }}"
                                                    style="font-size: 13.5px; color: {{ $isComplete ? '#099139' : ($stepRejected ? '#dc3545' : (($isCurrent || $isParallelActive) ? setting('primary_color', '#1a1a1a') : '#8c98a4')) }};">
                                                    {{ $stage['label'] }}
                                                </h6>
                                            </div>
                                            <small class="text-muted" style="font-size: 11.5px;">
                                                <i class="mdi mdi-account-outline me-1"></i>{{ $stage['role'] }}
                                            </small>
                                        </div>

                                        {{-- Status Badge + Chevron --}}
                                        <div class="flex-shrink-0 ms-2 d-flex align-items-center gap-2">
                                            <span class="badge {{ $statusBadge }}">
                                                {{ $statusText }}
                                            </span>
                                            @if(!$isFuture)
                                                <i class="mdi mdi-chevron-down twwda-accordion-chevron" style="font-size: 20px; transition: transform 0.3s ease; color: #94a3b8;"></i>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </div>

                            {{-- Accordion Body --}}
                            @if(!$isFuture)
                            <div id="collapse-{{ $num }}"
                                 class="collapse {{ $isCurrent ? 'show' : '' }}"
                                 aria-labelledby="heading-{{ $num }}"
                                 data-bs-parent="#workflowAccordion">
                                <div class="card-body pt-0 px-3 pb-3">
                                    <div class="ps-5">
                                        {{-- Stage Description --}}
                                        <p class="text-muted mb-3" style="font-size: 12.5px;">
                                            {{ $stage['description'] }}
                                        </p>

                                        {{-- ═══ Stage 1: Meeting Initiated ═══ --}}
                                        @if($num == 1)
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-6">
                                                    <div class="border rounded-3 p-3 h-100" style="background: #f8f9fa;">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10.5px; letter-spacing: 0.05em;">Meeting Date</small>
                                                        <span class="fw-semibold" style="font-size: 13px;">
                                                            <i class="mdi mdi-calendar-outline text-twwda-navy me-1"></i>
                                                            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d M Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="border rounded-3 p-3 h-100" style="background: #f8f9fa;">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10.5px; letter-spacing: 0.05em;">Meeting Time</small>
                                                        <span class="fw-semibold" style="font-size: 13px;">
                                                            <i class="mdi mdi-clock-outline text-twwda-navy me-1"></i>
                                                            {{ $meeting->meeting_time ? \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') : 'TBD' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="border rounded-3 p-3 h-100" style="background: #f8f9fa;">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10.5px; letter-spacing: 0.05em;">Venue</small>
                                                        <span class="fw-semibold" style="font-size: 13px;">
                                                            <i class="mdi mdi-map-marker-outline text-twwda-navy me-1"></i>
                                                            {{ $meeting->venue ?? 'To Be Confirmed' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="border rounded-3 p-3 h-100" style="background: #f8f9fa;">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10.5px; letter-spacing: 0.05em;">Initiated By</small>
                                                        <span class="fw-semibold" style="font-size: 13px;">
                                                            <i class="mdi mdi-account-outline text-twwda-navy me-1"></i>
                                                            {{ $meeting->initiator->name ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($isCurrent)
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('workflow.internal-notice.create', $meeting->id) }}"
                                                       class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                        <i class="mdi mdi-arrow-right me-1"></i> Proceed to Create Internal Notice
                                                    </a>
                                                </div>
                                            @endif

                                        {{-- ═══ Stage 2: Internal Notice Created ═══ --}}
                                        @elseif($num == 2)
                                            @if($meeting->internalNotice)
                                                <div class="border rounded-3 p-3 mb-3" style="background: #f8f9fa;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold" style="font-size: 13px;">
                                                            {{ $meeting->internalNotice->reference_number ?? 'Internal Notice' }}
                                                        </span>
                                                        <span class="badge
                                                            @switch($meeting->internalNotice->status)
                                                                @case('approved') @case('special_approved') bg-success @break
                                                                @case('submitted') bg-info @break
                                                                @case('rejected') bg-danger @break
                                                                @default bg-warning
                                                            @endswitch" style="font-size: 11px;">
                                                            {{ ucwords(str_replace('_', ' ', $meeting->internalNotice->status)) }}
                                                        </span>
                                                    </div>
                                                    <p class="mb-2 text-muted" style="font-size: 12.5px;">
                                                        <strong>Subject:</strong> {{ $meeting->internalNotice->subject ?? '-' }}
                                                    </p>
                                                    @if($meeting->internalNotice->agenda_items)
                                                        <small class="text-muted">
                                                            <i class="mdi mdi-format-list-numbered"></i>
                                                            {{ count($meeting->internalNotice->agenda_items) }} agenda item(s)
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('workflow.internal-notice.preview', $meeting->internalNotice->id) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="mdi mdi-eye-outline me-1"></i> Preview Notice
                                                    </a>
                                                    @if($isCurrent && in_array($meeting->internalNotice->status, ['draft', 'rejected']))
                                                        <a href="{{ route('workflow.internal-notice.create', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-pencil-outline me-1"></i> Edit & Submit
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                @if($isCurrent)
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('workflow.internal-notice.create', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-plus me-1"></i> Create Internal Notice
                                                        </a>
                                                    </div>
                                                @else
                                                    <p class="text-muted mb-0" style="font-size: 12.5px;">No internal notice created yet.</p>
                                                @endif
                                            @endif

                                        {{-- ═══ Stage 3: CEO Approves Internal Notice ═══ --}}
                                        @elseif($num == 3)
                                            @if($meeting->internalNotice)
                                                <div class="border rounded-3 p-3 mb-3" style="background: #f8f9fa;">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <span class="fw-semibold d-block" style="font-size: 13px;">Notice Review Status</span>
                                                            <small class="text-muted">Submitted for CEO approval</small>
                                                        </div>
                                                        @if($meeting->internalNotice->status === 'special_approved')
                                                            <span class="badge" style="font-size: 11px; background: #0f9f41; color: #fff;">
                                                                <i class="mdi mdi-shield-star-outline me-1"></i> Special Approved
                                                            </span>
                                                        @else
                                                            <span class="badge
                                                                @switch($meeting->internalNotice->status)
                                                                    @case('approved') bg-success @break
                                                                    @case('submitted') bg-warning @break
                                                                    @case('rejected') bg-danger @break
                                                                    @default bg-secondary
                                                                @endswitch" style="font-size: 11px;">
                                                                {{ ucwords(str_replace('_', ' ', $meeting->internalNotice->status)) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Show special approval details if applicable --}}
                                                    @if($meeting->internalNotice->is_special_approval)
                                                        <div class="mt-2 pt-2 border-top">
                                                            <div class="rounded-3 p-2 px-3" style="background: #f9f9f9; border: 1px solid #eaeaea;">
                                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                                    <i class="mdi mdi-shield-star" style="color: #0f9f41; font-size: 16px;"></i>
                                                                    <span class="fw-semibold" style="font-size: 13px; color: #1b9345;">Special Approval</span>
                                                                </div>
                                                                <small style="color: #6b7280; font-size: 12px;">
                                                                    Approved by <strong style="color: #0d0d0d;">{{ $meeting->internalNotice->approver->name ?? 'Unknown' }}</strong>
                                                                    ({{ $meeting->internalNotice->approver->roles->pluck('name')->implode(', ') ?? '-' }})
                                                                    on {{ $meeting->internalNotice->approved_at ? \Carbon\Carbon::parse($meeting->internalNotice->approved_at)->format('d M Y, h:i A') : '-' }}
                                                                </small>
                                                                @if($meeting->internalNotice->special_approval_reason)
                                                                    <div class="mt-1">
                                                                        <small style="color: #4b5563; font-size: 12px;"><strong>Reason:</strong> {{ $meeting->internalNotice->special_approval_reason }}</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Show rejection comments if any --}}
                                                    @if($meeting->internalNotice->comments && $meeting->internalNotice->comments->where('action', 'rejected')->count() > 0)
                                                        @php $lastRejection = $meeting->internalNotice->comments->where('action', 'rejected')->last(); @endphp
                                                        <div class="mt-2 pt-2 border-top">
                                                            <small class="text-muted"><strong>Rejection Feedback:</strong> {{ $lastRejection->comment }}</small>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if($isCurrent)
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        {{-- CEO normal approval --}}
                                                        @if(auth()->user()->hasRole('CEO'))
                                                            <a href="{{ route('workflow.ceo.internal-notice.review', $meeting->internalNotice->id) }}"
                                                               class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                                <i class="mdi mdi-check-circle-outline me-1"></i> Review & Decide
                                                            </a>
                                                        @endif

                                                        {{-- Special Approval for CS / Deputy Director Internal Audit --}}
                                                        @if($meeting->internalNotice->status === 'submitted'
                                                            && auth()->user()->hasAnyRole(['Corporation Secretary', 'CS-Assistant'])
                                                            && !auth()->user()->hasRole('CEO'))
                                                            <button type="button" class="btn btn-sm text-white"
                                                                    style="background: #0f9f41; border-color: #0f9f41;"
                                                                    onclick="showSpecialApproveModal({{ $meeting->internalNotice->id }})">
                                                                <i class="mdi mdi-shield-star-outline me-1"></i> Special Approve
                                                            </button>
                                                        @endif

                                                        {{-- Revise & Resubmit after rejection --}}
                                                        @if($meeting->internalNotice->status === 'rejected')
                                                            <a href="{{ route('workflow.internal-notice.create', $meeting->id) }}"
                                                               class="btn btn-sm btn-outline-warning">
                                                                <i class="mdi mdi-refresh me-1"></i> Revise & Resubmit
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-muted mb-0" style="font-size: 12.5px;">Awaiting internal notice submission.</p>
                                            @endif

                                            {{-- Hidden form for special approval --}}
                                            @if($meeting->internalNotice && $meeting->internalNotice->status === 'submitted')
                                                <form id="special-approve-form" method="POST" class="d-none"
                                                      action="{{ route('workflow.internal-notice.special-approve', $meeting->internalNotice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="reason" id="special-approve-reason">
                                                </form>
                                            @endif

                                        {{-- ═══ Stage 4: Officers Notified & Assigned ═══ --}}
                                        @elseif($num == 4)
                                            @if($meeting->officerAssignments && $meeting->officerAssignments->count() > 0)
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12.5px;">
                                                        <thead>
                                                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                                                                <th>Officer</th>
                                                                <th>Agenda Item</th>
                                                                <th class="text-center">Read</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($meeting->officerAssignments->take(5) as $assignment)
                                                                <tr>
                                                                    <td>
                                                                        <i class="mdi mdi-account-circle-outline me-1 text-muted"></i>
                                                                        {{ $assignment->user->name ?? '-' }}
                                                                    </td>
                                                                    <td>{{ $assignment->agenda_item_number }} - {{ Str::limit($assignment->agenda_item_title, 40) }}</td>
                                                                    <td class="text-center">
                                                                        @if($assignment->system_read_at || $assignment->email_read_at)
                                                                            <i class="mdi mdi-check-circle text-success"></i>
                                                                        @else
                                                                            <i class="mdi mdi-clock-outline text-warning"></i>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if($meeting->officerAssignments->count() > 5)
                                                    <small class="text-muted d-block mb-2">
                                                        + {{ $meeting->officerAssignments->count() - 5 }} more assignment(s)
                                                    </small>
                                                @endif
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('workflow.assignments.dashboard', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="mdi mdi-view-dashboard-outline me-1"></i> Assignment Dashboard
                                                    </a>
                                                    @if($isCurrent)
                                                        <a href="{{ route('workflow.assignments.index', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-account-plus-outline me-1"></i> Assign More Officers
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                @if($isCurrent)
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('workflow.assignments.index', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-account-group-outline me-1"></i> Assign Officers
                                                        </a>
                                                    </div>
                                                @else
                                                    <p class="text-muted mb-0" style="font-size: 12.5px;">No officers assigned yet.</p>
                                                @endif
                                            @endif

                                        {{-- ═══ Stage 5: Board Papers Prepared ═══ --}}
                                        @elseif($num == 5)
                                            @if($meeting->boardPapers && $meeting->boardPapers->count() > 0)
                                                <div class="mb-3">
                                                    @foreach($meeting->boardPapers->take(5) as $paper)
                                                        <div class="d-flex align-items-center justify-content-between border rounded-2 px-3 py-2 mb-2" style="background: #f8f9fa;">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="mdi mdi-file-document-outline text-muted"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block" style="font-size: 12.5px;">
                                                                        {{ $paper->paper_number ?? 'Paper' }} - {{ Str::limit($paper->title, 50) }}
                                                                    </span>
                                                                    <small class="text-muted" style="font-size: 11px;">
                                                                        By {{ $paper->author->name ?? '-' }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <span class="badge
                                                                @switch($paper->status)
                                                                    @case('approved') @case('ceo_approved') bg-success @break
                                                                    @case('submitted') @case('director_approved') bg-info @break
                                                                    @case('rejected') @case('director_rejected') @case('ceo_rejected') bg-danger @break
                                                                    @default bg-warning
                                                                @endswitch" style="font-size: 10.5px;">
                                                                {{ ucwords(str_replace('_', ' ', $paper->status)) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if($meeting->boardPapers->count() > 5)
                                                    <small class="text-muted d-block mb-2">
                                                        + {{ $meeting->boardPapers->count() - 5 }} more paper(s)
                                                    </small>
                                                @endif
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if($isCurrent)
                                                        <a href="{{ route('workflow.papers.create', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-plus me-1"></i> Add Board Paper
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                @if($isCurrent)
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('workflow.papers.create', $meeting->id) }}"
                                                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                            <i class="mdi mdi-file-plus-outline me-1"></i> Create Board Paper
                                                        </a>
                                                    </div>
                                                @else
                                                    <p class="text-muted mb-0" style="font-size: 12.5px;">No board papers submitted yet.</p>
                                                @endif
                                            @endif

                                        {{-- ═══ Stage 6: Director Reviews & Approves ═══ --}}
                                        @elseif($num == 6)
                                            @if($meeting->boardPapers && $meeting->boardPapers->count() > 0)
                                                @php
                                                    $dirApproved = $meeting->boardPapers->whereIn('status', ['director_approved', 'ceo_review', 'ceo_approved', 'approved'])->count();
                                                    $dirRejected = $meeting->boardPapers->where('status', 'rejected_by_director')->count();
                                                    $dirPending = $meeting->boardPapers->whereIn('status', ['submitted', 'director_review'])->count();
                                                    $totalPapers6 = $meeting->boardPapers->count();
                                                @endphp
                                                {{-- Summary Counters --}}
                                                <div class="row g-2 mb-3">
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #f8f9fa;">
                                                            <div class="fw-bold" style="font-size: 20px; color: {{ setting('primary_color', '#1a1a1a') }};">{{ $totalPapers6 }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Total</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #fff8dd;">
                                                            <div class="fw-bold" style="font-size: 20px; color: #b57d00;">{{ $dirPending }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Pending</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #f0fdf4;">
                                                            <div class="fw-bold" style="font-size: 20px; color: #099139;">{{ $dirApproved }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Approved</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: {{ $dirRejected > 0 ? '#fef2f2' : '#f8f9fa' }};">
                                                            <div class="fw-bold" style="font-size: 20px; color: {{ $dirRejected > 0 ? '#dc3545' : '#8c98a4' }};">{{ $dirRejected }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Rejected</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Per-Paper Director Review Table --}}
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-sm table-hover mb-0" style="font-size: 12.5px;">
                                                        <thead>
                                                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                                                                <th>#</th>
                                                                <th>Paper</th>
                                                                <th>Author</th>
                                                                <th>Director</th>
                                                                <th>Status</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($meeting->boardPapers as $idx6 => $bp6)
                                                                @php
                                                                    $dirStatus = match(true) {
                                                                        in_array($bp6->status, ['director_approved', 'ceo_review', 'ceo_approved', 'approved']) => 'approved',
                                                                        $bp6->status === 'rejected_by_director' => 'rejected',
                                                                        default => 'pending',
                                                                    };
                                                                    $dirComment = $dirStatus === 'rejected' ? $bp6->comments->first() : null;
                                                                    $hasDirector6 = $bp6->assignment && $bp6->assignment->director;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $idx6 + 1 }}</td>
                                                                    <td>
                                                                        <span class="fw-semibold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $bp6->paper_number }}</span>
                                                                        <br><small class="text-muted">{{ Str::limit($bp6->title, 40) }}</small>
                                                                    </td>
                                                                    <td style="font-size: 12px;">{{ $bp6->author->name ?? '-' }}</td>
                                                                    <td style="font-size: 12px;">
                                                                        @if($hasDirector6 && $bp6->assignment->director_id === $bp6->prepared_by)
                                                                            <span class="text-success fw-semibold">{{ $bp6->assignment->director->name }}</span>
                                                                            <br><small class="text-muted fst-italic">Author is Director</small>
                                                                        @elseif($hasDirector6)
                                                                            {{ $bp6->assignment->director->name }}
                                                                        @elseif($bp6->is_auto_approved)
                                                                            <span class="text-muted fst-italic">Direct to CEO</span>
                                                                        @else
                                                                            <span class="text-muted">&mdash;</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($dirStatus === 'approved' && $bp6->is_auto_approved && $hasDirector6 && $bp6->assignment->director_id === $bp6->prepared_by)
                                                                            <span class="badge bg-info" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-arrow-right-bold-circle-outline me-1"></i>Auto-approved
                                                                            </span>
                                                                        @elseif($dirStatus === 'approved')
                                                                            <span class="badge bg-success" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-check-circle-outline me-1"></i>Approved
                                                                            </span>
                                                                        @elseif($dirStatus === 'rejected')
                                                                            <span class="badge bg-danger" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-close-circle-outline me-1"></i>Rejected
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-warning" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-clock-outline me-1"></i>Pending
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($dirStatus === 'approved' && $bp6->director_approved_at)
                                                                            <small>{{ $bp6->director_approved_at->format('d M Y') }}</small>
                                                                            <br><small class="text-muted">{{ $bp6->director_approved_at->diffForHumans() }}</small>
                                                                        @elseif($dirStatus === 'rejected' && $dirComment)
                                                                            <small>{{ $dirComment->created_at->format('d M Y') }}</small>
                                                                            <br><small class="text-muted">{{ $dirComment->created_at->diffForHumans() }}</small>
                                                                        @else
                                                                            <small class="text-muted">&mdash;</small>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @if($dirStatus === 'rejected' && $dirComment)
                                                                    <tr>
                                                                        <td colspan="6" style="background: #fef2f2; border-top: none;">
                                                                            <div class="d-flex align-items-start gap-2 ps-3">
                                                                                <i class="mdi mdi-comment-alert-outline text-danger mt-1"></i>
                                                                                <div>
                                                                                    <small class="fw-semibold text-danger">Rejection Comment:</small>
                                                                                    <small class="text-muted d-block">{{ Str::limit($dirComment->content, 150) }}</small>
                                                                                    <small class="text-muted fst-italic">&mdash; {{ $dirComment->user->name ?? 'Director' }}</small>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if(auth()->user()->hasAnyRole(['Director', 'Division Head']))
                                                    <a href="{{ route('workflow.director.papers') }}"
                                                       class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                        <i class="mdi mdi-clipboard-check-outline me-1"></i> Review Board Papers
                                                    </a>
                                                @endif
                                            @else
                                                <p class="text-muted mb-0" style="font-size: 12.5px;">Awaiting board paper submissions for review.</p>
                                            @endif

                                        {{-- ═══ Stage 7: CEO Reviews & Approves Papers ═══ --}}
                                        @elseif($num == 7)
                                            @if($meeting->boardPapers && $meeting->boardPapers->count() > 0)
                                                @php
                                                    $ceoApproved = $meeting->boardPapers->whereIn('status', ['ceo_approved', 'approved'])->count();
                                                    $ceoRejected = $meeting->boardPapers->where('status', 'rejected_by_ceo')->count();
                                                    $ceoPending = $meeting->boardPapers->whereIn('status', ['director_approved', 'ceo_review'])->count();
                                                    $ceoAwaiting = $meeting->boardPapers->whereNotIn('status', ['director_approved', 'ceo_review', 'ceo_approved', 'approved', 'rejected_by_ceo'])->count();
                                                @endphp
                                                {{-- Summary Counters --}}
                                                <div class="row g-2 mb-3">
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #f5f5f5;">
                                                            <div class="fw-bold" style="font-size: 20px; color: #64748b;">{{ $ceoAwaiting }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Awaiting</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #fff8dd;">
                                                            <div class="fw-bold" style="font-size: 20px; color: #b57d00;">{{ $ceoPending }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Pending CEO</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: #f0fdf4;">
                                                            <div class="fw-bold" style="font-size: 20px; color: #099139;">{{ $ceoApproved }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">CEO Approved</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-center border rounded-3 p-2" style="background: {{ $ceoRejected > 0 ? '#fef2f2' : '#f8f9fa' }};">
                                                            <div class="fw-bold" style="font-size: 20px; color: {{ $ceoRejected > 0 ? '#dc3545' : '#8c98a4' }};">{{ $ceoRejected }}</div>
                                                            <small class="text-muted" style="font-size: 11px;">Rejected</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Per-Paper CEO Review Table --}}
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-sm table-hover mb-0" style="font-size: 12.5px;">
                                                        <thead>
                                                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                                                                <th>#</th>
                                                                <th>Paper</th>
                                                                <th>Author</th>
                                                                <th>Director Status</th>
                                                                <th>CEO Status</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($meeting->boardPapers as $idx7 => $bp7)
                                                                @php
                                                                    $ceoStatus = match(true) {
                                                                        in_array($bp7->status, ['ceo_approved', 'approved']) => 'approved',
                                                                        $bp7->status === 'rejected_by_ceo' => 'rejected',
                                                                        in_array($bp7->status, ['director_approved', 'ceo_review']) => 'pending',
                                                                        default => 'awaiting',
                                                                    };
                                                                    $ceoComment = $ceoStatus === 'rejected' ? $bp7->comments->first() : null;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $idx7 + 1 }}</td>
                                                                    <td>
                                                                        <span class="fw-semibold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $bp7->paper_number }}</span>
                                                                        <br><small class="text-muted">{{ Str::limit($bp7->title, 40) }}</small>
                                                                    </td>
                                                                    <td style="font-size: 12px;">{{ $bp7->author->name ?? '-' }}</td>
                                                                    <td>
                                                                        @if($bp7->is_auto_approved && $bp7->assignment && $bp7->assignment->director_id === $bp7->prepared_by)
                                                                            <span class="badge bg-info" style="font-size: 10px;">
                                                                                <i class="mdi mdi-arrow-right-bold-circle-outline me-1"></i>Dir. Auto-approved
                                                                            </span>
                                                                        @elseif(in_array($bp7->status, ['director_approved', 'ceo_review', 'ceo_approved', 'approved', 'rejected_by_ceo']))
                                                                            <span class="badge bg-success" style="font-size: 10px;">Dir. Approved</span>
                                                                        @elseif($bp7->status === 'rejected_by_director')
                                                                            <span class="badge bg-danger" style="font-size: 10px;">Dir. Rejected</span>
                                                                        @elseif($bp7->is_auto_approved)
                                                                            <span class="badge bg-info" style="font-size: 10px;">Direct to CEO</span>
                                                                        @else
                                                                            <span class="badge bg-secondary" style="font-size: 10px;">{{ ucwords(str_replace('_', ' ', $bp7->status)) }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($ceoStatus === 'approved')
                                                                            <span class="badge bg-success" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-seal-variant me-1"></i>Approved
                                                                            </span>
                                                                        @elseif($ceoStatus === 'rejected')
                                                                            <span class="badge bg-danger" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-close-circle-outline me-1"></i>Rejected
                                                                            </span>
                                                                        @elseif($ceoStatus === 'pending')
                                                                            <span class="badge bg-warning" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-clock-outline me-1"></i>Pending Review
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-secondary" style="font-size: 10.5px;">
                                                                                <i class="mdi mdi-timer-sand me-1"></i>Awaiting
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($ceoStatus === 'approved' && $bp7->ceo_approved_at)
                                                                            <small>{{ $bp7->ceo_approved_at->format('d M Y') }}</small>
                                                                            <br><small class="text-muted">{{ $bp7->ceo_approved_at->diffForHumans() }}</small>
                                                                        @elseif($ceoStatus === 'rejected' && $ceoComment)
                                                                            <small>{{ $ceoComment->created_at->format('d M Y') }}</small>
                                                                            <br><small class="text-muted">{{ $ceoComment->created_at->diffForHumans() }}</small>
                                                                        @else
                                                                            <small class="text-muted">&mdash;</small>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @if($ceoStatus === 'rejected' && $ceoComment)
                                                                    <tr>
                                                                        <td colspan="6" style="background: #fef2f2; border-top: none;">
                                                                            <div class="d-flex align-items-start gap-2 ps-3">
                                                                                <i class="mdi mdi-comment-alert-outline text-danger mt-1"></i>
                                                                                <div>
                                                                                    <small class="fw-semibold text-danger">CEO Rejection Comment:</small>
                                                                                    <small class="text-muted d-block">{{ Str::limit($ceoComment->content, 150) }}</small>
                                                                                    <small class="text-muted fst-italic">&mdash; {{ $ceoComment->user->name ?? 'CEO' }}</small>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if(auth()->user()->hasRole('CEO'))
                                                    <a href="{{ route('workflow.ceo.papers.show', $meeting->id) }}"
                                                       class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                        <i class="mdi mdi-seal-variant me-1"></i> Review Papers as CEO
                                                    </a>
                                                @endif
                                            @else
                                                <p class="text-muted mb-0" style="font-size: 12.5px;">Awaiting director-approved papers for CEO review.</p>
                                            @endif

                                        {{-- ═══ Stage 8: Meeting Preparations Finalized ═══ --}}
                                        @elseif($num == 8)
                                            @php
                                                $totalPapers8 = $meeting->boardPapers ? $meeting->boardPapers->count() : 0;
                                                $approvedPapers8 = $meeting->boardPapers ? $meeting->boardPapers->whereIn('status', ['ceo_approved', 'approved'])->count() : 0;
                                                $allApproved8 = $totalPapers8 > 0 && $approvedPapers8 === $totalPapers8;
                                            @endphp

                                            {{-- Readiness Checklist --}}
                                            <div class="border rounded-3 p-3 mb-3" style="background: #f8f9fa;">
                                                <h6 class="mb-2" style="font-size: 13px; font-weight: 700; color: #121212;">
                                                    <i class="mdi mdi-clipboard-list-outline me-1 text-primary"></i> Meeting Readiness
                                                </h6>
                                                <div class="d-flex flex-column gap-2" style="font-size: 12.5px;">
                                                    {{-- Papers approved --}}
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($allApproved8)
                                                            <i class="mdi mdi-check-circle text-success" style="font-size: 16px;"></i>
                                                        @else
                                                            <i class="mdi mdi-circle-outline text-warning" style="font-size: 16px;"></i>
                                                        @endif
                                                        <span>All board papers CEO-approved <small class="text-muted">({{ $approvedPapers8 }}/{{ $totalPapers8 }})</small></span>
                                                    </div>
                                                    {{-- Board notice sent --}}
                                                    @php $noticeSent8 = $meeting->boardNotices && $meeting->boardNotices->where('status', 'sent')->count() > 0; @endphp
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($noticeSent8)
                                                            <i class="mdi mdi-check-circle text-success" style="font-size: 16px;"></i>
                                                        @else
                                                            <i class="mdi mdi-circle-outline text-warning" style="font-size: 16px;"></i>
                                                        @endif
                                                        <span>Board notice sent to members</span>
                                                    </div>
                                                    {{-- Preparation finalized --}}
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($meeting->preparation && $meeting->preparation->is_finalized)
                                                            <i class="mdi mdi-check-circle text-success" style="font-size: 16px;"></i>
                                                        @else
                                                            <i class="mdi mdi-circle-outline text-warning" style="font-size: 16px;"></i>
                                                        @endif
                                                        <span>Preparations finalized</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($meeting->preparation)
                                                <div class="border rounded-3 p-3 mb-3" style="background: #f8f9fa;">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="fw-semibold" style="font-size: 13px;">Preparation Status</span>
                                                        <span class="badge {{ $meeting->preparation->is_finalized ? 'bg-success' : 'bg-warning' }}" style="font-size: 11px;">
                                                            {{ $meeting->preparation->is_finalized ? 'Finalized' : 'In Progress' }}
                                                        </span>
                                                    </div>
                                                    @if($meeting->preparation->paper_order)
                                                        <small class="text-muted d-block mt-1">
                                                            <i class="mdi mdi-sort-numeric-ascending me-1"></i>
                                                            Paper order configured
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif
                                            {{-- Board Notice summary --}}
                                            @if($meeting->boardNotices && $meeting->boardNotices->count() > 0)
                                                <div class="border rounded-3 p-3 mb-3" style="background: #f8f8f8;">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <span class="fw-semibold d-block" style="font-size: 13px;">Board Notice</span>
                                                            <small class="text-muted">Notification to board members</small>
                                                        </div>
                                                        @php $latestNotice = $meeting->boardNotices->first(); @endphp
                                                        <span class="badge
                                                            @switch($latestNotice->status ?? 'draft')
                                                                @case('sent') bg-success @break
                                                                @case('approved') bg-success @break
                                                                @case('submitted') bg-info @break
                                                                @case('rejected') bg-danger @break
                                                                @default bg-warning
                                                            @endswitch" style="font-size: 11px;">
                                                            {{ ucwords(str_replace('_', ' ', $latestNotice->status ?? 'Draft')) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($isCurrent || $isParallelActive)
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('workflow.prepare.index', $meeting->id) }}"
                                                       class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                                                        <i class="mdi mdi-format-list-checks me-1"></i> Manage Preparations
                                                    </a>
                                                    <a href="{{ route('workflow.board-notice.create', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="mdi mdi-email-seal-outline me-1"></i> Board Notice
                                                    </a>
                                                </div>
                                            @endif

                                        {{-- ═══ Stage 9: Meeting Day Proceedings ═══ --}}
                                        @elseif($num == 9)
                                            {{-- Dev mode indicator --}}
                                            @if(!$isCurrent && $isParallelActive)
                                                <div class="alert alert-info py-2 mb-3 d-flex align-items-center gap-2" style="font-size: 12px; background: #efefef; border-color: #82f7ab;">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    <span>This stage is accessible early for development and testing purposes.</span>
                                                </div>
                                            @endif

                                            @if($meeting->is_started)
                                                <div class="border rounded-3 p-3 mb-3" style="background: #f0fdf4;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 10px; height: 10px;">
                                                        </div>
                                                        <span class="fw-semibold text-success" style="font-size: 13px;">Meeting is LIVE</span>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        Started {{ $meeting->started_at ? $meeting->started_at->diffForHumans() : '' }}
                                                        @if($meeting->attendance)
                                                            &middot; {{ $meeting->attendance->count() }} attendee(s)
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                            @if($meeting->conflictDeclarations && $meeting->conflictDeclarations->count() > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted d-block mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Conflict Declarations</small>
                                                    <span class="badge bg-info" style="font-size: 11px;">
                                                        {{ $meeting->conflictDeclarations->count() }} declaration(s) recorded
                                                    </span>
                                                </div>
                                            @endif
                                            @if($isCurrent || $isParallelActive)
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if(!$meeting->is_started)
                                                        <form method="POST" action="{{ route('workflow.meeting.start', $meeting->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="mdi mdi-play-circle-outline me-1"></i> Start Meeting
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('workflow.meeting.live', $meeting->id) }}"
                                                           class="btn btn-sm btn-success">
                                                            <i class="mdi mdi-video-outline me-1"></i> Enter Live Meeting
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('workflow.meeting.conflict', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="mdi mdi-shield-alert-outline me-1"></i> Conflict Declarations
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>

            {{-- RIGHT: Frozen Sidebar --}}
            <div class="col-xl-4 col-lg-5">
                <div class="twwda-frozen-sidebar">

                    {{-- Workflow Progress Stepper --}}
                    @include('workflow.components.workflow-progress', ['currentStage' => $meeting->current_stage, 'status' => $meeting->status])

                    {{-- Quick Stats --}}
                    @php
                        $paperCount = $meeting->boardPapers ? $meeting->boardPapers->count() : 0;
                        $assignmentCount = $meeting->officerAssignments ? $meeting->officerAssignments->count() : 0;
                        $approvedCount = $meeting->boardPapers ? $meeting->boardPapers->whereIn('status', ['ceo_approved', 'approved'])->count() : 0;
                    @endphp
                    <div class="card border-0 shadow-sm mb-3 twwda-sidebar-stats">
                        <div class="card-body p-0">
                            <div class="d-flex">
                                <div class="twwda-stat-cell flex-fill">
                                    <div class="twwda-stat-icon" style="background: #f0f0f0; color: {{ setting('primary_color', '#1a1a1a') }};">
                                        <i class="mdi mdi-account-group-outline"></i>
                                    </div>
                                    <div class="twwda-stat-value">{{ $assignmentCount }}</div>
                                    <div class="twwda-stat-label">Assigned</div>
                                </div>
                                <div class="twwda-stat-cell flex-fill" style="border-left: 1px solid #f0f0f0;">
                                    <div class="twwda-stat-icon" style="background: #fff8dd; color: #b57d00;">
                                        <i class="mdi mdi-file-multiple-outline"></i>
                                    </div>
                                    <div class="twwda-stat-value">{{ $paperCount }}</div>
                                    <div class="twwda-stat-label">Papers</div>
                                </div>
                                <div class="twwda-stat-cell flex-fill" style="border-left: 1px solid #f0f0f0;">
                                    <div class="twwda-stat-icon" style="background: #e6f7ed; color: #099139;">
                                        <i class="mdi mdi-check-decagram-outline"></i>
                                    </div>
                                    <div class="twwda-stat-value">{{ $approvedCount }}</div>
                                    <div class="twwda-stat-label">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Log (compact) --}}
                    <div class="card border-0 shadow-sm mb-3 twwda-activity-card">
                        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between twwda-activity-header">
                            <h6 class="mb-0 text-white fw-semibold" style="font-size: 12.5px;">
                                <i class="mdi mdi-history me-1"></i> RECENT ACTIVITY
                            </h6>
                            <a href="{{ route('workflow.meetings.timeline', $meeting->id) }}" class="text-white-50 text-decoration-none" style="font-size: 11px;">
                                View all <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                            @if($meeting->auditLogs && $meeting->auditLogs->count() > 0)
                                @foreach($meeting->auditLogs->sortByDesc('created_at')->take(6) as $log)
                                    @php
                                        $logStyle = match($log->action ?? '') {
                                            'created', 'approved' => ['bg' => '#e6f7ed', 'color' => '#099139', 'icon' => $log->action === 'approved' ? 'mdi-check' : 'mdi-plus'],
                                            'rejected' => ['bg' => '#fef2f2', 'color' => '#dc3545', 'icon' => 'mdi-close'],
                                            'submitted' => ['bg' => '#f0f0f0', 'color' => setting('primary_color', '#1a1a1a'), 'icon' => 'mdi-send'],
                                            default => ['bg' => '#f5f6f8', 'color' => '#6b7280', 'icon' => 'mdi-circle-medium'],
                                        };
                                    @endphp
                                    <div class="twwda-activity-item d-flex align-items-start px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="flex-shrink-0 me-2 mt-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 24px; height: 24px; background: {{ $logStyle['bg'] }}; color: {{ $logStyle['color'] }}; font-size: 12px;">
                                                <i class="mdi {{ $logStyle['icon'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <p class="mb-0 text-truncate" style="font-size: 12px; color: #111111;">{{ $log->description }}</p>
                                            <small style="font-size: 10.5px; color: #94a3b8;">
                                                {{ $log->user->name ?? 'System' }} &middot; {{ $log->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="mdi mdi-clock-outline" style="font-size: 28px; color: #d1d5db;"></i>
                                    <p class="mb-0 mt-1" style="font-size: 12px; color: #9ca3af;">No activity recorded yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══ Meeting Show — Enhanced Styles ═══ --}}
<style>
    /* ══════════════════════════════════════════════════════════════
       MEETING SHOW — Polished Professional Design
       ══════════════════════════════════════════════════════════════ */

    /* ── HERO HEADER ─────────────────────────────────────────────── */
    .twwda-meeting-hero {
        background: linear-gradient(135deg, #2b2b2b 0%, {{ setting('primary_color', '#1a1a1a') }} 40%, #a91622 100%);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }
    .twwda-hero-inner {
        padding: 24px 28px 20px;
        position: relative;
        z-index: 2;
    }
    .twwda-hero-pattern {
        position: absolute; inset: 0; z-index: 1; opacity: 0.06;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .twwda-hero-back {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.15); color: #fff; font-size: 18px;
        text-decoration: none; transition: all 0.2s;
        backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.1);
        flex-shrink: 0; margin-top: 2px;
    }
    .twwda-hero-back:hover { background: rgba(255,255,255,0.25); color: #fff; transform: translateX(-2px); }
    .twwda-hero-ref, .twwda-hero-committee {
        display: inline-flex; align-items: center;
        font-size: 12px; font-weight: 500; padding: 3px 10px; border-radius: 20px;
        background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.85);
        backdrop-filter: blur(4px);
    }
    .twwda-hero-title {
        font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em; line-height: 1.2;
    }
    .twwda-hero-meta {
        font-size: 13px; color: rgba(255,255,255,0.75); font-weight: 500;
        display: inline-flex; align-items: center;
    }
    .twwda-hero-meta i { font-size: 16px; }
    .twwda-hero-status {
        display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 700; color: #fff; letter-spacing: 0.03em; text-transform: uppercase;
    }
    .twwda-hero-btn {
        font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.8); text-decoration: none;
        padding: 5px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.08); transition: all 0.2s;
        display: inline-flex; align-items: center;
    }
    .twwda-hero-btn:hover { background: rgba(255,255,255,0.18); color: #fff; }
    .twwda-hero-progress { display: flex; align-items: center; gap: 12px; }
    .twwda-hero-progress-track {
        flex: 1; height: 6px; border-radius: 6px;
        background: rgba(255,255,255,0.15); overflow: hidden;
    }
    .twwda-hero-progress-fill {
        height: 100%; border-radius: 6px; transition: width 0.8s ease;
        background: linear-gradient(90deg, #4ade80, #22c55e);
        box-shadow: 0 0 8px rgba(74,222,128,0.4);
    }
    .twwda-hero-progress-label {
        font-size: 11px; color: rgba(255,255,255,0.6); font-weight: 600;
        white-space: nowrap; letter-spacing: 0.03em;
    }

    /* ── FROZEN SIDEBAR ──────────────────────────────────────────── */
    .twwda-frozen-sidebar {
        position: sticky; top: 75px;
        max-height: calc(100vh - 90px);
        overflow-y: auto; padding-bottom: 1rem;
        scrollbar-width: thin; scrollbar-color: #c8ccd0 transparent;
    }
    .twwda-frozen-sidebar::-webkit-scrollbar { width: 4px; }
    .twwda-frozen-sidebar::-webkit-scrollbar-thumb { background: #c8ccd0; border-radius: 4px; }

    /* ── Sidebar Stats ───────────────────────────────────────────── */
    .twwda-sidebar-stats .card-body { border-radius: 12px; overflow: hidden; }
    .twwda-stat-cell {
        text-align: center; padding: 14px 10px;
        transition: background 0.2s;
    }
    .twwda-stat-cell:hover { background: #fafafa; }
    .twwda-stat-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 16px; margin-bottom: 6px;
    }
    .twwda-stat-value { font-size: 22px; font-weight: 800; color: #121212; line-height: 1; }
    .twwda-stat-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

    /* ── Activity Card ───────────────────────────────────────────── */
    .twwda-activity-header {
        background: linear-gradient(135deg, #212121, {{ setting('primary_color', '#1a1a1a') }}) !important;
        border: none !important; border-radius: 0;
    }
    .twwda-activity-item { transition: background 0.15s; }
    .twwda-activity-item:hover { background: #fafafa; }

    /* ── ACCORDION STEP CARDS ────────────────────────────────────── */
    .twwda-workflow-accordion .twwda-step-card {
        border-radius: 14px !important;
        overflow: hidden;
        transition: all 0.25s ease;
        border: 1px solid #e5e7eb !important;
        background: #fff;
    }
    .twwda-workflow-accordion .twwda-step-card:hover:not(.twwda-step-future) {
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        transform: translateY(-1px);
    }

    .twwda-step-active {
        border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }} !important;
        background: linear-gradient(135deg, #fcfcfc 0%, #f8f8f8 100%) !important;
    }
    .twwda-step-complete {
        border-left: 4px solid #099139 !important;
        background: linear-gradient(135deg, #fbfefb 0%, #f0faf2 100%) !important;
    }
    .twwda-step-rejected { border-left: 4px solid #dc3545 !important; background: #fffbfb !important; }
    .twwda-step-future { opacity: 0.45; border-left: 4px solid transparent !important; }
    .twwda-step-future .card-header button { cursor: default !important; }

    /* ── Step Icon Circles ───────────────────────────────────────── */
    .twwda-step-circle {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 20px; flex-shrink: 0;
        transition: all 0.25s;
    }
    .twwda-circle-complete {
        background: linear-gradient(135deg, #099139, #0ab347);
        color: #fff; box-shadow: 0 4px 12px rgba(9,145,57,0.2);
    }
    .twwda-circle-current {
        background: linear-gradient(135deg, #2b2b2b, {{ setting('primary_color', '#1a1a1a') }});
        color: #fff; box-shadow: 0 4px 12px rgba(200, 16, 46,0.25);
        animation: twwda-pulse 2.5s ease-in-out infinite;
    }
    .twwda-circle-rejected {
        background: linear-gradient(135deg, #b91c1c, #dc3545);
        color: #fff; box-shadow: 0 4px 12px rgba(220,53,69,0.2);
    }
    .twwda-circle-future {
        background: #f3f4f6; color: #9ca3af; border: 2px solid #e5e7eb;
        font-size: 16px;
    }

    @keyframes twwda-pulse {
        0%, 100% { box-shadow: 0 4px 12px rgba(200, 16, 46,0.25); }
        50%      { box-shadow: 0 4px 20px rgba(200, 16, 46,0.4); }
    }

    /* ── Stage Number Badge ──────────────────────────────────────── */
    .twwda-step-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px; border-radius: 6px;
        background: #f5f5f5; color: #64748b; font-size: 11px; font-weight: 700;
        flex-shrink: 0;
    }

    /* ── Step Label Typography ────────────────────────────────────── */
    .twwda-workflow-accordion .card-header h6 {
        font-size: 16px !important;
        color: #0d0d0d; letter-spacing: -0.01em;
    }
    .twwda-workflow-accordion .card-header small {
        font-size: 13px !important;
        color: #6b7280;
    }
    .twwda-workflow-accordion .card-body p {
        font-size: 14px !important;
        color: #4b5563; line-height: 1.5;
    }
    .twwda-workflow-accordion .card-body .fw-semibold,
    .twwda-workflow-accordion .card-body span.fw-semibold {
        font-size: 14px !important;
        color: #0d0d0d;
    }
    .twwda-workflow-accordion .card-body small {
        font-size: 12px !important;
        color: #6b7280;
    }
    .twwda-workflow-accordion .card-body .btn {
        font-size: 14px; padding: 8px 18px;
        border-radius: 10px; font-weight: 600;
        transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .twwda-workflow-accordion .card-body .btn:hover {
        transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,0.1);
    }
    .twwda-workflow-accordion .card-body .table td,
    .twwda-workflow-accordion .card-body .table th {
        font-size: 13px !important;
        padding: 10px 12px; color: #374151; vertical-align: middle;
    }
    .twwda-workflow-accordion .card-body .table thead tr {
        background: #fafafa;
    }

    /* ── Status Badges ───────────────────────────────────────────── */
    .twwda-badge-complete {
        background: #e6f7ed; color: #166534; border: 1px solid #bbf0ce;
        font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;
    }
    .twwda-badge-current {
        background: #f0f0f0; color: #b81926; border: 1px solid #e92f3f;
        font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;
    }
    .twwda-badge-rejected {
        background: #fef2f2; color: #b91c1c; border: 1px solid #f5c6cb;
        font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;
    }
    .twwda-badge-pending {
        background: #f5f6f8; color: #6b7280; border: 1px solid #e5e7eb;
        font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;
    }
    .twwda-badge-active {
        background: #ececec; color: #292929; border: 1px solid #fb1e30;
        font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;
    }

    /* ── Stage Detail Cards ──────────────────────────────────────── */
    .twwda-workflow-accordion .border.rounded-3 {
        border-radius: 12px !important;
        border-color: #e5e7eb !important;
    }
    .twwda-workflow-accordion .border.rounded-2 { border-radius: 10px !important; }

    /* ── Accordion Chevron ───────────────────────────────────────── */
    .twwda-workflow-accordion .btn[aria-expanded="true"] .twwda-accordion-chevron {
        transform: rotate(180deg);
    }
    .twwda-workflow-accordion .card-header .btn:focus { box-shadow: none; }

    /* ── Summary Counter Cards ───────────────────────────────────── */
    .twwda-workflow-accordion .row.g-2 .border.rounded-3.p-2 {
        border-radius: 10px !important;
        transition: transform 0.15s;
    }
    .twwda-workflow-accordion .row.g-2 .border.rounded-3.p-2:hover {
        transform: scale(1.03);
    }
</style>

@endsection

@push('scripts')
<script>
/**
 * Special Approve Modal — SweetAlert2
 * Allows Corporation Secretary / Deputy Director Internal Audit
 * to approve an internal notice when the CEO is unavailable.
 */
function showSpecialApproveModal(noticeId) {
    Swal.fire({
        icon: 'warning',
        title: 'Special Approval',
        input: 'textarea',
        inputPlaceholder: 'Enter justification for special approval (required)...',
        inputAttributes: {
            style: 'font-size:14px; min-height:100px; border-radius:8px; border:1px solid #d1d5db; padding:12px;',
            maxlength: 1000
        },
        html:
            '<p class="mb-2" style="font-size:14px; color:#4b5563;">' +
                'You are about to <strong>special-approve</strong> this internal notice because the CEO is currently unavailable.' +
            '</p>' +
            '<p class="mb-0" style="font-size:13px; color:#6b7280;">' +
                'This action will be <strong>logged and clearly marked</strong> as a special approval in all records.' +
            '</p>',
        showCancelButton: true,
        confirmButtonColor: '#0f9f41',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="mdi mdi-shield-star-outline me-1"></i> Confirm Special Approval',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        inputValidator: function(value) {
            if (!value || !value.trim()) {
                return 'Please provide a reason for the special approval.';
            }
            if (value.trim().length > 1000) {
                return 'Reason must not exceed 1,000 characters.';
            }
        }
    }).then(function(result) {
        if (result.isConfirmed && result.value) {
            document.getElementById('special-approve-reason').value = result.value.trim();
            document.getElementById('special-approve-form').submit();
        }
    });
}
</script>
@endpush
