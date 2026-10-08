@if(auth()->user()->hasRole('Superadmin'))
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
        <a href="{{route('workflow.notifications')}}">
            <i class="mdi mdi-bell-ring-outline"></i>
            <span> Notifications</span>
        </a>
    </li>

    <li class="menu-title mt-2">User Management</li>
    <li>
        <a href="{{route('users.index')}}">
            <i class="mdi mdi-account-group-outline"></i>
            <span> All Users </span>
        </a>
    </li>
    <li>
        <a href="{{route('users.add')}}">
            <i class="mdi mdi-account-plus-outline"></i>
            <span> Add User </span>
        </a>
    </li>

    <li class="menu-title mt-2">Administration</li>
    <li>
        <a href="{{route('all.roles')}}">
            <i class="mdi mdi-shield-key-outline"></i>
            <span> Roles </span>
        </a>
    </li>
    <li>
        <a href="{{route('all.permission')}}">
            <i class="mdi mdi-lock-check-outline"></i>
            <span> All Permissions </span>
        </a>
    </li>
    <li>
        <a href="{{route('all.roles.permission')}}">
            <i class="mdi mdi-account-key-outline"></i>
            <span> Role Permissions </span>
        </a>
    </li>
    <li>
        <a href="{{ route('financial-years.index') }}">
            <i class="mdi mdi-calendar-clock"></i>
            <span> Financial Years </span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.settings.index') }}">
            <i class="mdi mdi-cog-outline"></i>
            <span> System Settings </span>
        </a>
    </li>
@endif
