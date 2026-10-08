<li class="menu-title mt-2">Board Portal</li>
<li>
    <a href="{{ route('board.portal.dashboard') }}">
        <i class="mdi mdi-view-dashboard-outline"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.calendar') }}">
        <i class="mdi mdi-calendar-month-outline"></i>
        <span> Board Calendar</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.committees') }}">
        <i class="mdi mdi-account-group-outline"></i>
        <span> My Committees</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.notices') }}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Official Notices</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.papers') }}">
        <i class="mdi mdi-file-document-outline"></i>
        <span> Board Papers</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.active') }}">
        <i class="mdi mdi-calendar-star"></i>
        <span> Active Meetings</span>
    </a>
</li>
<li>
    <a href="{{ route('board.portal.repository') }}">
        <i class="mdi mdi-book-multiple-outline"></i>
        <span> Document Repository</span>
    </a>
</li>

<li class="menu-title mt-2">Workflow</li>
<li>
    <a href="{{ route('workflow.dashboard') }}">
        <i class="mdi mdi-monitor-dashboard"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('workflow.notifications') }}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Notifications</span>
    </a>
</li>

@if(Auth::user()->hasRole('CEO'))
    <li class="menu-title mt-2">CEO Roles</li>
    <li>
        <a href="{{ route('all.almanac.pendingceoapproval') }}">
            <i class="mdi mdi-clock-alert-outline"></i>
            <span> Pending Approvals</span>
        </a>
    </li>
    <li>
        <a href="{{ route('all.almanac.ceoapproved') }}">
            <i class="mdi mdi-check-decagram-outline"></i>
            <span> Approved Almanac</span>
        </a>
    </li>
    <li>
        <a href="{{ route('ceo.notices.pending') }}">
            <i class="mdi mdi-bell-outline"></i>
            <span> Board Notices</span>
        </a>
    </li>
    <li>
        <a href="{{ route('ceo.board.papers.index') }}">
            <i class="mdi mdi-file-document-outline"></i>
            <span> Board Papers</span>
        </a>
    </li>
@endif
