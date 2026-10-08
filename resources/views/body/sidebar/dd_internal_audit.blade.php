@php
    $ddiaUnreadNotifs = \App\Models\Twwda\TwwdaNotification::forUser(auth()->id())->unread()->count();

    $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();

    // Active meetings in audit committees
    $ddiaActiveMeetings = \App\Models\Twwda\TwwdaMeeting::whereIn('board_committee_id', $auditCommitteeIds)
        ->where('is_started', true)
        ->count();

    // Meetings pending preparation (Stage 8) for audit committees
    $ddiaPrepMeetings = \App\Models\Twwda\TwwdaMeeting::whereIn('board_committee_id', $auditCommitteeIds)
        ->where('current_stage', 8)
        ->count();

    // Board papers in audit committee meetings pending CEO / auto-approved
    $ddiaApprovedPapers = \App\Models\Twwda\TwwdaBoardPaper::whereHas('meeting', function($q) use ($auditCommitteeIds) {
        $q->whereIn('board_committee_id', $auditCommitteeIds);
    })->where(function($q) {
        $q->where('status', 'ceo_approved')->orWhere('is_auto_approved', true);
    })->count();

    // Internal notices pending DDIA approval (audit committees)
    $ddiaPendingNotices = \App\Models\Twwda\TwwdaInternalNotice::whereHas('meeting', function($q) use ($auditCommitteeIds) {
        $q->whereIn('board_committee_id', $auditCommitteeIds);
    })->where('status', 'submitted')->count();

    // Board papers pending DDIA review/approval (audit committees)
    $ddiaPendingPaperReview = \App\Models\Twwda\TwwdaBoardPaper::whereHas('meeting', function($q) use ($auditCommitteeIds) {
        $q->whereIn('board_committee_id', $auditCommitteeIds);
    })->whereIn('status', ['director_approved', 'ceo_review'])->count();
@endphp

<li class="menu-title mt-2">Workflow</li>
<li>
    <a href="{{ route('workflow.dashboard') }}">
        <i class="mdi mdi-view-dashboard-outline"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('workflow.meetings.almanac') }}">
        <i class="mdi mdi-rocket-launch-outline"></i>
        <span> Meeting Initiation</span>
    </a>
</li>
<li>
    <a href="{{ route('workflow.notifications') }}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Notifications</span>
        @if($ddiaUnreadNotifs > 0)
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaUnreadNotifs }}</span>
        @endif
    </a>
</li>

@php
    $ddiaMyAssignments = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())->count();
    $ddiaPendingAssignments = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())
        ->whereDoesntHave('boardPapers')
        ->count();
@endphp
<li>
    <a href="{{ route('workflow.officer.assignments') }}">
        <i class="mdi mdi-clipboard-account-outline"></i>
        <span> My Assignments</span>
        @if($ddiaPendingAssignments > 0)
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaPendingAssignments }}</span>
        @endif
    </a>
</li>

<li class="menu-title mt-2">Audit Committees</li>
<li>
    <a href="{{ route('workflow.ceo.internal-notices') }}">
        <i class="mdi mdi-file-certificate-outline"></i>
        <span> Notice Approvals</span>
        @if($ddiaPendingNotices > 0)
            <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaPendingNotices }}</span>
        @endif
    </a>
</li>
<li>
    <a href="{{ route('workflow.ceo.papers') }}">
        <i class="mdi mdi-file-check-outline"></i>
        <span> Paper Approvals</span>
        @if($ddiaPendingPaperReview > 0)
            <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaPendingPaperReview }}</span>
        @endif
    </a>
</li>
<li>
    <a href="{{ route('workflow.board-notices.index') }}">
        <i class="mdi mdi-bulletin-board"></i>
        <span> Board Notices</span>
    </a>
</li>
<li>
    <a href="{{ route('cs.boardmeetings.index') }}">
        <i class="mdi mdi-file-document-outline"></i>
        <span> Board Papers</span>
        @if($ddiaApprovedPapers > 0)
            <span class="badge bg-success rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaApprovedPapers }}</span>
        @endif
    </a>
</li>

<li class="menu-title mt-2">Meeting Day</li>
<li>
    <a href="{{ route('actual.meetings.index') }}">
        <i class="mdi mdi-calendar-check-outline"></i>
        <span> Meetings</span>
        @if($ddiaActiveMeetings > 0)
            <span class="badge bg-danger rounded-pill ms-1 animate__animated animate__pulse animate__infinite" style="font-size: 10px;">{{ $ddiaActiveMeetings }} live</span>
        @endif
    </a>
</li>
@if($ddiaPrepMeetings > 0)
<li>
    <a href="{{ route('workflow.meetings.almanac') }}">
        <i class="mdi mdi-clipboard-clock-outline"></i>
        <span> Preparation</span>
        <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size: 10px;">{{ $ddiaPrepMeetings }}</span>
    </a>
</li>
@endif

<li class="menu-title mt-2">Records</li>
<li>
    <a href="{{ route('audit.logs.index') }}">
        <i class="mdi mdi-shield-check-outline"></i>
        <span> Audit Logs</span>
    </a>
</li>
