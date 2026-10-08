@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-cog-outline"></i> Workflow Configuration</h3>
<p class="section-subtitle">Configure how meetings, papers, and references are numbered and labeled.</p>

<form action="{{ route('setup.process', 3) }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-12"><h6 class="text-muted mb-0">Reference Numbers</h6></div>

        <div class="col-md-6">
            <label class="form-label">Reference Number Prefix <span class="text-danger">*</span></label>
            <input type="text" name="reference_number_prefix" class="form-control" placeholder="e.g. TWWDA" value="{{ old('reference_number_prefix', $savedData['workflow']['reference_number_prefix'] ?? '') }}" required>
            <small class="text-muted">Used in meeting references: <code>[PREFIX]/AUD/1/07/2026</code></small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Staff Number Prefix</label>
            <input type="text" name="staff_number_prefix" class="form-control" placeholder="e.g. TWWDA" value="{{ old('staff_number_prefix', $savedData['workflow']['staff_number_prefix'] ?? '') }}">
            <small class="text-muted">Used in staff numbers: <code>[PREFIX]/001</code></small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email Subject Prefix <span class="text-danger">*</span></label>
            <input type="text" name="email_subject_prefix" class="form-control" placeholder="e.g. TWWDA eBoard" value="{{ old('email_subject_prefix', $savedData['workflow']['email_subject_prefix'] ?? '') }}" required>
            <small class="text-muted">Prefixes all notification emails: <code>[PREFIX]: Meeting Notice</code></small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Default Meeting Venue</label>
            <input type="text" name="default_venue" class="form-control" placeholder="e.g. Board Room, Headquarters" value="{{ old('default_venue', $savedData['workflow']['default_venue'] ?? '') }}">
        </div>

        <div class="col-12"><hr class="my-2"><h6 class="text-muted mb-0">Signatory Titles</h6></div>
        <p class="text-muted small">These titles appear on official documents, notices, and PDFs.</p>

        <div class="col-md-6">
            <label class="form-label">CEO Title <span class="text-danger">*</span></label>
            <input type="text" name="signatory_title_ceo" class="form-control" placeholder="e.g. CHIEF EXECUTIVE OFFICER" value="{{ old('signatory_title_ceo', $savedData['workflow']['signatory_title_ceo'] ?? 'CHIEF EXECUTIVE OFFICER') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Board Chairperson Title <span class="text-danger">*</span></label>
            <input type="text" name="signatory_title_chairperson" class="form-control" placeholder="e.g. CHAIRPERSON" value="{{ old('signatory_title_chairperson', $savedData['workflow']['signatory_title_chairperson'] ?? 'CHAIRPERSON') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Audit Head Title</label>
            <input type="text" name="signatory_title_audit_head" class="form-control" placeholder="e.g. DIRECTOR, INTERNAL AUDIT" value="{{ old('signatory_title_audit_head', $savedData['workflow']['signatory_title_audit_head'] ?? 'DIRECTOR, INTERNAL AUDIT') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Corporation Secretary Title</label>
            <input type="text" name="signatory_title_cs" class="form-control" placeholder="e.g. CORPORATION SECRETARY" value="{{ old('signatory_title_cs', $savedData['workflow']['signatory_title_cs'] ?? 'CORPORATION SECRETARY') }}">
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.show', 2) }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup">Next: Role Labels <i class="mdi mdi-arrow-right"></i></button>
    </div>
</form>
@endsection
