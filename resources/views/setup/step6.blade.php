@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-shield-account-outline"></i> Admin Account & Summary</h3>
<p class="section-subtitle">Create the initial superadmin account and review your configuration.</p>

<form action="{{ route('setup.process', 6) }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-12"><h6 class="text-muted mb-0">Superadmin Account</h6></div>

        <div class="col-md-12">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="admin_name" class="form-control" placeholder="e.g. System Administrator" value="{{ old('admin_name') }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="admin_email" class="form-control" placeholder="e.g. admin@institution.go.ke" value="{{ old('admin_email') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="admin_password" class="form-control" placeholder="Min 8 characters" required minlength="8">
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" name="admin_password_confirmation" class="form-control" placeholder="Re-enter password" required>
        </div>

        @if(isset($savedData['institution']) || isset($savedData['branding']) || isset($savedData['workflow']))
        <div class="col-12 mt-3">
            <hr class="my-2">
            <h6 class="text-muted mb-3">Configuration Summary</h6>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    @if(isset($savedData['institution']))
                    <tr class="table-light"><th colspan="2">Institution</th></tr>
                    <tr><td class="text-muted" style="width:40%">Name</td><td>{{ $savedData['institution']['institution_name'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Acronym</td><td>{{ $savedData['institution']['institution_acronym'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $savedData['institution']['email_info'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Website</td><td>{{ $savedData['institution']['website_url'] ?? '-' }}</td></tr>
                    @endif

                    @if(isset($savedData['branding']))
                    <tr class="table-light"><th colspan="2">Branding</th></tr>
                    <tr>
                        <td class="text-muted">Colors</td>
                        <td>
                            <span class="badge rounded-pill text-white px-3" style="background: {{ $savedData['branding']['primary_color'] ?? '#232323' }}">Primary</span>
                            <span class="badge rounded-pill text-white px-3" style="background: {{ $savedData['branding']['accent_color'] ?? '#7AC143' }}">Accent</span>
                        </td>
                    </tr>
                    @foreach(['logo_main' => 'Main Logo', 'logo_sidebar' => 'Sidebar Logo', 'logo_letterhead' => 'Letterhead'] as $key => $label)
                        @if(isset($savedData['branding'][$key]))
                        <tr><td class="text-muted">{{ $label }}</td><td><i class="mdi mdi-check-circle text-success"></i> Uploaded</td></tr>
                        @endif
                    @endforeach
                    @endif

                    @if(isset($savedData['workflow']))
                    <tr class="table-light"><th colspan="2">Workflow</th></tr>
                    <tr><td class="text-muted">Reference Prefix</td><td>{{ $savedData['workflow']['reference_number_prefix'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Email Subject Prefix</td><td>{{ $savedData['workflow']['email_subject_prefix'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">CEO Title</td><td>{{ $savedData['workflow']['signatory_title_ceo'] ?? '-' }}</td></tr>
                    @endif

                    @if(isset($savedData['email']))
                    <tr class="table-light"><th colspan="2">Email</th></tr>
                    <tr><td class="text-muted">SMTP Host</td><td>{{ $savedData['email']['mail_host'] ?? '-' }}:{{ $savedData['email']['mail_port'] ?? '-' }}</td></tr>
                    <tr><td class="text-muted">From</td><td>{{ $savedData['email']['mail_from_name'] ?? '-' }} &lt;{{ $savedData['email']['mail_from_address'] ?? '-' }}&gt;</td></tr>
                    @endif
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.show', 5) }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup-accent btn-lg">
            <i class="mdi mdi-check-all"></i> Complete Setup
        </button>
    </div>
</form>
@endsection
