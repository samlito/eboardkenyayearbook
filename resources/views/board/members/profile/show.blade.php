@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;

        // Safety helpers
        $fmt = function($date){
            return $date ? Carbon::parse($date)->isoFormat('MMMM D, YYYY') : '—';
        };
        $age = function($dob){
            return $dob ? Carbon::parse($dob)->age . ' yrs' : '—';
        };
        $tenure = function($start, $end){
            if(!$start) return '—';
            $to = $end ? Carbon::parse($end) : Carbon::now();
            $from = Carbon::parse($start);
            return $from->diffForHumans($to, ['parts' => 3, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);
        };
        $fileLink = function($path){
            if(!$path) return null;
            $clean = Str::startsWith($path, ['http://','https://','/storage/']) ? $path : ('storage/'.$path);
            return asset($clean);
        };

        // Profile completeness (same logic family as dashboard)
        $checklist = [
            'photo' => !!$member->photo,
            'upload_cv' => !!$member->upload_cv,
            'appointment_letter' => !!$member->appointment_letter,
            'gazettee_notice' => !!$member->gazettee_notice,
            'acceptance_letter' => !!$member->acceptance_letter,
            'qualifications' => !!$member->qualifications,
            'experience_summary' => !!$member->experience_summary,
            'postal_address' => !!$member->postal_address,
            'county_id' => !!$member->county_id,
            'date_of_birth' => !!$member->date_of_birth,
                'signature_image' => !!$member->signature_image,  // <-- add
        ];
        $score = (int) (collect($checklist)->filter()->count() / count($checklist) * 100);
    @endphp

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
            border:0; box-shadow:0 8px 22px rgba(0,0,0,.06); border-radius:16px;
        }
        .avatar-xxl{
            width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 6px 20px rgba(0,0,0,.15);
        }
        .chip{
            background: var(--brand-soft); color: var(--brand);
            border-radius:999px; padding:.25rem .65rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem;
        }
        .progress.brand .progress-bar{ background-color:var(--brand); }
        .list-kv .kv{
            display:flex; align-items:baseline; gap:12px; padding:.4rem 0; border-bottom:1px dashed #f4f4f4;
        }
        .list-kv .kv:last-child{ border-bottom:0; }
        .list-kv .k{min-width:170px; color:#6c757d;}
        .btn-brand{
            background:var(--brand); border-color:var(--brand); color:#fff;
        }
        .btn-brand:hover{ opacity:.93; color:#fff; }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Header / Identity --}}
            <div class="card brand-gradient text-white card-shadow mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $member->photo ? asset('storage/'.$member->photo) : asset('images/avatar-placeholder.png') }}"
                             alt="Photo" class="avatar-xxl">
                        <div>
                            <h3 class="mb-1 fw-bold">
                                {{ optional($member->assignsalutation)->name ? optional($member->assignsalutation)->name.' ' : '' }}{{ $member->names }}
                            </h3>
                            <div class="opacity-75">
                                {{ $member->designation ?: 'Board Member' }}
                                @if($member->director_type)
                                    <span class="badge bg-light text-dark ms-1">{{ $member->director_type }}</span>
                                @endif
                                @if($member->role_type)
                                    <span class="badge bg-dark text-white ms-1">{{ ucfirst($member->role_type) }}</span>
                                @endif
                            </div>
                            <div class="mt-1 small opacity-75">
                                Member since {{ $fmt($member->appointment_date) }}
                                @if($member->end_date)
                                    • Ended {{ $fmt($member->end_date) }}
                                @else
                                    • Tenure: {{ $tenure($member->appointment_date, $member->end_date) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-2">
                            @forelse($member->committees as $committee)
                                <span class="chip"><i class="mdi mdi-account-group"></i> {{ $committee->name }}</span>
                            @empty
                                <span class="chip"><i class="mdi mdi-information-outline"></i> No committee assigned</span>
                            @endforelse
                        </div>
                        <a href="{{ route('board.dashboard') }}" class="btn btn-light btn-sm">Back to Dashboard</a>
                        {{-- Optional Edit route (uncomment if exists) --}}
                        {{-- <a href="{{ route('board.profile.edit', $member->id) }}" class="btn btn-dark btn-sm">Edit Profile</a> --}}
                        <a href="{{ route('board.profile.edit', $member->id) }}" class="btn btn-dark btn-sm">Edit Profile</a>

                    </div>
                </div>
            </div>

            {{-- Top Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card card-shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Profile Completeness</div>
                                <h3 class="mb-2">{{ $score }}%</h3>
                                <div class="progress brand" style="height:8px">
                                    <div class="progress-bar" style="width: {{ $score }}%"></div>
                                </div>
                            </div>
                            <i class="mdi mdi-account-badge text-brand" style="font-size:32px"></i>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <span class="text-brand small">Keep your profile up to date</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Appointment Date</div>
                                <h4 class="mb-1">{{ $fmt($member->appointment_date) }}</h4>
                                <small class="text-muted">Tenure: {{ $tenure($member->appointment_date, $member->end_date) }}</small>
                            </div>
                            <i class="mdi mdi-calendar-check text-brand" style="font-size:32px"></i>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <span class="text-brand small">Status: {{ $member->status ?: '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Date of Birth</div>
                                <h4 class="mb-1">{{ $fmt($member->date_of_birth) }}</h4>
                                <small class="text-muted">Age: {{ $age($member->date_of_birth) }}</small>
                            </div>
                            <i class="mdi mdi-cake-variant text-brand" style="font-size:32px"></i>
                        </div>
                        <div class="card-footer bg-light py-2 text-end">
                            <span class="text-brand small">{{ $member->director_type ?? 'Member' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="row g-3">
                {{-- Personal & Contact --}}
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Personal Information</h5>
                            <div class="list-kv">
                                <div class="kv"><div class="k">Names</div><div class="v">{{ $member->names }}</div></div>
                                <div class="kv"><div class="k">Salutation</div><div class="v">{{ optional($member->assignsalutation)->name ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Designation</div><div class="v">{{ $member->designation ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Director Type</div><div class="v">{{ $member->director_type ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Role Type</div><div class="v">{{ $member->role_type ? ucfirst($member->role_type) : '—' }}</div></div>
                                <div class="kv"><div class="k">National ID / Passport</div><div class="v">{{ $member->idno ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Place of Residence</div><div class="v">{{ $member->place_of_residence ?: '—' }}</div></div>
                                <div class="kv"><div class="k">County</div><div class="v">{{ optional($member->county)->name ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Postal Address</div><div class="v">{{ $member->postal_address ?: '—' }}</div></div>
                            </div>

                            <hr class="my-3">

                            <h5 class="mb-3">Contact</h5>
                            <div class="list-kv">
                                <div class="kv"><div class="k">Email</div><div class="v"><a href="mailto:{{ $member->email }}">{{ $member->email }}</a></div></div>
                                <div class="kv"><div class="k">Phone</div><div class="v"><a href="tel:{{ $member->phone }}">{{ $member->phone }}</a></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Qualifications & Experience --}}
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Qualifications</h5>
                            <div class="p-3 bg-brand-soft rounded-3" style="min-height:96px">
                                {!! $member->qualifications ? nl2br(e($member->qualifications)) : '<span class="text-muted">Not provided</span>' !!}
                            </div>

                            <h5 class="mt-4 mb-3">Experience Summary</h5>
                            <div class="p-3 bg-brand-soft rounded-3" style="min-height:120px">
                                {!! $member->experience_summary ? nl2br(e($member->experience_summary)) : '<span class="text-muted">Not provided</span>' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Appointment Details --}}
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Appointment Details</h5>
                            <div class="list-kv">
                                <div class="kv"><div class="k">Appointment Date</div><div class="v">{{ $fmt($member->appointment_date) }}</div></div>
                                <div class="kv"><div class="k">End Date</div><div class="v">{{ $fmt($member->end_date) }}</div></div>
                                <div class="kv"><div class="k">Exit Date</div><div class="v">{{ $fmt($member->exit_date) }}</div></div>
                                <div class="kv"><div class="k">Status</div><div class="v">{{ $member->status ?: '—' }}</div></div>
                                <div class="kv"><div class="k">Added By</div><div class="v">{{ $member->addedby ?: '—' }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($member->signature_image)
                    <div class="mt-3">
                        <h6 class="mb-2">Signature Specimen</h6>
                        <div class="p-2 bg-brand-soft rounded-3" style="display:inline-block;">
                            <img src="{{ asset('storage/'.$member->signature_image) }}" alt="Signature" style="max-width:280px; height:auto;">
                        </div>
                    </div>
                @endif

                {{-- Documents --}}
                <div class="col-lg-6">
                    <div class="card card-shadow h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Documents</h5>

                            @php
                                $docs = [
                                    ['label' => 'National ID / Passport Scan', 'key' => 'upload_id', 'icon' => 'mdi-card-account-details'],
                                    ['label' => 'Curriculum Vitae', 'key' => 'upload_cv', 'icon' => 'mdi-file-account'],
                                    ['label' => 'Appointment Letter', 'key' => 'appointment_letter', 'icon' => 'mdi-file-document'],
                                    ['label' => 'Gazette Notice', 'key' => 'gazettee_notice', 'icon' => 'mdi-file-certificate'],
                                    ['label' => 'Acceptance Letter', 'key' => 'acceptance_letter', 'icon' => 'mdi-file-check'],
                                    ['label' => 'Signature Specimen', 'key' => 'signature_image', 'icon' => 'mdi-gesture'],

                                ];
                            @endphp

                            <ul class="list-group list-group-flush">
                                @foreach($docs as $d)
                                    @php
                                        $path = $member->{$d['key']} ?? null;
                                        $url  = $fileLink($path);
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <i class="mdi {{ $d['icon'] }} text-brand me-1"></i> {{ $d['label'] }}
                                        </div>
                                        <div>
                                            @if($url)
                                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    View / Download
                                                </a>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Optional: Bulk download if you have a route to zip them --}}
                            {{-- <a href="{{ route('board.profile.documents.zip', $member->id) }}" class="btn btn-sm btn-brand mt-3">Download All</a> --}}
                        </div>
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="col-12">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <h5 class="mb-3">Profile Checklist</h5>
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
                                    'signature_image' => 'Signature Specimen',
                                ];
                            @endphp
                            <div class="row g-2">
                                @foreach($labels as $key => $label)
                                    <div class="col-md-4 col-lg-3">
                                        <div class="p-2 rounded d-flex align-items-center justify-content-between {{ !empty($checklist[$key]) ? 'bg-light' : 'bg-brand-soft' }}">
                                            <span class="{{ !empty($checklist[$key]) ? '' : 'text-muted' }}">{{ $label }}</span>
                                            @if(!empty($checklist[$key]))
                                                <i class="mdi mdi-check-circle text-success"></i>
                                            @else
                                                <i class="mdi mdi-alert-circle text-warning"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3 text-end">
                                {{-- Hook to edit page if/when available --}}
                                 <a href="{{ route('board.profile.edit', $member->id) }}" class="btn btn-brand btn-sm">Update Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div> {{-- /container-fluid --}}
    </div>
@endsection
