@extends('admin_dashboard')
@section('admin')

<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0"><i class="mdi mdi-account-group-outline text-primary me-1"></i> User Management</h3>
            <p class="text-muted mb-0">Manage system users, roles and access control.</p>
        </div>
        <div class="d-flex gap-2">
            @can('user.create')
                <a href="{{ route('users.add') }}" class="btn btn-primary">
                    <i class="mdi mdi-account-plus-outline me-1"></i> Add New User
                </a>
            @endcan
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalUsers   = $users->count();
        $activeUsers  = $users->where('status', 'active')->count();
        $inactiveUsers = $users->whereIn('status', ['inactive', 'suspended', 'disabled'])->count();
        $lockedUsers  = $users->where('locked', true)->count();
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="mdi mdi-account-group text-primary" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold">{{ $totalUsers }}</h3>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="mdi mdi-check-circle-outline text-success" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-success">{{ $activeUsers }}</h3>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-warning">{{ $inactiveUsers }}</h3>
                            <small class="text-muted">Inactive / Suspended</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="mdi mdi-lock-outline text-danger" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-danger">{{ $lockedUsers }}</h3>
                            <small class="text-muted">Locked Accounts</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Users DataTable --}}
    <div class="card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="mdi mdi-format-list-bulleted me-1"></i> All Users</h5>
            <div class="d-flex gap-2">
                <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                    <option value="disabled">Disabled</option>
                </select>
                <select id="roleFilter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Roles</option>
                    @foreach($users->pluck('roles')->flatten()->pluck('name')->unique()->sort() as $roleName)
                        <option value="{{ $roleName }}">{{ $roleName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover twwda-datatable w-100" data-export-title="Users List">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Staff No.</th>
                        <th>Role</th>
                        <th>Directorate / Division</th>
                        <th>Status</th>
                        <th width="130">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $item)
                        @php
                            $roleName = $item->roles->first()->name ?? 'No Role';
                            $dirMapping = $item->activeDirectorateMapping;
                            $divisionName = $item->division?->name ?? null;
                            $directorateName = $dirMapping?->directorate?->name ?? null;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; min-width: 36px;">
                                        <span class="fw-bold text-primary" style="font-size: 13px;">{{ strtoupper(substr($item->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold d-block" style="font-size: 13px;">{{ $item->name }}</span>
                                        <small class="text-muted">{{ $item->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px;">{{ $item->phone ?? '—' }}</span>
                            </td>
                            <td>
                                <code style="font-size: 12px;">{{ $item->staff_number ?? '—' }}</code>
                            </td>
                            <td>
                                @php
                                    $roleColors = [
                                        'Superadmin' => 'danger',
                                        'CEO' => 'success',
                                        'Corporation Secretary' => 'primary',
                                        'CS-Assistant' => 'info',
                                        'Board Member' => 'warning',
                                        'Division Head' => 'secondary',
                                        'Director' => 'dark',
                                    ];
                                    $rc = $roleColors[$roleName] ?? 'secondary';
                                @endphp
                                <span class="badge bg-soft-{{ $rc }} text-{{ $rc }}">{{ $roleName }}</span>
                            </td>
                            <td style="font-size: 12px;">
                                @if($directorateName)
                                    <span class="fw-semibold">{{ $directorateName }}</span>
                                    @if($divisionName)
                                        <br><small class="text-muted">{{ $divisionName }}</small>
                                    @endif
                                @elseif($divisionName)
                                    <span>{{ $divisionName }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = [
                                        'active'    => ['bg-success', 'mdi-check-circle-outline'],
                                        'inactive'  => ['bg-secondary', 'mdi-minus-circle-outline'],
                                        'suspended' => ['bg-warning', 'mdi-pause-circle-outline'],
                                        'disabled'  => ['bg-danger', 'mdi-close-circle-outline'],
                                    ];
                                    $sb = $statusBadge[$item->status] ?? ['bg-secondary', 'mdi-help-circle-outline'];
                                @endphp
                                <span class="badge {{ $sb[0] }}">
                                    <i class="mdi {{ $sb[1] }} me-1"></i>{{ ucfirst($item->status ?? 'unknown') }}
                                </span>
                                @if($item->locked)
                                    <br><span class="badge bg-danger mt-1"><i class="mdi mdi-lock me-1"></i>Locked</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @can('user.edit')
                                        <a href="{{ route('users.edit', $item->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </a>
                                    @endcan

                                    @can('user.activate')
                                        <button type="button" class="btn btn-sm btn-soft-warning" title="Manage Status"
                                                onclick="openStatusModal({{ $item->id }}, '{{ $item->name }}', '{{ $item->status }}')">
                                            <i class="mdi mdi-shield-account-outline"></i>
                                        </button>
                                    @endcan

                                    @if($item->locked)
                                        <form action="{{ route('users.unlock', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Unlock Account"
                                                    onclick="return confirm('Unlock {{ $item->name }}\'s account?')">
                                                <i class="mdi mdi-lock-open-variant-outline"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Status Management Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-shield-account-outline me-1"></i> Manage User Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('users.changeStatus') }}">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Changing status for: <strong id="statusUserName"></strong></p>
                    <input type="hidden" name="user_id" id="statusUserId">
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold">Select New Status</label>
                        <div class="d-grid gap-2">
                            <label class="border rounded p-3 d-flex align-items-center cursor-pointer" style="cursor: pointer;">
                                <input type="radio" name="action" value="active" class="form-check-input me-3">
                                <div>
                                    <span class="badge bg-success me-1">Active</span>
                                    <small class="text-muted d-block mt-1">User can access the system normally.</small>
                                </div>
                            </label>
                            <label class="border rounded p-3 d-flex align-items-center" style="cursor: pointer;">
                                <input type="radio" name="action" value="inactive" class="form-check-input me-3">
                                <div>
                                    <span class="badge bg-secondary me-1">Inactive</span>
                                    <small class="text-muted d-block mt-1">Temporarily deactivate without removing access.</small>
                                </div>
                            </label>
                            <label class="border rounded p-3 d-flex align-items-center" style="cursor: pointer;">
                                <input type="radio" name="action" value="suspended" class="form-check-input me-3">
                                <div>
                                    <span class="badge bg-warning me-1">Suspended</span>
                                    <small class="text-muted d-block mt-1">Suspend access pending review or investigation.</small>
                                </div>
                            </label>
                            <label class="border rounded p-3 d-flex align-items-center" style="cursor: pointer;">
                                <input type="radio" name="action" value="disabled" class="form-check-input me-3">
                                <div>
                                    <span class="badge bg-danger me-1">Disabled</span>
                                    <small class="text-muted d-block mt-1">Permanently disable this account.</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-soft-primary { color: {{ setting('primary_color', '#1a1a1a') }}; background: #f3f3f3; border: none; }
    .btn-soft-primary:hover { background: #e6e6e6; color: {{ setting('secondary_color', '#2b2b2b') }}; }
    .btn-soft-warning { color: #856404; background: #fff3cd; border: none; }
    .btn-soft-warning:hover { background: #ffe69c; color: #664d03; }
    .btn-soft-danger { color: #842029; background: #f8d7da; border: none; }
    .btn-soft-danger:hover { background: #f1aeb5; color: #58151c; }
</style>
@endpush

@push('scripts')
<script>
function openStatusModal(userId, userName, currentStatus) {
    document.getElementById('statusUserId').value = userId;
    document.getElementById('statusUserName').textContent = userName;
    // Pre-select current status
    var radios = document.querySelectorAll('#statusModal input[name="action"]');
    radios.forEach(function(r) {
        r.checked = (r.value === currentStatus);
    });
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

$(document).ready(function() {
    var table = $('.twwda-datatable').DataTable();

    // Status filter
    $('#statusFilter').on('change', function() {
        table.column(6).search(this.value).draw();
    });

    // Role filter
    $('#roleFilter').on('change', function() {
        table.column(4).search(this.value).draw();
    });
});
</script>
@endpush
@endsection
