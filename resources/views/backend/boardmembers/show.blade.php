@extends('admin_dashboard')

@section('admin')
@php
    use Carbon\Carbon;
    $placeholderPhoto = asset('images/no-photo.png');
    $photoSrc = $boardMember->photo ? asset($boardMember->photo) : $placeholderPhoto;
    $today = Carbon::today();
    $isExpired = $boardMember->status === 'Active' && $boardMember->end_date && Carbon::parse($boardMember->end_date)->lt($today);
    $isExpiring = $boardMember->status === 'Active' && $boardMember->end_date && Carbon::parse($boardMember->end_date)->lte($today->copy()->addDays(90)) && Carbon::parse($boardMember->end_date)->gte($today);
    $daysLeft = $boardMember->end_date ? (int) $today->diffInDays(Carbon::parse($boardMember->end_date), false) : null;
@endphp

<style>
    .bm-profile-header {
        background: linear-gradient(135deg, #2b2b2b 0%, #212121 100%);
        border-radius: 12px;
        padding: 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }
    .bm-profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.3);
        object-fit: cover;
        flex-shrink: 0;
    }
    .bm-profile-name { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .bm-profile-designation { opacity: 0.85; font-size: 0.95rem; margin-top: 4px; }
    .bm-profile-meta { display: flex; gap: 1.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
    .bm-profile-meta-item { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; opacity: 0.9; }
    .bm-profile-actions { margin-left: auto; display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .bm-profile-actions .btn { font-size: 0.85rem; border-radius: 8px; }

    .bm-detail-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        height: 100%;
    }
    .bm-detail-title {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #181818;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bm-detail-title i { opacity: 0.6; }

    .bm-info-row {
        display: flex;
        padding: 0.55rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.88rem;
    }
    .bm-info-row:last-child { border-bottom: none; }
    .bm-info-label {
        width: 160px;
        flex-shrink: 0;
        color: #6c757d;
        font-weight: 500;
    }
    .bm-info-value { color: #212529; flex: 1; }

    .bm-term-bar {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .bm-term-bar.warn { background: #fff3e0; border-left: 4px solid #e65100; }
    .bm-term-bar.expired { background: #fce4ec; border-left: 4px solid #c62828; }
    .bm-term-bar.ok { background: #e8f5e9; border-left: 4px solid #2e7d32; }
    .bm-term-icon { font-size: 1.5rem; }
    .bm-term-text { font-size: 0.88rem; }
    .bm-term-text strong { display: block; font-size: 0.95rem; }

    .bm-doc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .bm-doc-item {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
    }
    .bm-doc-item:hover { border-color: #9B0C23; background: #fcfcfc; }
    .bm-doc-item i { font-size: 2rem; color: #181818; display: block; margin-bottom: 0.5rem; }
    .bm-doc-item .doc-name { font-size: 0.82rem; font-weight: 500; color: #495057; }
    .bm-doc-item .doc-status { font-size: 0.72rem; margin-top: 4px; }
    .bm-doc-item a { text-decoration: none; color: inherit; }

    .bm-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .bm-badge-active { background: #e8f5e9; color: #2e7d32; }
    .bm-badge-inactive { background: #fce4ec; color: #c62828; }
    .bm-badge-expired { background: #fff3e0; color: #e65100; }

    @media (max-width: 768px) {
        .bm-profile-header { flex-direction: column; text-align: center; }
        .bm-profile-actions { margin-left: 0; justify-content: center; }
        .bm-profile-meta { justify-content: center; }
        .bm-info-row { flex-direction: column; gap: 2px; }
        .bm-info-label { width: auto; }
    }
</style>

<div class="content">
    <div class="container-fluid">

        {{-- Profile Header --}}
        <div class="bm-profile-header">
            <img src="{{ $photoSrc }}" alt="" class="bm-profile-photo" onerror="this.src='{{ $placeholderPhoto }}';">
            <div>
                <h4 class="bm-profile-name">{{ $boardMember->assignsalutation->name ?? '' }} {{ $boardMember->names }}</h4>
                <div class="bm-profile-designation">
                    {{ $boardMember->designation ?? 'Board Member' }}
                    @if($boardMember->director_type)
                        &middot; {{ $boardMember->director_type }}
                    @endif
                </div>
                <div class="bm-profile-meta">
                    <div class="bm-profile-meta-item">
                        <i class="mdi mdi-email-outline"></i> {{ $boardMember->email }}
                    </div>
                    <div class="bm-profile-meta-item">
                        <i class="mdi mdi-phone-outline"></i> {{ $boardMember->phone }}
                    </div>
                    <div class="bm-profile-meta-item">
                        @if($isExpired)
                            <span class="bm-badge bm-badge-expired">Term Expired</span>
                        @elseif($boardMember->status === 'Active')
                            <span class="bm-badge bm-badge-active">Active</span>
                        @else
                            <span class="bm-badge bm-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bm-profile-actions">
                @php
                    $hasUser = $boardMember->user_id || \App\Models\User::where('email', $boardMember->email)->exists();
                @endphp
                @if(!$hasUser && $boardMember->status === 'Active')
                    <form action="{{ route('boardmembers.createUser', $boardMember->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Create a user account for {{ $boardMember->names }}? A welcome email with login credentials will be sent.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="mdi mdi-account-key-outline me-1"></i> Create User Account
                        </button>
                    </form>
                @endif
                <a href="{{ route('boardmembers.edit', $boardMember->id) }}" class="btn btn-sm btn-warning">
                    <i class="mdi mdi-pencil-outline me-1"></i> Edit
                </a>
                <a href="{{ route('all.boardmembers') }}" class="btn btn-sm btn-light">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        {{-- Term Status Banner --}}
        @if($boardMember->end_date)
            @if($isExpired)
                <div class="bm-term-bar expired mb-3">
                    <div class="bm-term-icon"><i class="mdi mdi-alert-circle" style="color:#c62828;"></i></div>
                    <div class="bm-term-text">
                        <strong>Term Expired</strong>
                        This member's term ended on {{ Carbon::parse($boardMember->end_date)->format('d M Y') }} ({{ abs($daysLeft) }} days ago). Consider deactivating this member.
                    </div>
                    <form action="{{ route('boardmembers.toggleStatus', $boardMember->id) }}" method="POST" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate this board member?')">Deactivate Now</button>
                    </form>
                </div>
            @elseif($isExpiring)
                <div class="bm-term-bar warn mb-3">
                    <div class="bm-term-icon"><i class="mdi mdi-clock-alert-outline" style="color:#e65100;"></i></div>
                    <div class="bm-term-text">
                        <strong>Term Expiring Soon</strong>
                        This member's term ends on {{ Carbon::parse($boardMember->end_date)->format('d M Y') }} — {{ $daysLeft }} days remaining.
                    </div>
                </div>
            @else
                <div class="bm-term-bar ok mb-3">
                    <div class="bm-term-icon"><i class="mdi mdi-check-circle" style="color:#2e7d32;"></i></div>
                    <div class="bm-term-text">
                        <strong>Term Active</strong>
                        Serving until {{ Carbon::parse($boardMember->end_date)->format('d M Y') }} ({{ $daysLeft }} days remaining).
                    </div>
                </div>
            @endif
        @endif

        <div class="row">
            {{-- Left: Personal & Contact --}}
            <div class="col-lg-6">
                <div class="bm-detail-card">
                    <div class="bm-detail-title"><i class="mdi mdi-account-outline"></i> Personal & Contact</div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Full Name</div>
                        <div class="bm-info-value">{{ $boardMember->assignsalutation->name ?? '' }} {{ $boardMember->names }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Email</div>
                        <div class="bm-info-value">{{ $boardMember->email }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Phone</div>
                        <div class="bm-info-value">{{ $boardMember->phone }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">National ID</div>
                        <div class="bm-info-value">{{ $boardMember->idno }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Date of Birth</div>
                        <div class="bm-info-value">{{ $boardMember->date_of_birth ? Carbon::parse($boardMember->date_of_birth)->format('d M Y') : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Right: Role & Address --}}
            <div class="col-lg-6">
                <div class="bm-detail-card">
                    <div class="bm-detail-title"><i class="mdi mdi-shield-account-outline"></i> Role & Address</div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Designation</div>
                        <div class="bm-info-value">{{ $boardMember->designation ?? '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Director Type</div>
                        <div class="bm-info-value">{{ $boardMember->director_type ?? '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Role Type</div>
                        <div class="bm-info-value">{{ $boardMember->role_type ? ucfirst($boardMember->role_type) : '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">County</div>
                        <div class="bm-info-value">{{ $boardMember->county->county_name ?? '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Residence</div>
                        <div class="bm-info-value">{{ $boardMember->place_of_residence ?? '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Postal Address</div>
                        <div class="bm-info-value">{{ $boardMember->postal_address ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Dates --}}
            <div class="col-lg-6">
                <div class="bm-detail-card">
                    <div class="bm-detail-title"><i class="mdi mdi-calendar-range"></i> Tenure & Dates</div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Appointed</div>
                        <div class="bm-info-value">{{ $boardMember->appointment_date ? Carbon::parse($boardMember->appointment_date)->format('d M Y') : '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Term Ends</div>
                        <div class="bm-info-value">
                            @if($boardMember->end_date)
                                {{ Carbon::parse($boardMember->end_date)->format('d M Y') }}
                                @if($isExpired)
                                    <span class="text-danger ms-1"><i class="mdi mdi-alert-circle"></i> Expired</span>
                                @elseif($isExpiring)
                                    <span class="text-warning ms-1"><i class="mdi mdi-clock-alert-outline"></i> {{ $daysLeft }} days left</span>
                                @endif
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Exit Date</div>
                        <div class="bm-info-value">{{ $boardMember->exit_date ? Carbon::parse($boardMember->exit_date)->format('d M Y') : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- User Account --}}
            <div class="col-lg-6">
                <div class="bm-detail-card">
                    <div class="bm-detail-title"><i class="mdi mdi-account-key-outline"></i> User Account</div>
                    @php
                        $linkedUser = $boardMember->user_id ? \App\Models\User::find($boardMember->user_id) : \App\Models\User::where('email', $boardMember->email)->first();
                    @endphp
                    @if($linkedUser)
                        <div class="bm-info-row">
                            <div class="bm-info-label">Account Status</div>
                            <div class="bm-info-value">
                                @if($linkedUser->status === 'active' || $linkedUser->status === null)
                                    <span style="display:inline-flex;align-items:center;gap:5px;background:#e8f5e9;color:#2e7d32;padding:3px 10px;border-radius:12px;font-size:0.8rem;font-weight:600;">
                                        <i class="mdi mdi-check-circle"></i> Active
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:5px;background:#fce4ec;color:#c62828;padding:3px 10px;border-radius:12px;font-size:0.8rem;font-weight:600;">
                                        <i class="mdi mdi-close-circle"></i> {{ ucfirst($linkedUser->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="bm-info-row">
                            <div class="bm-info-label">Last Login</div>
                            <div class="bm-info-value">{{ $linkedUser->last_login_at ? \Carbon\Carbon::parse($linkedUser->last_login_at)->format('d M Y H:i') : 'Never logged in' }}</div>
                        </div>
                        <div class="bm-info-row">
                            <div class="bm-info-label">Account Created</div>
                            <div class="bm-info-value">{{ $linkedUser->created_at->format('d M Y') }}</div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 1.5rem 1rem;">
                            <i class="mdi mdi-account-off-outline" style="font-size: 2.5rem; color: #dee2e6;"></i>
                            <p style="color: #6c757d; font-size: 0.88rem; margin: 0.5rem 0 1rem;">No user account linked to this board member.</p>
                            @if($boardMember->status === 'Active')
                                <form action="{{ route('boardmembers.createUser', $boardMember->id) }}" method="POST" onsubmit="return confirm('Create a user account and send welcome email?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px;">
                                        <i class="mdi mdi-account-plus-outline me-1"></i> Create Account & Send Invite
                                    </button>
                                </form>
                            @else
                                <small class="text-muted">Activate this member first to create a user account.</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Qualifications --}}
            <div class="col-lg-6">
                <div class="bm-detail-card">
                    <div class="bm-detail-title"><i class="mdi mdi-school-outline"></i> Qualifications & Experience</div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Qualifications</div>
                        <div class="bm-info-value">{{ $boardMember->qualifications ?: '—' }}</div>
                    </div>
                    <div class="bm-info-row">
                        <div class="bm-info-label">Experience</div>
                        <div class="bm-info-value">{{ $boardMember->experience_summary ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="bm-detail-card">
            <div class="bm-detail-title"><i class="mdi mdi-file-document-multiple-outline"></i> Documents</div>
            <div class="bm-doc-grid">
                @php
                    $docs = [
                        ['field' => 'photo', 'label' => 'Photo', 'icon' => 'mdi-camera-outline'],
                        ['field' => 'upload_id', 'label' => 'National ID', 'icon' => 'mdi-card-account-details-outline'],
                        ['field' => 'upload_cv', 'label' => 'CV', 'icon' => 'mdi-file-account-outline'],
                        ['field' => 'appointment_letter', 'label' => 'Appointment Letter', 'icon' => 'mdi-file-sign'],
                        ['field' => 'gazettee_notice', 'label' => 'Gazette Notice', 'icon' => 'mdi-newspaper-variant-outline'],
                        ['field' => 'acceptance_letter', 'label' => 'Acceptance Letter', 'icon' => 'mdi-file-check-outline'],
                    ];
                @endphp
                @foreach($docs as $doc)
                    <div class="bm-doc-item">
                        @if($boardMember->{$doc['field']})
                            <a href="{{ asset($boardMember->{$doc['field']}) }}" target="_blank">
                                <i class="mdi {{ $doc['icon'] }}"></i>
                                <div class="doc-name">{{ $doc['label'] }}</div>
                                <div class="doc-status text-success"><i class="mdi mdi-check-circle"></i> Uploaded</div>
                            </a>
                        @else
                            <i class="mdi {{ $doc['icon'] }}" style="color: #dee2e6;"></i>
                            <div class="doc-name" style="color: #adb5bd;">{{ $doc['label'] }}</div>
                            <div class="doc-status text-muted">Not uploaded</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
