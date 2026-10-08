@extends('admin_dashboard')
@section('admin')

<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Users
            </a>
            <h3 class="mb-0"><i class="mdi mdi-account-plus-outline text-primary me-1"></i> Add New User</h3>
            <p class="text-muted mb-0">Create a new system user. A welcome email with login credentials will be sent automatically.</p>
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

                    <form id="userForm" method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}" placeholder="Enter full name" required>
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
                                               value="{{ old('email') }}" placeholder="user@example.com" required>
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
                                               value="{{ old('phone') }}" placeholder="0700 000 000" required>
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
                                               value="{{ old('staff_number') }}" placeholder="e.g. {{ setting('institution_acronym', 'ORG') }}/001" required>
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
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
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
                                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                                {{ $division->name }}
                                                @if($division->directorate) ({{ $division->directorate->name }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- Directorate (for Director) --}}
                            <div class="col-md-6 directorate-field" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">Directorate <span class="text-danger">*</span></label>
                                    <select name="directorate_id" class="form-select @error('directorate_id') is-invalid @enderror">
                                        <option value="">Select Directorate</option>
                                        @foreach($directorates as $directorate)
                                            <option value="{{ $directorate->id }}" {{ old('directorate_id') == $directorate->id ? 'selected' : '' }}>
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
                                <i class="mdi mdi-account-plus-outline me-1"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card bg-soft-primary border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="mdi mdi-information-outline text-primary me-1"></i> How it works</h6>
                    <ul class="mb-0" style="font-size: 13px; padding-left: 16px;">
                        <li class="mb-2">A <strong>random password</strong> is generated automatically.</li>
                        <li class="mb-2">A <strong>welcome email</strong> with login credentials is sent to the user.</li>
                        <li class="mb-2">The user will be prompted to <strong>change their password</strong> on first login.</li>
                        <li class="mb-2">The account is created as <strong>Active</strong> by default.</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Role Guide</h6>
                    <div style="font-size: 12px;">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="fw-semibold">CEO</span>
                            <span class="text-muted">Final paper approval</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="fw-semibold">Corporation Secretary</span>
                            <span class="text-muted">Meeting management</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="fw-semibold">Director</span>
                            <span class="text-muted">Paper review (by directorate)</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="fw-semibold">Division Head</span>
                            <span class="text-muted">Paper authoring</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="fw-semibold">Board Member</span>
                            <span class="text-muted">Meeting participation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function toggleRoleFields() {
        var role = $('#roleSelect').val();

        // Division field — shown for Division Head
        if (role === 'Division Head') {
            $('.division-field').slideDown(200);
        } else {
            $('.division-field').slideUp(200).find('select').val('');
        }

        // Directorate field — shown for Director
        if (role === 'Director') {
            $('.directorate-field').slideDown(200);
        } else {
            $('.directorate-field').slideUp(200).find('select').val('');
        }
    }

    // Initial check
    toggleRoleFields();

    // On change
    $('#roleSelect').on('change', toggleRoleFields);

    // Form validation
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
