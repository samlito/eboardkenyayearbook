@if(auth()->user()->hasRole('Corporation Secretary'))
    <li class="menu-title mt-2">Workflow</li>
    <li>
        <a href="{{route('workflow.dashboard')}}">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span> Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{route('workflow.meetings.almanac')}}">
            <i class="mdi mdi-rocket-launch-outline"></i>
            <span> Meeting Initiation</span>
        </a>
    </li>
    <li>
        <a href="{{route('workflow.notifications')}}">
            <i class="mdi mdi-bell-ring-outline"></i>
            <span> Notifications</span>
        </a>
    </li>

    @php
        $csMyAssignments = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())->count();
        $csPendingAssignments = \App\Models\Twwda\TwwdaOfficerAssignment::where('user_id', auth()->id())
            ->whereDoesntHave('boardPapers')
            ->count();
    @endphp
    <li>
        <a href="{{ route('workflow.officer.assignments') }}">
            <i class="mdi mdi-clipboard-account-outline"></i>
            <span> My Assignments</span>
            @if($csPendingAssignments > 0)
                <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $csPendingAssignments }}</span>
            @endif
        </a>
    </li>

    <li class="menu-title mt-2">Board Notices</li>
    <li>
        <a href="{{ route('workflow.board-notices.index') }}">
            <i class="mdi mdi-bulletin-board"></i>
            <span> Board Notices Hub</span>
        </a>
    </li>

    <li class="menu-title mt-2">Secretary</li>
    <li>
        <a href="{{route('users.index')}}">
            <i class="mdi mdi-account-group-outline"></i>
            <span> All Users</span>
        </a>
    </li>
    <li>
        <a href="{{route('users.add')}}">
            <i class="mdi mdi-account-plus-outline"></i>
            <span> Add User</span>
        </a>
    </li>
    <li>
        <a href="{{route('all.roles')}}">
            <i class="mdi mdi-shield-key-outline"></i>
            <span> Roles</span>
        </a>
    </li>
    <li>
        <a href="{{route('all.roles.permission')}}">
            <i class="mdi mdi-account-key-outline"></i>
            <span> Role Permissions</span>
        </a>
    </li>
    <li>
        <a href="{{route('directorates.index')}}">
            <i class="mdi mdi-office-building-outline"></i>
            <span> Directorates</span>
        </a>
    </li>
    <li>
        <a href="{{route('divisions.index')}}">
            <i class="mdi mdi-sitemap"></i>
            <span> Divisions</span>
        </a>
    </li>
    <li>
        <a href="{{route('all.boardmembers')}}">
            <i class="mdi mdi-account-tie-outline"></i>
            <span> Board Members</span>
        </a>
    </li>
    <li>
        <a href="{{route('committees.index')}}">
            <i class="mdi mdi-account-supervisor-outline"></i>
            <span> Board Committees</span>
        </a>
    </li>

    <li class="menu-title mt-2">Meetings</li>
    <li>
        <a href="{{route('division.head.meetings')}}">
            <i class="mdi mdi-bell-ring-outline"></i>
            <span> Meeting Notices</span>
        </a>
    </li>
    <li>
        <a href="{{route('cs.boardmeetings.index')}}">
            <i class="mdi mdi-file-document-outline"></i>
            <span> Board Papers</span>
        </a>
    </li>
    <li>
        <a href="{{ route('actual.meetings.index') }}">
            <i class="mdi mdi-calendar-check-outline"></i>
            <span> Actual Meetings</span>
        </a>
    </li>
    <li>
        <a href="{{ route('meetings.manage.index') }}">
            <i class="mdi mdi-cog-outline"></i>
            <span> Manage Meetings</span>
        </a>
    </li>

    <li class="menu-title mt-2">Almanac</li>
    <li>
        <a href="{{route('all.almanac.main')}}">
            <i class="mdi mdi-calendar-text-outline"></i>
            <span> Almanac</span>
        </a>
    </li>
    <li>
        <a href="{{route('all.almanac.rejected')}}">
            <i class="mdi mdi-calendar-edit"></i>
            <span> Amend Almanac</span>
        </a>
    </li>
{{--    <li>--}}
{{--        <a href="{{route('board_meetings.select_schedule')}}">--}}
{{--            <i class="mdi mdi-calendar-clock"></i>--}}
{{--            <span> Almanac Meetings</span>--}}
{{--        </a>--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <a href="{{route('board.meetings.all')}}">--}}
{{--            <i class="mdi mdi-calendar-multiple"></i>--}}
{{--            <span> Almanac Board Meetings</span>--}}
{{--        </a>--}}
{{--    </li>--}}

    <li class="menu-title mt-2">Records</li>
    <li>
        <a href="{{ route('repository.index') }}">
            <i class="mdi mdi-book-multiple-outline"></i>
            <span> Document Repository</span>
        </a>
    </li>
    <li>
        <a href="{{ route('resolutions.index') }}">
            <i class="mdi mdi-gavel"></i>
            <span> Board Resolutions</span>
        </a>
    </li>
    <li>
        <a href="{{route('audit.logs.index')}}">
            <i class="mdi mdi-shield-check-outline"></i>
            <span> Audit Logs</span>
        </a>
    </li>
{{--    <li>--}}
{{--        <a href="{{ route('quicksign.index') }}">--}}
{{--            <i class="mdi mdi-fountain-pen-tip"></i>--}}
{{--            <span> Demo Sign</span>--}}
{{--        </a>--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <a href="{{ route('pdfsign.index') }}">--}}
{{--            <i class="mdi mdi-file-certificate-outline"></i>--}}
{{--            <span> Demo Sign 2</span>--}}
{{--        </a>--}}
{{--    </li>--}}
@endif
