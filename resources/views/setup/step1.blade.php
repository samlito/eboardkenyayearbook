@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-domain"></i> Institution Identity</h3>
<p class="section-subtitle">Enter your institution's basic information. This will appear throughout the system.</p>

<form action="{{ route('setup.process', 1) }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Institution Full Name <span class="text-danger">*</span></label>
            <input type="text" name="institution_name" class="form-control" placeholder="e.g. Tana Water Works Development Agency" value="{{ old('institution_name', $savedData['institution']['institution_name'] ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Acronym <span class="text-danger">*</span></label>
            <input type="text" name="institution_acronym" class="form-control" placeholder="e.g. TWWDA" value="{{ old('institution_acronym', $savedData['institution']['institution_acronym'] ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Tagline / Motto</label>
            <input type="text" name="institution_tagline" class="form-control" placeholder="e.g. Water for all, always" value="{{ old('institution_tagline', $savedData['institution']['institution_tagline'] ?? '') }}">
        </div>

        <div class="col-12"><hr class="my-2"><h6 class="text-muted mb-0">Contact Information</h6></div>

        <div class="col-md-6">
            <label class="form-label">Physical Address</label>
            <textarea name="physical_address" class="form-control" rows="2" placeholder="e.g. Maji House, Baden Powell Road">{{ old('physical_address', $savedData['institution']['physical_address'] ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Postal Address</label>
            <input type="text" name="postal_address" class="form-control" placeholder="e.g. P.O. Box 1292 - 10100, Nyeri" value="{{ old('postal_address', $savedData['institution']['postal_address'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Primary Phone</label>
            <input type="text" name="phone_primary" class="form-control" placeholder="e.g. +254 724 259 891" value="{{ old('phone_primary', $savedData['institution']['phone_primary'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Secondary Phone</label>
            <input type="text" name="phone_secondary" class="form-control" placeholder="e.g. +254 61 2032282" value="{{ old('phone_secondary', $savedData['institution']['phone_secondary'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Info Email</label>
            <input type="email" name="email_info" class="form-control" placeholder="e.g. info@institution.go.ke" value="{{ old('email_info', $savedData['institution']['email_info'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Website URL</label>
            <input type="url" name="website_url" class="form-control" placeholder="e.g. https://www.institution.go.ke" value="{{ old('website_url', $savedData['institution']['website_url'] ?? '') }}">
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.index') }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup">Next: Branding <i class="mdi mdi-arrow-right"></i></button>
    </div>
</form>
@endsection
