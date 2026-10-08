@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="page-title mb-1">Roles Management</h4>
                        <p class="text-muted mb-0">Manage system roles, permissions, and display labels</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('add.roles') }}" class="btn btn-primary rounded-pill px-3">
                            <i class="mdi mdi-plus-circle-outline me-1"></i> Add Role
                        </a>
                        <a href="{{ route('all.roles.permission') }}" class="btn btn-outline-primary rounded-pill px-3">
                            <i class="mdi mdi-shield-key-outline me-1"></i> Role Permissions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: {{ setting('primary_color', '#1a1a1a') }}15;">
                                <i class="mdi mdi-shield-account-outline" style="font-size: 22px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $roles->count() }}</h3>
                                <p class="text-muted mb-0 small">Total Roles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #10b98115;">
                                <i class="mdi mdi-account-group-outline" style="font-size: 22px; color: #10b981;"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $roles->sum('users_count') }}</h3>
                                <p class="text-muted mb-0 small">Users Assigned</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #f59e0b15;">
                                <i class="mdi mdi-lock-check-outline" style="font-size: 22px; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $roles->where('permissions_count', '>', 0)->count() }}</h3>
                                <p class="text-muted mb-0 small">With Permissions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #6366f115;">
                                <i class="mdi mdi-cog-outline" style="font-size: 22px; color: #ec2c3c;"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ collect($systemRoles)->intersect($roles->pluck('name'))->count() }}</h3>
                                <p class="text-muted mb-0 small">System Roles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert border-0 d-flex align-items-start" style="background: #f7f7f7; border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }} !important;">
                    <i class="mdi mdi-information-outline me-2 mt-1" style="font-size: 20px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                    <div>
                        <strong>System Roles</strong> are required for the eBoard system to function. They cannot be deleted, but their
                        <strong>display labels</strong> can be customized in <a href="{{ route('admin.settings.index') }}">Admin Settings</a> to match your institution's terminology.
                    </div>
                </div>
            </div>
        </div>

        {{-- Roles Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="roles-datatable" class="table table-hover align-middle w-100 mb-0">
                                <thead>
                                    <tr class="text-uppercase small" style="background: #fafafa;">
                                        <th style="width: 50px;">#</th>
                                        <th>Role (System Slug)</th>
                                        <th>Display Label</th>
                                        <th class="text-center">Users</th>
                                        <th class="text-center">Permissions</th>
                                        <th>Type</th>
                                        <th>Created</th>
                                        <th class="text-center" style="width: 140px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $key => $role)
                                    @php
                                        $isSystem = in_array($role->name, $systemRoles);
                                        $labelKey = $roleLabelMap[$role->name] ?? null;
                                        $displayLabel = $labelKey ? setting($labelKey, $role->name) : $role->name;
                                        $roleColors = [
                                            'Superadmin' => ['bg' => '#dc262615', 'color' => '#dc2626', 'icon' => 'mdi-shield-crown-outline'],
                                            'admin' => ['bg' => '#f59e0b15', 'color' => '#f59e0b', 'icon' => 'mdi-shield-star-outline'],
                                            'CEO' => ['bg' => '#7c3aed15', 'color' => '#0f9f41', 'icon' => 'mdi-account-tie-outline'],
                                            'Corporation Secretary' => ['bg' => '#005bac15', 'color' => '#222222', 'icon' => 'mdi-briefcase-account-outline'],
                                            'Director' => ['bg' => '#0891b215', 'color' => '#0baf44', 'icon' => 'mdi-account-supervisor-outline'],
                                            'Division Head' => ['bg' => '#059669 15', 'color' => '#059669', 'icon' => 'mdi-account-hard-hat-outline'],
                                            'DD Internal Audit' => ['bg' => '#d9770615', 'color' => '#d97706', 'icon' => 'mdi-clipboard-check-outline'],
                                            'DD ICT' => ['bg' => '#4f46e515', 'color' => '#df1f2f', 'icon' => 'mdi-monitor-dashboard'],
                                            'Board Member' => ['bg' => '#10b98115', 'color' => '#10b981', 'icon' => 'mdi-account-group-outline'],
                                            'Officer' => ['bg' => '#6b728015', 'color' => '#6b7280', 'icon' => 'mdi-account-outline'],
                                        ];
                                        $rc = $roleColors[$role->name] ?? ['bg' => '#6b728015', 'color' => '#6b7280', 'icon' => 'mdi-account-circle-outline'];
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: {{ $rc['bg'] }}; flex-shrink: 0;">
                                                    <i class="mdi {{ $rc['icon'] }}" style="font-size: 18px; color: {{ $rc['color'] }};"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ $role->name }}</span>
                                                    @if($role->guard_name !== 'web')
                                                        <span class="badge bg-soft-secondary text-secondary ms-1" style="font-size: 10px;">{{ $role->guard_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($labelKey && $displayLabel !== $role->name)
                                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $rc['bg'] }}; color: {{ $rc['color'] }}; font-size: 12px;">
                                                    {{ $displayLabel }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ $displayLabel }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($role->users_count > 0)
                                                <span class="badge bg-soft-primary text-primary rounded-pill px-2" style="font-size: 12px;">
                                                    {{ $role->users_count }}
                                                </span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($role->permissions_count > 0)
                                                <span class="badge bg-soft-success text-success rounded-pill px-2" style="font-size: 12px;">
                                                    {{ $role->permissions_count }}
                                                </span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isSystem)
                                                <span class="badge bg-soft-info text-info rounded-pill px-2" style="font-size: 11px;">
                                                    <i class="mdi mdi-lock-outline me-1" style="font-size: 11px;"></i>System
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary rounded-pill px-2" style="font-size: 11px;">Custom</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $role->created_at ? $role->created_at->format('d M Y') : '—' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('edit.roles', $role->id) }}" class="btn btn-sm btn-soft-primary rounded-pill px-2" data-bs-toggle="tooltip" title="Edit Role">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </a>
                                                <a href="{{ route('admin.edit.roles', $role->id) }}" class="btn btn-sm btn-soft-info rounded-pill px-2" data-bs-toggle="tooltip" title="Manage Permissions">
                                                    <i class="mdi mdi-shield-key-outline"></i>
                                                </a>
                                                @if(!$isSystem)
                                                    <a href="{{ route('delete.roles', $role->id) }}" class="btn btn-sm btn-soft-danger rounded-pill px-2" id="delete" data-bs-toggle="tooltip" title="Delete Role">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-soft-secondary rounded-pill px-2 disabled" data-bs-toggle="tooltip" title="System role cannot be deleted">
                                                        <i class="mdi mdi-lock-outline"></i>
                                                    </button>
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
            </div>
        </div>

    </div>
