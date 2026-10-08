@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('repository.index') }}" class="btn btn-secondary btn-sm me-3">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">New Category</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Create a new document category</p>
                </div>
            </div>

            <div class="card" style="max-width: 640px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('repository.category.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Board Resolutions" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Brief description of what documents belong in this category">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text" id="icon-preview">
                                    <i class="mdi {{ old('icon', 'mdi-folder-outline') }}"></i>
                                </span>
                                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                                       value="{{ old('icon', 'mdi-folder-outline') }}" id="icon-input"
                                       placeholder="mdi-folder-outline">
                            </div>
                            <small class="text-muted">
                                Use an MDI icon class (e.g. <code>mdi-folder-outline</code>, <code>mdi-file-document</code>, <code>mdi-gavel</code>).
                                Browse icons at <a href="https://materialdesignicons.com" target="_blank">materialdesignicons.com</a>
                            </small>
                            @error('icon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check me-1"></i> Save Category
                            </button>
                            <a href="{{ route('repository.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('icon-input').addEventListener('input', function () {
            const preview = document.querySelector('#icon-preview i');
            preview.className = 'mdi ' + this.value.trim();
        });
    </script>
@endsection
