@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1">Divisions</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Manage divisions and their directorate mappings</p>
                </div>
                <a href="{{ route('divisions.create') }}" class="btn btn-success">
                    <i class="mdi mdi-plus me-1"></i> Add Division
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered twwda-datatable" data-export-title="{{ setting('institution_acronym', 'ORG') }} Divisions Report" style="width:100%;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Division Name</th>
                            <th>Directorate</th>
                            <th>Reports To</th>
                            <th>CEO Approval</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($divisions as $division)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $division->name }}</span>
                                </td>
                                <td>
                                    @if($division->directorate)
                                        <span class="badge bg-info">{{ $division->directorate->name }}</span>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($division->directorate)
                                        Director, {{ $division->directorate->name }}
                                    @else
                                        <span class="badge bg-warning">CEO (Direct)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($division->requires_ceo_approval)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('divisions.edit', $division->id) }}" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-pencil-outline"></i> Edit
                                    </a>
                                    <form action="{{ route('divisions.destroy', $division->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this division?')">
                                            <i class="mdi mdi-trash-can-outline"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
