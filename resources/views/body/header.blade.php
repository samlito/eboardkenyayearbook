<div class="navbar-custom">
    <div class="container-fluid">

        <!-- Left: Menu toggle -->
        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect waves-light">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <!-- Breadcrumb area -->
            <li class="d-none d-sm-flex align-items-center ms-2">
                <span class="topbar-org">
                    {{ setting('institution_name', 'eBoard System') }}
                </span>
            </li>
        </ul>

        <!-- Right: Actions -->
        <ul class="list-unstyled topnav-menu float-end mb-0">

            <!-- Search -->
            <li class="d-none d-lg-block">
                <form class="app-search">
                    <div class="app-search-box">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search..." id="top-search">
                            <button class="btn input-group-text" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>

            <!-- Fullscreen -->
            <li class="dropdown d-none d-lg-inline-block">
                <a class="nav-link waves-effect waves-light" data-toggle="fullscreen" href="#">
                    <i class="fe-maximize noti-icon"></i>
                </a>
            </li>

            <!-- Notifications -->
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect" data-bs-toggle="dropdown" href="#">
                    <i class="fe-bell noti-icon"></i>
                    <span class="badge bg-danger rounded-circle noti-icon-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">Notifications</h5>
                    </div>
                    <div class="noti-scroll" data-simplebar>
                        <a href="#" class="dropdown-item notify-item">
                            <div class="notify-icon bg-info">
                                <i class="mdi mdi-bell-ring-outline"></i>
                            </div>
                            <p class="notify-details">No new notifications</p>
                        </a>
                    </div>
                    <a href="#" class="dropdown-item text-center text-primary notify-item notify-all">
                        View All <i class="fe-arrow-right"></i>
                    </a>
                </div>
            </li>

            <!-- User Profile -->
            @php
                $adminData = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());
            @endphp
            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect" data-bs-toggle="dropdown" href="#">
                    <img src="{{ (!empty($adminData->photo)) ? url('upload/admin_image/'.$adminData->photo) : url('upload/no_image.jpg') }}"
                         alt="user-image" class="rounded-circle" style="border: 2px solid #e5e7eb;">
                    <span class="pro-user-name ms-1">
                        {{ $adminData->name ?? 'User' }} <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0" style="font-size: 13px;">
                            Welcome, {{ $adminData->name ?? 'User' }}
                        </h6>
                        <small class="text-muted">{{ $adminData->email ?? '' }}</small>
                    </div>
                    <a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
                        <i class="fe-user me-1"></i> <span>My Account</span>
                    </a>
                    <a href="{{ route('change.password') }}" class="dropdown-item notify-item">
                        <i class="fe-lock me-1"></i> <span>Change Password</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.logout') }}" class="dropdown-item notify-item text-danger">
                        <i class="fe-log-out me-1"></i> <span>Logout</span>
                    </a>
                </div>
            </li>

        </ul>

        <div class="clearfix"></div>
    </div>
</div>
