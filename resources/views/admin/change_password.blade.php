@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="page-title mb-1">Change Password</h4>
                        <p class="text-muted mb-0">Update your account password</p>
                    </div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </div>
            </div>
        </div>

        @if(auth()->user()->password_reset)
        <div class="row mb-3">
            <div class="col-lg-8">
                <div class="alert border-0 d-flex align-items-start" style="background: #fef3c7; border-left: 4px solid #f59e0b !important; border-radius: 12px;">
                    <i class="mdi mdi-shield-alert-outline me-2 mt-1" style="font-size: 22px; color: #d97706;"></i>
                    <div>
                        <strong>First-time login — password change required</strong>
                        <p class="mb-0 small text-muted mt-1">Your account was created with a temporary password. Please set a new secure password to continue using the system.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="row mb-3">
            <div class="col-lg-8">
                <div class="alert alert-warning alert-dismissible fade show border-0" style="border-radius: 12px;">
                    <i class="mdi mdi-alert-outline me-1"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: {{ setting('primary_color', '#1a1a1a') }}15; flex-shrink: 0;">
                                <i class="mdi mdi-lock-reset" style="font-size: 22px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Set New Password</h5>
                                <p class="text-muted mb-0 small">Choose a strong password you haven't used before</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('update.password') }}" id="passwordForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                    <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" id="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                                @error('old_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @if(auth()->user()->password_reset)
                                    <div class="form-text">This is the temporary password sent to you via email.</div>
                                @endif
                            </div>

                            <hr class="my-3" style="border-style: dashed; border-color: #e5e7eb;">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-lock-plus-outline"></i></span>
                                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                                @error('new_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div id="password-strength" class="mt-2" style="display: none;">
                                    <div class="d-flex gap-1 mb-1">
                                        <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: #e5e7eb;"></div>
                                        <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: #e5e7eb;"></div>
                                        <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: #e5e7eb;"></div>
                                        <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: #e5e7eb;"></div>
                                    </div>
                                    <small class="strength-text text-muted"></small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-lock-check-outline"></i></span>
                                    <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                                <small id="match-msg" class="d-none"></small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: .6px;">Password Requirements</h6>
                        <ul class="list-unstyled mb-0" style="font-size: 13px;">
                            <li class="d-flex align-items-start mb-2">
                                <i class="mdi mdi-check-circle-outline text-success me-2 mt-1"></i>
                                <span>At least <strong>8 characters</strong> long</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <i class="mdi mdi-check-circle-outline text-success me-2 mt-1"></i>
                                <span>Mix of <strong>uppercase & lowercase</strong> letters</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <i class="mdi mdi-check-circle-outline text-success me-2 mt-1"></i>
                                <span>Include at least <strong>one number</strong></span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="mdi mdi-check-circle-outline text-success me-2 mt-1"></i>
                                <span>Must be <strong>different</strong> from current password</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'mdi mdi-eye-off-outline';
    } else {
        input.type = 'password';
        icon.className = 'mdi mdi-eye-outline';
    }
}

document.getElementById('new_password').addEventListener('input', function() {
    const val = this.value;
    const container = document.getElementById('password-strength');
    const bars = container.querySelectorAll('.strength-bar');
    const text = container.querySelector('.strength-text');

    if (!val) { container.style.display = 'none'; return; }
    container.style.display = 'block';

    let score = 0;
    if (val.length >= 8) score++;
    if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
    if (/\d/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    const colors = ['#dc2626', '#f59e0b', '#10b981', '#059669'];
    const labels = ['Weak', 'Fair', 'Good', 'Strong'];
    bars.forEach((bar, i) => bar.style.background = i < score ? colors[Math.min(score - 1, 3)] : '#e5e7eb');
    text.textContent = labels[Math.min(score - 1, 3)] || '';
    text.style.color = colors[Math.min(score - 1, 3)] || '#9ca3af';
});

document.getElementById('new_password_confirmation').addEventListener('input', function() {
    const msg = document.getElementById('match-msg');
    const newPw = document.getElementById('new_password').value;
    if (!this.value) { msg.classList.add('d-none'); return; }
    msg.classList.remove('d-none');
    if (this.value === newPw) {
        msg.textContent = 'Passwords match';
        msg.className = 'text-success small';
    } else {
        msg.textContent = 'Passwords do not match';
        msg.className = 'text-danger small';
    }
});
</script>
@endpush

@endsection
