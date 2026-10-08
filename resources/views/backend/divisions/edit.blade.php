@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('divisions.index') }}" class="btn btn-secondary btn-sm me-3">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Edit Division</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Update division details and directorate assignment</p>
                </div>
            </div>

            <div class="card" style="max-width: 640px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('divisions.update', $division->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Division Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $division->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Directorate</label>
                            <select name="directorate_id" class="form-select @error('directorate_id') is-invalid @enderror">
                                <option value="">-- Reports Directly to CEO --</option>
                                @foreach($directorates as $directorate)
                                    <option value="{{ $directorate->id }}"
                                        {{ old('directorate_id', $division->directorate_id) == $directorate->id ? 'selected' : '' }}>
                                        {{ $directorate->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Leave blank if this division reports directly to the CEO.</div>
                            @error('directorate_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="requires_ceo_approval" value="0">
                                <input type="checkbox" name="requires_ceo_approval" value="1"
                                       class="form-check-input" id="ceoApproval"
                                       {{ old('requires_ceo_approval', $division->requires_ceo_approval) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ceoApproval">Requires CEO Approval</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i> Update Division
                            </button>
                            <a href="{{ route('divisions.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
