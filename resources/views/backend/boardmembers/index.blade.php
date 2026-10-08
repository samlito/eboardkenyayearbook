@extends('admin_dashboard')
@section('admin')

@php
    use Carbon\Carbon;
    $placeholderPhoto = asset('images/no-photo.png');
    $today = Carbon::today();
@endphp

<style>
    .bm-page-header {
        background: linear-gradient(135deg, #2b2b2b 0%, #212121 100%);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    .bm-page-header h4 { margin: 0; font-weight: 600; }
    .bm-page-header p { margin: 0.25rem 0 0; opacity: 0.8; font-size: 0.9rem; }

    .bm-stats-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .bm-stat-card {
        flex: 1;
        min-width: 160px;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .bm-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .bm-stat-icon.total { background: #efefef; color: #181818; }
    .bm-stat-icon.active { background: #e8f5e9; color: #2e7d32; }
    .bm-stat-icon.inactive { background: #fce4ec; color: #c62828; }
    .bm-stat-icon.expiring { background: #fff3e0; color: #e65100; }
    .bm-stat-num { font-size: 1.5rem; font-weight: 700; color: #181818; line-height: 1; }
    .bm-stat-label { font-size: 0.78rem; color: #6c757d; margin-top: 2px; }

    .bm-table-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    .bm-table-toolbar {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .bm-filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .bm-filter-btn {
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        border: 1px solid #dee2e6;
        background: #fff;
        font-size: 0.8rem;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }
    .bm-filter-btn:hover { border-color: #9B0C23; color: #181818; }
    .bm-filter-btn.active { background: #2b2b2b; color: #fff; border-color: #9B0C23; }

    .bm-member-row { transition: background 0.15s; }
    .bm-member-row:hover { background: #fcfcfc !important; }

    .bm-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
        flex-shrink: 0;
    }
    .bm-member-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .bm-member-name { font-weight: 600; color: #181818; font-size: 0.9rem; }
    .bm-member-email { font-size: 0.78rem; color: #6c757d; }

    .bm-badge {
        padding: 0.3rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .bm-badge-active { background: #e8f5e9; color: #2e7d32; }
    .bm-badge-inactive { background: #fce4ec; color: #c62828; }
    .bm-badge-expired { background: #fff3e0; color: #e65100; }
    .bm-badge-chairperson { background: #efefef; color: #181818; }
    .bm-badge-member { background: #f3f4f6; color: #4b5563; }

    .bm-term-info { font-size: 0.78rem; color: #6c757d; }
    .bm-term-warn { color: #e65100; font-weight: 500; }
    .bm-term-expired { color: #c62828; font-weight: 600; }

    .bm-action-group { display: flex; gap: 4px; }
    .bm-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .bm-action-btn:hover { border-color: #9B0C23; color: #181818; background: #f8f8f8; }
    .bm-action-btn.view { color: #e00214; }
    .bm-action-btn.view:hover { background: #f3f3f3; border-color: #e00214; }
    .bm-action-btn.edit { color: #e67e22; }
    .bm-action-btn.edit:hover { background: #fef5e7; border-color: #e67e22; }
    .bm-action-btn.deactivate { color: #dc3545; }
    .bm-action-btn.deactivate:hover { background: #fce4ec; border-color: #dc3545; }
    .bm-action-btn.activate { color: #198754; }
    .bm-action-btn.activate:hover { background: #e8f5e9; border-color: #198754; }
    .bm-action-btn.create-user { color: #238b47; }
    .bm-action-btn.create-user:hover { background: #f6f6f6; border-color: #238b47; }
    .bm-user-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 500;
    }
    .bm-user-badge.linked { background: #e8f5e9; color: #2e7d32; }
    .bm-user-badge.not-linked { background: #fff3e0; color: #e65100; }

    .bm-empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .bm-empty-icon { font-size: 4rem; color: #dee2e6; }
    .bm-empty-state h5 { color: #6c757d; font-weight: 600; margin-top: 1rem; }
    .bm-empty-state p { color: #adb5bd; max-width: 400px; margin: 0.5rem auto 1.5rem; }

    .bm-tooltip-inner {
        max-width: 240px;
        font-size: 0.78rem;
    }

    @media (max-width: 768px) {
        .bm-stats-row { flex-direction: column; }
        .bm-table-toolbar { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="bm-page-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4><i class="mdi mdi-account-group-outline me-2"></i>Board Members</h4>
                    <p>Manage {{ setting('institution_acronym', 'eBoard') }} board composition and member records</p>
                </div>
                <a href="{{ route('boardmembers.add') }}" class="btn btn-light btn-sm">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Add Board Member
                </a>
            </div>
        </div>

        {{-- Stats --}}
        @php
            $totalMembers = $boardmembers->count();
            $activeMembers = $boardmembers->where('status', 'Active')->count();
            $inactiveMembers = $boardmembers->where('status', '!=', 'Active')->count();
            $expiringMembers = $boardmembers->filter(function($m) use ($today) {
                return $m->status === 'Active' && $m->end_date && Carbon::parse($m->end_date)->lte($today->copy()->addDays(90)) && Carbon::parse($m->end_date)->gte($today);
            })->count();
            $expiredMembers = $boardmembers->filter(function($m) use ($today) {
                return $m->status === 'Active' && $m->end_date && Carbon::parse($m->end_date)->lt($today);
            })->count();
        @endphp

        <div class="bm-stats-row">
            <div class="bm-stat-card">
                <div class="bm-stat-icon total"><i class="mdi mdi-account-group"></i></div>
                <div>
                    <div class="bm-stat-num">{{ $totalMembers }}</div>
                    <div class="bm-stat-label">Total Members</div>
                </div>
            </div>
            <div class="bm-stat-card">
                <div class="bm-stat-icon active"><i class="mdi mdi-check-circle"></i></div>
                <div>
                    <div class="bm-stat-num">{{ $activeMembers }}</div>
                    <div class="bm-stat-label">Active</div>
                </div>
            </div>
            <div class="bm-stat-card">
                <div class="bm-stat-icon inactive"><i class="mdi mdi-close-circle"></i></div>
                <div>
                    <div class="bm-stat-num">{{ $inactiveMembers }}</div>
                    <div class="bm-stat-label">Inactive</div>
                </div>
            </div>
            <div class="bm-stat-card">
                <div class="bm-stat-icon expiring"><i class="mdi mdi-clock-alert-outline"></i></div>
                <div>
                    <div class="bm-stat-num">{{ $expiringMembers + $expiredMembers }}</div>
                    <div class="bm-stat-label">Term Expiring / Expired</div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bm-table-card">
            <div class="bm-table-toolbar">
                <div class="bm-filter-group">
                    <button class="bm-filter-btn active" data-filter="all">All ({{ $totalMembers }})</button>
                    <button class="bm-filter-btn" data-filter="active">Active ({{ $activeMembers }})</button>
                    <button class="bm-filter-btn" data-filter="inactive">Inactive ({{ $inactiveMembers }})</button>
                    @if($expiredMembers > 0)
                        <button class="bm-filter-btn" data-filter="expired">Term Expired ({{ $expiredMembers }})</button>
                    @endif
                </div>
                <div>
                    <input type="text" id="bmSearch" class="form-control form-control-sm" placeholder="Search members..." style="min-width: 200px; border-radius: 20px; font-size: 0.85rem;">
                </div>
            </div>

            @if($boardmembers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="bmTable">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="width: 45px; padding-left: 1.25rem;">#</th>
                                <th>Member</th>
                                <th>Designation</th>
                                <th>Phone</th>
                                <th>County</th>
                                <th>Term</th>
                                <th>Status</th>
                                <th>Account</th>
                                <th style="width: 160px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boardmembers as $key => $member)
                                @php
                                    $isExpired = $member->status === 'Active' && $member->end_date && Carbon::parse($member->end_date)->lt($today);
                                    $isExpiring = $member->status === 'Active' && $member->end_date && Carbon::parse($member->end_date)->lte($today->copy()->addDays(90)) && Carbon::parse($member->end_date)->gte($today);
                                    $daysLeft = $member->end_date ? (int) $today->diffInDays(Carbon::parse($member->end_date), false) : null;
                                    $filterClass = $member->status === 'Active' ? ($isExpired ? 'expired' : 'active') : 'inactive';
                                    $photoSrc = $member->photo ? asset($member->photo) : $placeholderPhoto;
                                @endphp
                                <tr class="bm-member-row" data-status="{{ $filterClass }}">
                                    <td style="padding-left: 1.25rem; color: #adb5bd; font-size: 0.85rem;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="bm-member-info">
                                            <img src="{{ $photoSrc }}" alt="" class="bm-avatar" onerror="this.src='{{ $placeholderPhoto }}';">
                                            <div>
                                                <div class="bm-member-name">{{ $member->assignsalutation->name ?? '' }} {{ $member->names }}</div>
                                                <div class="bm-member-email">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($member->designation === 'Chairperson')
                                            <span class="bm-badge bm-badge-chairperson">{{ $member->designation }}</span>
                                        @else
                                            <span class="bm-badge bm-badge-member">{{ $member->designation ?? 'Member' }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem;">{{ $member->phone }}</td>
                                    <td style="font-size: 0.85rem;">{{ $member->county->county_name ?? '—' }}</td>
                                    <td>
                                        @if($member->appointment_date)
                                            <div class="bm-term-info">
                                                {{ Carbon::parse($member->appointment_date)->format('d M Y') }}
                                                @if($member->end_date)
                                                    <br>to {{ Carbon::parse($member->end_date)->format('d M Y') }}
                                                    @if($isExpired)
                                                        <br><span class="bm-term-expired"><i class="mdi mdi-alert-circle"></i> Expired</span>
                                                    @elseif($isExpiring)
                                                        <br><span class="bm-term-warn"><i class="mdi mdi-clock-alert-outline"></i> {{ $daysLeft }} days left</span>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isExpired)
                                            <span class="bm-badge bm-badge-expired">Term Expired</span>
                                        @elseif($member->status === 'Active')
                                            <span class="bm-badge bm-badge-active">Active</span>
                                        @else
                                            <span class="bm-badge bm-badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasUser = $member->user_id || ($member->user ?? null);
                                        @endphp
                                        @if($hasUser)
                                            <span class="bm-user-badge linked"><i class="mdi mdi-check-circle"></i> Linked</span>
                                        @else
                                            <span class="bm-user-badge not-linked"><i class="mdi mdi-alert-circle"></i> No Account</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="bm-action-group justify-content-center">
                                            <a href="{{ route('boardmembers.show', $member->id) }}" class="bm-action-btn view" title="View Profile">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                            <a href="{{ route('boardmembers.edit', $member->id) }}" class="bm-action-btn edit" title="Edit">
                                                <i class="mdi mdi-pencil-outline"></i>
                                            </a>
                                            @if(!$hasUser && $member->status === 'Active')
                                                <form action="{{ route('boardmembers.createUser', $member->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Create a user account for {{ $member->names }}? A welcome email with login credentials will be sent.')">
                                                    @csrf
                                                    <button type="submit" class="bm-action-btn create-user" title="Create User Account & Send Invitation">
                                                        <i class="mdi mdi-account-key-outline"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('boardmembers.toggleStatus', $member->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ $member->status === 'Active' ? 'Deactivate this member? Their user account will also be disabled.' : 'Activate this member? Their user account will also be enabled.' }}')">
                                                @csrf
                                                @if($member->status === 'Active')
                                                    <button type="submit" class="bm-action-btn deactivate" title="Deactivate Member & Disable Account">
                                                        <i class="mdi mdi-account-off-outline"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="bm-action-btn activate" title="Activate Member & Enable Account">
                                                        <i class="mdi mdi-account-check-outline"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bm-empty-state">
                    <div class="bm-empty-icon"><i class="mdi mdi-account-group-outline"></i></div>
                    <h5>No Board Members Yet</h5>
                    <p>Start by adding the first board member to the {{ setting('institution_acronym', 'eBoard') }} system.</p>
                    <a href="{{ route('boardmembers.add') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Add First Board Member
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
(function() {
    // Filter buttons
    const filterBtns = document.querySelectorAll('.bm-filter-btn');
    const rows = document.querySelectorAll('.bm-member-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            rows.forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Search
    const searchInput = document.getElementById('bmSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
            filterBtns.forEach(b => b.classList.remove('active'));
            filterBtns[0].classList.add('active');
        });
    }
})();
</script>

@endsection
