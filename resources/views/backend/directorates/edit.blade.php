@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('directorates.index') }}" class="btn btn-secondary btn-sm me-3">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Edit Directorate</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Update directorate details</p>
                </div>
            </div>

            <div class="card" style="max-width: 640px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('directorates.update', $directorate->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Directorate Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $directorate->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i> Update Directorate
                            </button>
                            <a href="{{ route('directorates.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
