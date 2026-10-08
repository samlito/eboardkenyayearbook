<li class="menu-title mt-2">Workflow</li>
<li>
    <a href="{{route('workflow.dashboard')}}">
        <i class="mdi mdi-view-dashboard-outline"></i>
        <span> Dashboard</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.meetings.almanac')}}">
        <i class="mdi mdi-book-open-page-variant-outline"></i>
        <span> Almanac</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.ceo.almanac')}}">
        <i class="mdi mdi-clipboard-check-outline"></i>
        <span> Almanac Approvals</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.ceo.internal-notices')}}">
        <i class="mdi mdi-file-check-outline"></i>
        <span> Internal Notices</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.ceo.papers')}}">
        <i class="mdi mdi-file-document-outline"></i>
        <span> Board Papers</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.ceo.board-notices')}}">
        <i class="mdi mdi-bell-check-outline"></i>
        <span> Board Notices</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.ceo.agenda-items')}}">
        <i class="mdi mdi-format-list-checks"></i>
        <span> Agenda Items</span>
    </a>
</li>
<li>
    <a href="{{route('workflow.notifications')}}">
        <i class="mdi mdi-bell-ring-outline"></i>
        <span> Notifications</span>
    </a>
</li>

{{-- Board Member Portal — CEO is also a board member --}}
<li class="menu-title mt-2">Board Portal</li>
<li>
    <a href="{{ route('board.portal.dashboard') }}">
        <i class="mdi mdi-view-dashboard-variant-outline"></i>
        <span> Board Dashboard</span>
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

{{--<li class="menu-title mt-2">Version 1</li>--}}
{{--<li>--}}
{{--    <a href="{{route('all.almanac.pendingceoapproval')}}">--}}
{{--        <i class="mdi mdi-clock-alert-outline"></i>--}}
{{--        <span> Pending Approvals</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--<li>--}}
{{--    <a href="{{route('all.almanac.ceoapproved')}}">--}}
{{--        <i class="mdi mdi-check-decagram-outline"></i>--}}
{{--        <span> Approved Almanac</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--<li>--}}
{{--    <a href="{{route('ceo.notices.pending')}}">--}}
{{--        <i class="mdi mdi-bell-outline"></i>--}}
{{--        <span> Board Notices</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--<li>--}}
{{--    <a href="{{route('ceo.board.papers.index')}}">--}}
{{--        <i class="mdi mdi-file-document-outline"></i>--}}
{{--        <span> Board Papers</span>--}}
{{--    </a>--}}
{{--</li>--}}
