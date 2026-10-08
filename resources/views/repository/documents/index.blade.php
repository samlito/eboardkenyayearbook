@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('repository.index') }}" class="btn btn-secondary btn-sm me-3">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-1">
                            <i class="mdi {{ $category->icon ?? 'mdi-folder-outline' }} me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                            {{ $category->name }}
                        </h4>
                        <p class="text-muted mb-0" style="font-size: 13px;">
                            {{ $category->description ?? 'Documents in this category' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('repository.document.create', $category) }}" class="btn btn-success">
                    <i class="mdi mdi-plus me-1"></i> Add Document
                </a>
            </div>

            @if(session('message'))
                <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($documents->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #d1d5db;"></i>
                            <h5 class="mt-3 text-muted">No Documents Yet</h5>
                            <p class="text-muted mb-3" style="font-size: 13px;">Upload the first document to this category.</p>
                            <a href="{{ route('repository.document.create', $category) }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-plus me-1"></i> Add Document
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>File Type</th>
                                    <th>Size</th>
                                    <th>Visibility</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th style="width: 140px;">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $document->title }}</span>
                                            @if(!$document->is_active)
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 12.5px;">
                                                {{ Str::limit($document->description, 60) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ strtoupper($document->file_type) }}</span>
                                        </td>
                                        <td style="white-space: nowrap;">{{ $document->formattedFileSize() }}</td>
                                        <td>
                                            @if($document->visibility === 'all')
                                                <span class="badge bg-success">All Members</span>
                                            @elseif($document->visibility === 'committee')
                                                <span class="badge bg-warning text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                                      title="{{ $document->accessRules->pluck('committee.name')->filter()->implode(', ') ?: 'No committees assigned' }}">
                                                    Committee Only
                                                </span>
                                            @elseif($document->visibility === 'specific')
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                                      title="{{ $document->accessRules->pluck('member.names')->filter()->implode(', ') ?: 'No members assigned' }}">
                                                    Specific Members
                                                </span>
                                            @endif
                                        </td>
                                        <td style="white-space: nowrap; font-size: 12.5px;">
                                            {{ $document->uploader->name ?? 'Unknown' }}
                                        </td>
                                        <td style="white-space: nowrap; font-size: 12.5px;">
                                            {{ $document->created_at->format('d M Y') }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('repository.document.download', $document) }}" class="btn btn-sm btn-outline-success" title="Download">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                                <a href="{{ route('repository.document.edit', [$category, $document]) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </a>
                                                <form action="{{ route('repository.document.destroy', [$category, $document]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                            onclick="return confirm('Delete this document? This action cannot be undone.')">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
