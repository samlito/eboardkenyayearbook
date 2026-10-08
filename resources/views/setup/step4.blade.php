@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-account-cog-outline"></i> Role Display Labels</h3>
<p class="section-subtitle">Customize how role names appear in the system. The internal role identifiers remain unchanged — these are display labels only.</p>

<form action="{{ route('setup.process', 4) }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-12">
            <div class="alert alert-info py-2">
                <i class="mdi mdi-information-outline"></i>
                Different institutions may use different titles. For example, "CEO" might be "Managing Director" or "Executive Director" at your institution.
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">CEO / Head of Institution <span class="text-danger">*</span></label>
            <input type="text" name="role_label_ceo" class="form-control" placeholder="e.g. Chief Executive Officer" value="{{ old('role_label_ceo', $savedData['roles']['role_label_ceo'] ?? 'Chief Executive Officer') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Corporation Secretary <span class="text-danger">*</span></label>
            <input type="text" name="role_label_corporation_secretary" class="form-control" placeholder="e.g. Corporation Secretary" value="{{ old('role_label_corporation_secretary', $savedData['roles']['role_label_corporation_secretary'] ?? 'Corporation Secretary') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Board Member <span class="text-danger">*</span></label>
            <input type="text" name="role_label_board_member" class="form-control" placeholder="e.g. Board Member" value="{{ old('role_label_board_member', $savedData['roles']['role_label_board_member'] ?? 'Board Member') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Internal Audit Head</label>
            <input type="text" name="role_label_dd_internal_audit" class="form-control" placeholder="e.g. DD Internal Audit" value="{{ old('role_label_dd_internal_audit', $savedData['roles']['role_label_dd_internal_audit'] ?? 'DD Internal Audit') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Director <span class="text-danger">*</span></label>
            <input type="text" name="role_label_director" class="form-control" placeholder="e.g. Director" value="{{ old('role_label_director', $savedData['roles']['role_label_director'] ?? 'Director') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Division Head <span class="text-danger">*</span></label>
            <input type="text" name="role_label_division_head" class="form-control" placeholder="e.g. Division Head" value="{{ old('role_label_division_head', $savedData['roles']['role_label_division_head'] ?? 'Division Head') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Board Chairperson</label>
            <input type="text" name="role_label_chairperson" class="form-control" placeholder="e.g. Board Chairperson" value="{{ old('role_label_chairperson', $savedData['roles']['role_label_chairperson'] ?? 'Board Chairperson') }}">
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.show', 3) }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup">Next: Email <i class="mdi mdi-arrow-right"></i></button>
    </div>
</form>
@endsection
