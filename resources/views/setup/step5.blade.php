@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-email-outline"></i> Email Configuration</h3>
<p class="section-subtitle">Configure your SMTP email settings for sending notifications.</p>

<form action="{{ route('setup.process', 5) }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-12">
            <div class="alert alert-warning py-2">
                <i class="mdi mdi-shield-lock-outline"></i>
                Email passwords are encrypted before storage. Use app-specific passwords for services like Gmail.
            </div>
        </div>

        <div class="col-md-8">
            <label class="form-label">SMTP Host <span class="text-danger">*</span></label>
            <input type="text" name="mail_host" class="form-control" placeholder="e.g. smtp.gmail.com" value="{{ old('mail_host', $savedData['email']['mail_host'] ?? 'smtp.gmail.com') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">SMTP Port <span class="text-danger">*</span></label>
            <input type="number" name="mail_port" class="form-control" placeholder="e.g. 587" value="{{ old('mail_port', $savedData['email']['mail_port'] ?? '587') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">SMTP Username <span class="text-danger">*</span></label>
            <input type="text" name="mail_username" class="form-control" placeholder="e.g. noreply@institution.go.ke" value="{{ old('mail_username', $savedData['email']['mail_username'] ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">SMTP Password <span class="text-danger">*</span></label>
            <input type="password" name="mail_password" class="form-control" placeholder="App-specific password" value="{{ old('mail_password', $savedData['email']['mail_password'] ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Encryption <span class="text-danger">*</span></label>
            <select name="mail_encryption" class="form-select" required>
                <option value="tls" {{ old('mail_encryption', $savedData['email']['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ old('mail_encryption', $savedData['email']['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="null" {{ old('mail_encryption', $savedData['email']['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">From Email <span class="text-danger">*</span></label>
            <input type="email" name="mail_from_address" class="form-control" placeholder="e.g. noreply@institution.go.ke" value="{{ old('mail_from_address', $savedData['email']['mail_from_address'] ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">From Name <span class="text-danger">*</span></label>
            <input type="text" name="mail_from_name" class="form-control" placeholder="e.g. ORG eBoard System" value="{{ old('mail_from_name', $savedData['email']['mail_from_name'] ?? '') }}" required>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.show', 4) }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup">Next: Admin Account <i class="mdi mdi-arrow-right"></i></button>
    </div>
</form>
@endsection
