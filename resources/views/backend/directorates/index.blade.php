@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1">Directorates</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Manage organizational directorates, divisions, and director assignments</p>
                </div>
                <a href="{{ route('directorates.create') }}" class="btn btn-success">
                    <i class="mdi mdi-plus me-1"></i> Add Directorate
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered twwda-datatable" data-export-title="{{ setting('institution_acronym', 'ORG') }} Directorates Report" style="width:100%;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Directorate Name</th>
                            <th>Assigned Director</th>
                            <th>Divisions</th>
                            <th>No. of Divisions</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($directorates as $directorate)
                            @php
                                $mapping = $directorMappings[$directorate->id] ?? null;
                                $director = $mapping?->user;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $directorate->name }}</span>
                                </td>
                                <td>
                                    @if($director)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="twwda-director-avatar">
                                                <i class="mdi mdi-account-tie"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13.5px;">{{ $director->name }}</span>
                                                <span class="text-muted" style="font-size: 11.5px;">{{ $director->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="twwda-no-director">
                                            <i class="mdi mdi-account-off-outline me-1"></i> No director assigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($directorate->divisions as $division)
                                        <span class="badge bg-info mb-1">{{ $division->name }}</span>
                                    @empty
                                        <span class="text-muted" style="font-size: 12px;">No divisions assigned</span>
                                    @endforelse
                                </td>
                                <td>{{ $directorate->divisions->count() }}</td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        {{-- Assign / Change Director --}}
                                        <button type="button" class="btn btn-sm {{ $director ? 'btn-outline-warning' : 'btn-warning' }}"
                                                onclick="openAssignDirector({{ $directorate->id }}, '{{ addslashes($directorate->name) }}', {{ $director ? $director->id : 'null' }})">
                                            <i class="mdi mdi-account-tie-outline"></i>
                                            {{ $director ? 'Change' : 'Assign' }} Director
                                        </button>

                                        {{-- Remove Director (only if one exists) --}}
                                        @if($director)
                                            <form action="{{ route('directorates.remove-director', $directorate->id) }}" method="POST" class="d-inline remove-director-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmRemoveDirector(this, '{{ addslashes($director->name) }}', '{{ addslashes($directorate->name) }}')">
                                                    <i class="mdi mdi-account-remove-outline"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('directorates.edit', $directorate->id) }}" class="btn btn-primary btn-sm">
                                            <i class="mdi mdi-pencil-outline"></i> Edit
                                        </a>
                                        <form action="{{ route('directorates.destroy', $directorate->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this directorate? All divisions under it will become unassigned.')">
                                                <i class="mdi mdi-trash-can-outline"></i> Delete
                                            </button>
                                        </form>
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

    {{-- Hidden form for assigning a director --}}
    <form id="assign-director-form" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="user_id" id="assign-director-user-id">
    </form>

    <style>
        .twwda-director-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #b45309, #f59e0b);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px; flex-shrink: 0;
        }
        .twwda-no-director {
            display: inline-flex; align-items: center; gap: 4px;
            color: #9ca3af; font-size: 12.5px; font-style: italic;
        }
    </style>

    <script>
        // Director users data for the dropdown
        const directorUsers = @json($directorUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email]));

        function openAssignDirector(directorateId, directorateName, currentDirectorId) {
            // Build options HTML
            let optionsHtml = '<div style="text-align:left; max-height: 300px; overflow-y: auto;">';

            if (directorUsers.length === 0) {
                optionsHtml += '<p class="text-muted">No users with the Director role found. Please create a Director user first.</p>';
                optionsHtml += '</div>';

                Swal.fire({
                    title: 'No Directors Available',
                    html: optionsHtml,
                    icon: 'warning',
                    confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
                });
                return;
            }

            optionsHtml += '<select id="swal-director-select" class="form-select" style="font-size: 14px;">';
            optionsHtml += '<option value="">-- Select a Director --</option>';
            directorUsers.forEach(function(u) {
                const selected = (u.id === currentDirectorId) ? ' selected' : '';
                const badge = (u.id === currentDirectorId) ? ' (current)' : '';
                optionsHtml += `<option value="${u.id}"${selected}>${u.name} &mdash; ${u.email}${badge}</option>`;
            });
            optionsHtml += '</select>';
            optionsHtml += '</div>';

            Swal.fire({
                title: 'Assign Director',
                html: `<p style="font-size: 13.5px; color: #6b7280; margin-bottom: 12px;">Select a Director for <strong>${directorateName}</strong></p>${optionsHtml}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-check"></i> Assign',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#099139',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const selectedVal = document.getElementById('swal-director-select').value;
                    if (!selectedVal) {
                        Swal.showValidationMessage('Please select a director');
                        return false;
                    }
                    return selectedVal;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('assign-director-form');
                    form.action = `/directorates/${directorateId}/assign-director`;
                    document.getElementById('assign-director-user-id').value = result.value;
                    form.submit();
                }
            });
        }

        function confirmRemoveDirector(btn, directorName, directorateName) {
            Swal.fire({
                title: 'Remove Director?',
                html: `Remove <strong>${directorName}</strong> as Director of <strong>${directorateName}</strong>?<br><br><span style="font-size: 12.5px; color: #dc2626;">Board papers from this directorate will route directly to CEO until a new director is assigned.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-account-remove"></i> Remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        }
    </script>
@endsection
