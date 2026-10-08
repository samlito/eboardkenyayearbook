<div class="left-side-menu">
    <div class="h-100" data-simplebar>

        <!-- User box -->


        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Navigation</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        {{--another option <a href="{{ url('/dashboard') }}">--}}
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> Dashboards </span>
                    </a>
                </li>
                @if(auth()->user()->hasRole('Superadmin'))



                    <li>
                        <a href="{{route('all.roles')}}">
                            <i class="mdi mdi-domain"></i>
                            <span> Roles </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('all.permission')}}">
                            <i class="mdi mdi-allergy"></i>
                            <span> All Permissions </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('all.roles.permission')}}">
                            <i class="mdi mdi-account"></i>
                            <span> Role Permissions </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('users.index')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Users</span>
                        </a>
                    </li>




                    <li class="menu-title mt-2">Apps</li>

                @endif

                @if(auth()->user()->hasRole('Corporation Secretary'))
                    <li>
                        <a href="{{route('all.boardmembers')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Board Members</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('all.almanac.main')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Almanac</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('all.almanac.rejected')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Amend Almanac</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('board_meetings.select_schedule')}}">
                            <i class="mdi mdi-bell-ring-outline"></i>
                            <span> Almanac Meetings</span>
                        </a>
                    </li>



                    <li>
                        <a href="{{route('board.meetings.all')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span>Almanac Board Meetings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('actual.meetings.index')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Actual Meetings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('committees.index')}}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Board Committees</span>
                        </a>
                    </li>
                    <li>
                        {{--                        {{route('committees.members.index')}}--}}
                        <a href="#">
                            <i class="mdi mdi-account-box"></i>
                            <span> Board Committees members</span>
                        </a>
                    </li>
                    {{--                    <li>--}}
                    {{--                        <a href="{{ route('meetings.manage.index') }}">--}}
                    {{--                            <i class="bi bi-tools"></i> <span>Manage Meetings</span>--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
                    <li>
                        <a href="{{ route('meetings.manage.index') }}">
                            <i class="bi bi-tools"></i> <span>Manage Meetings</span>
                        </a>
                    </li>

                @endif

                @if(auth()->user()->hasRole('CEO'))

                    <li>
                        <a href="{{route('all.almanac.pendingceoapproval')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Pending Approvals</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('all.almanac.ceoapproved')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Approved Almanac</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('ceo.notices.pending')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Board Notices</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('ceo.board.papers.index')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Board Papers</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->hasRole('Board Member'))

                    <li>
                        <a href="{{route('board.notices.index')}}">
                            <i class="mdi mdi-bell-ring-outline"></i>
                            <span> Meeting Notices</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('board.my.meetings') }}" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>My Meetings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('meetings.active') }}" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Active Meetings</span>
                        </a>
                    </li>

                @endif
                @if(auth()->user()->hasRole('Division Head'))

                    <li>
                        <a href="{{route('division.head.meetings')}}">
                            <i class="mdi mdi-account-box-multiple"></i>
                            <span> Pending Notices</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('manager.revisions')}}">
                            <i class="mdi mdi-apple-airplay"></i>
                            <span> Revisions</span>
                        </a>
                    </li>


                @endif
                @if(auth()->user()->hasRole('admin'))
                    <li>
                        <a href="#sidebarEcommerce" data-bs-toggle="collapse">
                            <i class="mdi mdi-cart-outline"></i>
                            <span> Ecommerce </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarEcommerce">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="ecommerce-dashboard.html">Dashboard</a>
                                </li>
                                <li>
                                    <a href="ecommerce-products.html">Products</a>
                                </li>
                                <li>
                                    <a href="ecommerce-product-detail.html">Product Detail</a>
                                </li>
                                <li>
                                    <a href="ecommerce-product-edit.html">Add Product</a>
                                </li>
                                <li>
                                    <a href="ecommerce-customers.html">Customers</a>
                                </li>
                                <li>
                                    <a href="ecommerce-orders.html">Orders</a>
                                </li>
                                <li>
                                    <a href="ecommerce-order-detail.html">Order Detail</a>
                                </li>
                                <li>
                                    <a href="ecommerce-sellers.html">Sellers</a>
                                </li>
                                <li>
                                    <a href="ecommerce-cart.html">Shopping Cart</a>
                                </li>
                                {{--                            <li>--}}
                                {{--                                <a href="ecommerce-checkout.html">Checkout</a>--}}
                                {{--                            </li>--}}
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarCrm" data-bs-toggle="collapse">
                            <i class="mdi mdi-account-multiple-outline"></i>
                            <span> CRM </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarCrm">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="crm-dashboard.html">Dashboard</a>
                                </li>
                                <li>
                                    <a href="crm-contacts.html">Contacts</a>
                                </li>
                                <li>
                                    <a href="crm-opportunities.html">Opportunities</a>
                                </li>
                                <li>
                                    <a href="crm-leads.html">Leads</a>
                                </li>
                                <li>
                                    <a href="crm-customers.html">Customers</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarEmail" data-bs-toggle="collapse">
                            <i class="mdi mdi-email-multiple-outline"></i>
                            <span> Email </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarEmail">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="email-inbox.html">Inbox</a>
                                </li>
                                <li>
                                    <a href="email-read.html">Read Email</a>
                                </li>
                                <li>
                                    <a href="email-compose.html">Compose Email</a>
                                </li>
                                <li>
                                    <a href="email-templates.html">Email Templates</a>
                                </li>
                            </ul>
                        </div>
                    </li>


                    <li class="menu-title mt-2">Custom</li>

                    <li>
                        <a href="#sidebarAuth" data-bs-toggle="collapse">
                            <i class="mdi mdi-account-circle-outline"></i>
                            <span> Auth Pages </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarAuth">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="auth-login.html">Log In</a>
                                </li>
                                <li>
                                    <a href="auth-login-2.html">Log In 2</a>
                                </li>
                                <li>
                                    <a href="auth-register.html">Register</a>
                                </li>
                                <li>
                                    <a href="auth-register-2.html">Register 2</a>
                                </li>
                                <li>
                                    <a href="auth-signin-signup.html">Signin - Signup</a>
                                </li>
                                <li>
                                    <a href="auth-signin-signup-2.html">Signin - Signup 2</a>
                                </li>
                                <li>
                                    <a href="auth-recoverpw.html">Recover Password</a>
                                </li>
                                <li>
                                    <a href="auth-recoverpw-2.html">Recover Password 2</a>
                                </li>
                                <li>
                                    <a href="auth-lock-screen.html">Lock Screen</a>
                                </li>
                                <li>
                                    <a href="auth-lock-screen-2.html">Lock Screen 2</a>
                                </li>
                                <li>
                                    <a href="auth-logout.html">Logout</a>
                                </li>
                                <li>
                                    <a href="auth-logout-2.html">Logout 2</a>
                                </li>
                                <li>
                                    <a href="auth-confirm-mail.html">Confirm Mail</a>
                                </li>
                                <li>
                                    <a href="auth-confirm-mail-2.html">Confirm Mail 2</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarExpages" data-bs-toggle="collapse">
                            <i class="mdi mdi-text-box-multiple-outline"></i>
                            <span> Extra Pages </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarExpages">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="pages-starter.html">Starter</a>
                                </li>
                                <li>
                                    <a href="pages-timeline.html">Timeline</a>
                                </li>
                                <li>
                                    <a href="pages-sitemap.html">Sitemap</a>
                                </li>
                                <li>
                                    <a href="pages-invoice.html">Invoice</a>
                                </li>
                                <li>
                                    <a href="pages-faqs.html">FAQs</a>
                                </li>
                                <li>
                                    <a href="pages-search-results.html">Search Results</a>
                                </li>
                                <li>
                                    <a href="pages-pricing.html">Pricing</a>
                                </li>
                                <li>
                                    <a href="pages-maintenance.html">Maintenance</a>
                                </li>
                                <li>
                                    <a href="pages-coming-soon.html">Coming Soon</a>
                                </li>
                                <li>
                                    <a href="pages-gallery.html">Gallery</a>
                                </li>
                                <li>
                                    <a href="pages-404.html">Error 404</a>
                                </li>
                                <li>
                                    <a href="pages-404-two.html">Error 404 Two</a>
                                </li>
                                <li>
                                    <a href="pages-404-alt.html">Error 404-alt</a>
                                </li>
                                <li>
                                    <a href="pages-500.html">Error 500</a>
                                </li>
                                <li>
                                    <a href="pages-500-two.html">Error 500 Two</a>
                                </li>
                            </ul>
                        </div>
                    </li>


                @endif



            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
