@php
    $pendingPaperCount = \App\Models\Twwda\TwwdaBoardPaper::whereIn('assignment_id',
        \App\Models\Twwda\TwwdaOfficerAssignment::where('director_id', auth()->id())->pluck('id')
    )->pendingDirectorReview()->count();

    $directorUnreadNotifs = \App\Models\Twwda\TwwdaNotification::forUser(auth()->id())->unread()->count();
@endphp

<li>
    <a href="{{ route('workflow.dashboard') }}">
        <i class="mdi mdi-view-dashboard-outline"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('workflow.director.papers') }}">
        <i class="mdi mdi-file-eye-outline"></i>
        <span> Paper Reviews</span>
        @if($pendingPaperCount > 0)
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $pendingPaperCount }}</span>
        @endif
    </a>
</li>
<li>
    <a href="{{ route('workflow.officer.assignments') }}">
        <i class="mdi mdi-clipboard-text-outline"></i>
        <span> My Assignments</span>
        @php
            $dirAssignCount = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())
                ->whereNull('system_read_at')->whereNull('email_read_at')->count();
        @endphp
        @if($dirAssignCount > 0)
            <span class="badge bg-warning rounded-pill ms-1" style="font-size: 10px;">{{ $dirAssignCount }}</span>
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
    <a href="{{ route('workflow.notifications') }}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Notifications</span>
        @if($directorUnreadNotifs > 0)
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $directorUnreadNotifs }}</span>
        @endif
    </a>
</li>
