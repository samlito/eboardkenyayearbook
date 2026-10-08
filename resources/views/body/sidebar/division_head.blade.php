<li class="menu-title mt-2">Workflow</li>
<li>
    <a href="{{route('workflow.dashboard')}}">
        <i class="mdi mdi-view-dashboard-outline"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.officer.assignments')}}">
        <i class="mdi mdi-clipboard-text-outline"></i>
        <span> My Assignments</span>
        @php
            $myUnreadCount = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())
                ->whereNull('system_read_at')->whereNull('email_read_at')->count();
        @endphp
        @if($myUnreadCount > 0)
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $myUnreadCount }}</span>
        @endif
    </a>
</li>
<li>
    <a href="{{route('workflow.director.papers')}}">
        <i class="mdi mdi-file-eye-outline"></i>
        <span> Paper Reviews</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.notifications')}}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Notifications</span>
    </a>
</li>

{{--<li class="menu-title mt-2">Version 1</li>--}}
{{--<li>--}}
{{--    <a href="{{route('division.head.meetings')}}">--}}
{{--        <i class="mdi mdi-bell-ring-outline"></i>--}}
{{--        <span> Pending Notices</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--@if(auth()->user()->division_id == 1)--}}
{{--<li>--}}
{{--    <a href="{{route('division.head.meetings.audit')}}">--}}
{{--        <i class="mdi mdi-clipboard-check-outline"></i>--}}
{{--        <span> Audit Submissions</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--@endif--}}
{{--<li>--}}
{{--    <a href="{{route('manager.revisions')}}">--}}
{{--        <i class="mdi mdi-file-restore-outline"></i>--}}
{{--        <span> Revisions</span>--}}
{{--    </a>--}}
{{--</li>--}}
