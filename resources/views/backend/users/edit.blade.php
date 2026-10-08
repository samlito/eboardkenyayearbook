@extends('admin_dashboard')
@section('admin')

<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Users
            </a>
            <h3 class="mb-0"><i class="mdi mdi-account-edit-outline text-primary me-1"></i> Edit User</h3>
            <p class="text-muted mb-0">{{ $user->name }} &mdash; {{ $user->email }}</p>
        </div>
        <div>
            @php
                $statusColors = ['active' => 'success', 'inactive' => 'secondary', 'suspended' => 'warning', 'disabled' => 'danger'];
                $sc = $statusColors[$user->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $sc }} px-3 py-2" style="font-size: 13px;">{{ ucfirst($user->status ?? 'unknown') }}</span>
            @if($user->locked)
                <span class="badge bg-danger px-3 py-2" style="font-size: 13px;"><i class="mdi mdi-lock me-1"></i> Locked</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="mdi mdi-account-circle-outline me-1 text-primary"></i> User Details</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> <strong>Please correct the following:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="userForm" method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="mdi mdi-phone-outline"></i></span>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', $user->phone) }}" required>
                                    </div>
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Staff Number --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Staff Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="mdi mdi-badge-account-outline"></i></span>
                                        <input type="text" name="staff_number" class="form-control @error('staff_number') is-invalid @enderror"
                                               value="{{ old('staff_number', $user->staff_number) }}" required>
                                    </div>
                                    @error('staff_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                    <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Division (for Division Head) --}}
                            <div class="col-md-6 division-field" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Division <span class="text-danger">*</span></label>
                                    <select name="division_id" class="form-select @error('division_id') is-invalid @enderror">
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}"
                                                {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                                {{ $division->name }}
                                                @if($division->directorate) ({{ $division->directorate->name }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Directorate (for Director) --}}
                            @php
                                $currentDirectorateId = $user->activeDirectorateMapping?->directorate_id;
                            @endphp
                            <div class="col-md-6 directorate-field" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Directorate <span class="text-danger">*</span></label>
                                    <select name="directorate_id" class="form-select @error('directorate_id') is-invalid @enderror">
                                        <option value="">Select Directorate</option>
                                        @foreach($directorates as $directorate)
                                            <option value="{{ $directorate->id }}"
                                                {{ old('directorate_id', $currentDirectorateId) == $directorate->id ? 'selected' : '' }}>
                                                {{ $directorate->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">The directorate this Director will oversee.</small>
                                    @error('directorate_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-light">
                                <i class="mdi mdi-close me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: 0.05em;">Account Info</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; min-width: 56px;">
                            <span class="fw-bold text-primary" style="font-size: 20px;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->roles->first()->name ?? 'No Role' }}</small>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                        <tr>
                            <td class="text-muted" style="width: 110px;">Email:</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Staff No.:</td>
                            <td><code>{{ $user->staff_number ?? '—' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '&mdash;' }}</td>
                        </tr>
                        @if($user->last_login_at)
                        <tr>
                            <td class="text-muted">Last Login:</td>
                            <td>{{ \Carbon\Carbon::parse($user->last_login_at)->format('d M Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($user->activeDirectorateMapping)
                <div class="card border-start border-primary border-3">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Current Directorate Mapping</h6>
                        <p class="fw-semibold mb-1">{{ $user->activeDirectorateMapping->directorate->name ?? 'N/A' }}</p>
                        <small class="text-muted">Role Type: {{ ucfirst(str_replace('_', ' ', $user->activeDirectorateMapping->role_type)) }}</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function toggleRoleFields() {
        var role = $('#roleSelect').val();

        if (role === 'Division Head') {
            $('.division-field').slideDown(200);
        } else {
            $('.division-field').slideUp(200).find('select').val('');
        }

        if (role === 'Director') {
            $('.directorate-field').slideDown(200);
        } else {
            $('.directorate-field').slideUp(200).find('select').val('');
        }
    }

    toggleRoleFields();
    $('#roleSelect').on('change', toggleRoleFields);

    $('#userForm').validate({
        rules: {
            name: { required: true },
            email: { required: true, email: true },
            phone: { required: true },
            staff_number: { required: true },
            role: { required: true },
        },
        messages: {
            name: { required: 'Please enter the user name' },
            email: { required: 'Please enter the email', email: 'Enter a valid email' },
            phone: { required: 'Please enter the phone number' },
            staff_number: { required: 'Please enter the staff number' },
            role: { required: 'Please select a role' },
        },
        errorElement: 'small',
        errorPlacement: function(error, element) {
            error.addClass('text-danger');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) { $(element).addClass('is-invalid'); },
        unhighlight: function(element) { $(element).removeClass('is-invalid'); },
    });
});
</script>
@endpush
@endsection
