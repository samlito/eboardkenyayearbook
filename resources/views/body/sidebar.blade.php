<div class="left-side-menu">
    <div class="h-100" data-simplebar>

        <!-- Sidebar Logo -->
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
                <img src="{{ asset(setting('logo_sidebar', 'backend/assets/images/logo-sm.png')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" class="me-2">
                <span>{{ setting('institution_acronym', 'ORG') }} eBoard</span>
            </a>
        </div>

        <div id="sidebar-menu">
            <ul id="side-menu">

{{--                <li class="menu-title" style="margin-top: 12px;">Navigation</li>--}}
{{--                <li>--}}
{{--                    <a href="{{ route('dashboard') }}">--}}
{{--                        <i class="mdi mdi-view-dashboard-outline"></i>--}}
{{--                        <span> Dashboard </span>--}}
{{--                    </a>--}}
{{--                </li>--}}

                @if(auth()->user()->hasRole('Superadmin'))
                    @include('body.sidebar.superadmin')
                @endif

                @if(auth()->user()->hasRole('Corporation Secretary'))
                    @include('body.sidebar.corporation_secretary')
                @endif

                @if(auth()->user()->hasRole('CEO'))
                    @include('body.sidebar.ceo')
                @endif

                @if(auth()->user()->hasRole('Board Member'))
                    @include('body.sidebar.board_member')
                @endif

                @if(auth()->user()->hasRole('Director'))
                    <li class="menu-title mt-2">Director</li>
                    @include('body.sidebar.director')
                @endif

                @if(auth()->user()->hasRole('DD Internal Audit'))
                    @include('body.sidebar.dd_internal_audit')
                @endif

                @if(auth()->user()->hasRole('Division Head'))
                    @include('body.sidebar.division_head')
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <li class="menu-title mt-2">Admin</li>
                    @include('body.sidebar.admin')
                @endif

                <li class="menu-title mt-2">Help</li>
                <li>
                    <a href="{{ route('user.guide') }}" class="{{ request()->routeIs('user.guide') ? 'active' : '' }}">
                        <i class="mdi mdi-book-open-page-variant-outline"></i>
                        <span> User Guide</span>
                    </a>
                </li>

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
