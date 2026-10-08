@extends('admin_dashboard')
@section('admin')
    <style>
        :root{
            --brand:{{ setting('primary_color', '#1a1a1a') }};
            --brand-soft:#fdecee;
        }
        .brand-gradient{
            background: linear-gradient(135deg, var(--brand) 0%, #3a3a3a 100%);
        }
        .text-brand{ color: var(--brand) !important; }
        .bg-brand{ background-color: var(--brand) !important; }
        .bg-brand-soft{ background-color: var(--brand-soft) !important; }
        .card-shadow{
            border: 0;
            box-shadow: 0 8px 22px rgba(0,0,0,0.06);
            border-radius: 16px;
        }
        .stat{
            border-left: 4px solid var(--brand);
            border-radius: 12px;
        }
        .chip{
            background: var(--brand-soft);
            color: var(--brand);
            border-radius: 999px;
            padding: .25rem .65rem;
            font-weight: 600;
            display: inline-flex;
            align-items:center;
            gap: .35rem;
        }
        .timeline{
            position: relative;
            padding-left: 1.25rem;
        }
        .timeline::before{
            content:"";
            position:absolute;left:6px;top:0;bottom:0;width:2px;background:#e7e7e7;
        }
        .tl-item{
            position:relative;padding-left:1rem;margin-bottom:1rem;
        }
        .tl-item::before{
            content:"";
            position:absolute;left:-2px;top:.35rem;width:10px;height:10px;background:var(--brand);
            border-radius:50%;
            box-shadow:0 0 0 3px #f0f0f0;
        }
        .progress.brand .progress-bar{
            background-color: var(--brand);
        }
        .avatar-xl{
            width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid white;box-shadow:0 4px 12px rgba(0,0,0,.15)
        }
        .btn-brand{
            background: var(--brand);
            border-color: var(--brand);
            color:#fff;
        }
        .btn-brand:hover{
            opacity:.92;color:#fff
        }
    </style>

    <div class="content">
        <div class="container-fluid">


            <div class="card brand-gradient text-white card-shadow mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <img
                            src="{{ $member->photo ? asset('storage/'.$member->photo) : asset('images/board_male.png') }}"
                            class="avatar-xl" alt="Photo">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: whitesmoke">
                                Welcome, {{ $member->salutation ? optional($member->assignsalutation)->name : '' }}
                                {{ $member->names }}
                            </h3>
                            <div class="opacity-75">
                                {{ $member->designation }} &middot;
                                <span class="badge bg-light text-dark">{{ $member->director_type ?? 'Board Member' }}</span>
                                @if($member->role_type)
                                    <span class="badge bg-dark text-white">{{ ucfirst($member->role_type) }}</span>
                                @endif
                            </div>
                            <div class="mt-1 small opacity-75">
                                {{ $today->isoFormat('dddd, MMMM D, YYYY') }} &mdash; Day {{ $dayOfYear }} of {{ $today->year }}
                                <span id="live-time" class="ms-2"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-2">


                            @forelse($member->committees as $committee)
                                <span class="chip">
                                    <i class="mdi mdi-account-group"></i> {{ $committee->name }}
                                </span>
                            @empty
                                <span class="chip"><i class="mdi mdi-information-outline"></i> No committee assigned</span>
                            @endforelse
                        </div>
                        <a href="{{ $routes['profile'] }}" class="btn btn-light btn-sm">View Profile</a>
                        <a href="{{ $routes['meetings'] }}" class="btn btn-dark btn-sm">Meetings</a>
                    </div>
                </div>

            </div>


            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-shadow stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Upcoming Meetings</div>
                                    <h3 class="my-1">{{ $upcomingMeetings->count() }}</h3>
                                    <small class="text-muted">
                                        @if($nextMeeting)
                                            Next: {{ \Carbon\Carbon::parse($nextMeeting->date)->isoFormat('ddd, MMM D') }}
                                        @else
                                            No meeting scheduled
                                        @endif
                                    </small>
                                </div>
                                <i class="mdi mdi-calendar-clock text-brand" style="font-size:28px"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <a href="{{ $routes['meetings'] }}" class="text-brand small">View all</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-shadow stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Notices</div>
                                    <h3 class="my-1">{{ $notices->count() }}</h3>
                                    <small class="text-muted">Sent to your committees</small>
                                </div>
                                <i class="mdi mdi-bullhorn text-brand" style="font-size:28px"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <a href="{{ $routes['notices'] }}" class="text-brand small">Open notices</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-shadow stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Assigned Papers</div>
                                    <h3 class="my-1">{{ $totalUpcomingPapers }}</h3>
                                    <small class="text-muted">Including annexures</small>
                                </div>
                                <i class="mdi mdi-file-document-multiple text-brand" style="font-size:28px"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <a href="{{ $routes['papers'] }}" class="text-brand small">Review papers</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-shadow stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Profile Completeness</div>
                                    <h3 class="my-1">{{ $score }}%</h3>
                                    <div class="progress brand" style="height:8px">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $score }}%"></div>
                                    </div>
                                </div>
                                <i class="mdi mdi-account-badge text-brand" style="font-size:28px"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <a href="{{ $routes['profile'] }}" class="text-brand small">Complete profile</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row g-3">
                 Notices
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Recent Notices</h5>
                                <a href="{{ $routes['notices'] }}" class="text-brand small">See all</a>
                            </div>

                            @if($notices->isEmpty())
                                <div class="alert bg-brand-soft mb-0">
                                    <i class="mdi mdi-information-outline"></i> No notices yet.
                                </div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($notices->take(5) as $meeting)
                                        @php
                                            $notice = $meeting->officialNotice ?? $meeting->officialNotice10 ?? null;
                                        @endphp
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ optional($meeting->committee)->name ?? 'Committee' }}:
                                                        {{ $meeting->title ?? 'Board Meeting' }}
                                                    </div>
                                                    <small class="text-muted">
                                                        Sent {{ optional($meeting->board_notified_at)->diffForHumans() }},
                                                        Meeting: {{ \Carbon\Carbon::parse($meeting->date)->isoFormat('ddd, MMM D, h:mm A') }}
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    @if($notice)
                                                        <a href="{{ route('board.notices.show', $meeting->id) }}"
                                                           class="btn btn-sm btn-outline-primary">Open Notice</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                 Upcoming Meetings Timeline
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Upcoming Meetings</h5>
                                <a href="{{ $routes['meetings'] }}" class="text-brand small">All meetings</a>
                            </div>

                            @if($upcomingMeetings->isEmpty())
                                <div class="alert bg-brand-soft mb-0">
                                    <i class="mdi mdi-calendar-remove"></i> No upcoming meetings scheduled.
                                </div>
                            @else
                                <div class="timeline">
                                    @foreach($upcomingMeetings->take(5) as $m)
                                        <div class="tl-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ \Carbon\Carbon::parse($m->date)->isoFormat('ddd, MMM D') }}
                                                        &middot; {{ \Carbon\Carbon::parse($m->date)->isoFormat('h:mm A') }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ optional($m->committee)->name ?? 'Committee' }}
{{--                                                        @if($m->venue) • Venue: {{ $m->venue }} @endif--}}
                                                    </div>
                                                    <div class="small">
                                                        <span class="chip"><i class="mdi mdi-file-document"></i> Papers: {{ $m->boardPapers->count() }}</span>
                                                        @if($m->boardPapers->sum(fn($bp)=>$bp->annexures->count())>0)
                                                            <span class="chip"><i class="mdi mdi-paperclip"></i> Annexures: {{ $m->boardPapers->sum(fn($bp)=>$bp->annexures->count()) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <a href="{{ route('board.meetings.show', $m->id) }}" class="btn btn-sm btn-brand">View</a>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                 Profile Checklist
                <div class="col-lg-5">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-2">Complete Your Profile</h5>
                            <p class="text-muted small mb-3">Improve transparency and reduce back-and-forth by keeping your profile complete.</p>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="flex-grow-1">
                                    <div class="progress brand" style="height:10px">
                                        <div class="progress-bar" style="width: {{ $score }}%"></div>
                                    </div>
                                </div>
                                <div class="fw-bold">{{ $score }}%</div>
                            </div>

                            <ul class="list-unstyled mb-0">
                                @php
                                    $labels = [
                                        'photo' => 'Profile Photo',
                                        'upload_cv' => 'Curriculum Vitae',
                                        'appointment_letter' => 'Appointment Letter',
                                        'gazettee_notice' => 'Gazette Notice',
                                        'acceptance_letter' => 'Acceptance Letter',
                                        'qualifications' => 'Qualifications',
                                        'experience_summary' => 'Experience Summary',
                                        'postal_address' => 'Postal Address',
                                        'county_id' => 'County',
                                        'date_of_birth' => 'Date of Birth',
                                    ];
                                @endphp
                                @foreach($labels as $key => $label)
                                    <li class="mb-2 d-flex align-items-center">
                                        @if(!empty($checklist[$key]))
                                            <i class="mdi mdi-check-circle text-success me-2"></i>
                                        @else
                                            <i class="mdi mdi-alert-circle text-warning me-2"></i>
                                        @endif
                                        <span>{{ $label }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-3">
                                <a href="{{ $routes['profile'] }}" class="btn btn-sm btn-brand">Update Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

                 Quick Links / Actions
                <div class="col-lg-7">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-2">Quick Actions</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a class="btn w-100 btn-outline-primary" href="{{ $routes['notices'] }}">
                                        <i class="mdi mdi-bullhorn"></i> Notices
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a class="btn w-100 btn-outline-primary" href="{{ $routes['meetings'] }}">
                                        <i class="mdi mdi-calendar"></i> Meetings
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a class="btn w-100 btn-outline-primary" href="{{ $routes['papers'] }}">
                                        <i class="mdi mdi-file-document"></i> Board Papers
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a class="btn w-100 btn-outline-primary" href="{{ $routes['committees'] }}">
                                        <i class="mdi mdi-account-group"></i> Committees
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a class="btn w-100 btn-outline-primary" href="{{ $routes['profile'] }}">
                                        <i class="mdi mdi-account-cog"></i> My Profile
                                    </a>
                                </div>
{{--                                 Add more contextual actions as needed--}}
                            </div>

                            @if($nextMeeting)
                                <div class="mt-4 p-3 bg-brand-soft rounded-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold mb-1">
                                                Next up: {{ optional($nextMeeting->committee)->name ?? 'Board Meeting' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($nextMeeting->date)->isoFormat('dddd, MMM D, h:mm A') }}
{{--                                                @if($nextMeeting->venue) • Venue: {{ $nextMeeting->venue }} @endif--}}
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('board.meetings.show', $nextMeeting->id) }}" class="btn btn-brand btn-sm">Open</a>

                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>

    <script>
        (function(){
            const el = document.getElementById('live-time');
            function tick(){
                const d = new Date();
                el.textContent = '• ' + d.toLocaleTimeString();
            }
            tick(); setInterval(tick, 1000);
        })();
    </script>
@endsection

{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}
{{--    <div class="content">--}}

{{--        <!-- Start Content-->--}}
{{--        <div class="container-fluid">--}}

{{--            <!-- start page title -->--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="page-title-box">--}}
{{--                        <div class="page-title-right">--}}
{{--                            <form class="d-flex align-items-center mb-3">--}}
{{--                                <div class="input-group input-group-sm">--}}
{{--                                    <input type="text" class="form-control border-0" id="dash-daterange">--}}
{{--                                    <span class="input-group-text bg-blue border-blue text-white">--}}
{{--                                                    <i class="mdi mdi-calendar-range"></i>--}}
{{--                                                </span>--}}
{{--                                </div>--}}
{{--                                <a href="javascript: void(0);" class="btn btn-blue btn-sm ms-2">--}}
{{--                                    <i class="mdi mdi-autorenew"></i>--}}
{{--                                </a>--}}
{{--                                <a href="javascript: void(0);" class="btn btn-blue btn-sm ms-1">--}}
{{--                                    <i class="mdi mdi-filter-variant"></i>--}}
{{--                                </a>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                        <h4 class="page-title">Dashboard</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- end page title -->--}}


{{--            <!-- end row -->--}}

{{--        </div> <!-- container -->--}}

{{--    </div>--}}
{{--@endsection--}}
