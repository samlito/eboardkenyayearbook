@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('resolutions.create') }}" class="btn btn-info btn-sm">
                                <i class="mdi mdi-plus"></i> Record New Resolution
                            </a>
                        </div>
                        <h4 class="page-title">Board Resolutions Tracker</h4>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $resolutions->count() }}</h3>
                            <p class="mb-0 text-muted">Total Resolutions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $resolutions->where('status', 'pending')->count() }}</h3>
                            <p class="mb-0 text-muted">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $resolutions->where('status', 'in_progress')->count() }}</h3>
                            <p class="mb-0 text-muted">In Progress</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $resolutions->where('status', 'completed')->count() }}</h3>
                            <p class="mb-0 text-muted">Completed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolutions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Meeting</th>
                                    <th style="width:300px;">Resolution</th>
                                    <th>Assigned Officer</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($resolutions as $key => $res)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $res->boardMeeting->meeting_name ?? $res->boardMeeting->name ?? 'N/A' }}</td>
                                        <td style="white-space:normal; word-wrap:break-word; max-width:300px;">
                                            {{ Str::limit($res->resolution, 100) }}
                                        </td>
                                        <td>{{ $res->assignedOfficer->name }}</td>
                                        <td>{{ $res->due_date ? $res->due_date->format('d M Y') : '—' }}</td>
                                        <td>
                                            @php
                                                $badge = match($res->status) {
                                                    'pending'     => 'warning',
                                                    'in_progress' => 'info',
                                                    'completed'   => 'success',
                                                    'overdue'     => 'danger',
                                                    default       => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $res->status)) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('resolutions.show', $res->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('resolutions.edit', $res->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>

                                            <!-- Quick Status Update -->
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                    Update Status
                                                </button>
                                                <div class="dropdown-menu">
                                                    @foreach(['pending','in_progress','completed','overdue'] as $st)
                                                        <form action="{{ route('resolutions.updateStatus', $res->id) }}" method="POST" class="d-inline">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="status" value="{{ $st }}">
                                                            <button type="submit" class="dropdown-item">{{ ucfirst(str_replace('_',' ',$st)) }}</button>
                                                        </form>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <form action="{{ route('resolutions.destroy', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this resolution?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
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

        </div>
    </div>

@endsection
