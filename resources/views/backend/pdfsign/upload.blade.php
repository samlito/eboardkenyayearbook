@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('pdfsign.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                        <h4 class="page-title">Upload PDF for Signing</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('pdfsign.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Document Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}" placeholder="e.g. Board Resolution Q1 2026" required>
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">PDF File <span class="text-danger">*</span></label>
                                    <input type="file" name="pdf_file" class="form-control @error('pdf_file') is-invalid @enderror"
                                           accept=".pdf" required>
                                    @error('pdf_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Max 20MB. PDF files only.</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-upload"></i> Upload & Continue to Viewer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