</div>

<style>
    .btn-soft-primary { background: #005bac15; color: #222222; border: none; }
    .btn-soft-primary:hover { background: #005bac25; color: #222222; }
    .btn-soft-info { background: #0891b215; color: #0baf44; border: none; }
    .btn-soft-info:hover { background: #0891b225; color: #0baf44; }
    .btn-soft-danger { background: #dc262615; color: #dc2626; border: none; }
    .btn-soft-danger:hover { background: #dc262625; color: #dc2626; }
    .btn-soft-secondary { background: #6b728015; color: #6b7280; border: none; }
    .bg-soft-primary { background: #005bac15 !important; }
    .bg-soft-success { background: #10b98115 !important; }
    .bg-soft-info { background: #0891b215 !important; }
    .bg-soft-secondary { background: #6b728015 !important; }

    #roles-datatable th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #6b7280;
        border-bottom: 2px solid #e5e7eb;
    }
    #roles-datatable td {
        border-bottom: 1px solid #f3f4f6;
        padding: 12px 10px;
    }
    #roles-datatable tbody tr:hover {
        background: #fafafa;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#roles-datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "",
                searchPlaceholder: "Search roles...",
                lengthMenu: "Show _MENU_ roles",
                info: "Showing _START_ to _END_ of _TOTAL_ roles",
                emptyTable: "No roles found",
            },
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-5"i><"col-sm-7"p>>',
            columnDefs: [
                { orderable: false, targets: [7] },
            ],
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control-sm rounded-pill ps-3').css('width', '250px');
                $('.dataTables_length select').addClass('form-select-sm rounded-pill');
            }
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush

@endsection
