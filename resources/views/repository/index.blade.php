@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1">Document Repository</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Manage document categories and their contents</p>
                </div>
                <a href="{{ route('repository.category.create') }}" class="btn btn-success">
                    <i class="mdi mdi-plus me-1"></i> New Category
                </a>
            </div>

            @if(session('message'))
                <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($categories->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-folder-open-outline" style="font-size: 48px; color: #d1d5db;"></i>
                        <h5 class="mt-3 text-muted">No Categories Yet</h5>
                        <p class="text-muted mb-3" style="font-size: 13px;">Create your first document category to get started.</p>
                        <a href="{{ route('repository.category.create') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-plus me-1"></i> Create Category
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($categories as $category)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 repo-category-card">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="repo-category-icon me-3">
                                            <i class="mdi {{ $category->icon ?? 'mdi-folder-outline' }}"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h5 class="mb-1 text-truncate">{{ $category->name }}</h5>
                                            @if($category->description)
                                                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.4;">
                                                    {{ Str::limit($category->description, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3 mt-auto">
                                        <span class="badge bg-light text-dark" style="font-size: 12px;">
                                            <i class="mdi mdi-file-document-outline me-1"></i>
                                            {{ $category->documents_count }} {{ Str::plural('document', $category->documents_count) }}
                                        </span>
                                        @if(!$category->is_active)
                                            <span class="badge bg-warning text-dark ms-2" style="font-size: 11px;">Inactive</span>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-1">
                                        <a href="{{ route('repository.category.show', $category) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="mdi mdi-eye-outline me-1"></i> View
                                        </a>
                                        <a href="{{ route('repository.category.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </a>
                                        <form action="{{ route('repository.category.destroy', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this category? All documents within it will also be removed.')">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .repo-category-card {
            border: 1px solid #e5e7eb;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .repo-category-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: {{ setting('primary_color', '#1a1a1a') }};
        }
        .repo-category-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}22, {{ setting('primary_color', '#1a1a1a') }}11);
            display: flex;
            align-items: center;
            justify-content: center;
            color: {{ setting('primary_color', '#1a1a1a') }};
            font-size: 22px;
            flex-shrink: 0;
        }
    </style>
@endsection
