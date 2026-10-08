@extends('admin_dashboard')
@section('admin')
    <style>
        .pagination {
            justify-content: center; /* centers arrows at bottom */
        }
        .page-item .page-link {
            border-radius: 6px;
            padding: 6px 12px;
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title mb-3">Audit Logs</h4>
{{--            <a href="{{ route('audit.logs.export.pdf') }}" class="btn btn-outline-danger mb-3">--}}
{{--                <i class="bi bi-file-earmark-pdf"></i> Download PDF--}}
{{--            </a>--}}
{{--            <div class="d-flex justify-content-between align-items-center mb-3">--}}
{{--                <h4 class="page-title">Audit Logs</h4>--}}
{{--                <a href="{{ route('audit.logs.export.pdf', request()->query()) }}" class="btn btn-outline-danger">--}}
{{--                    <i class="bi bi-file-earmark-pdf"></i> Download PDF--}}
{{--                </a>--}}
{{--            </div>--}}

            <!-- Filters -->
            <form method="GET" action="{{ route('audit.logs.index') }}" class="card p-3 mb-4 shadow-sm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">All</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action')==$action ? 'selected' : '' }}>
                                    {{ $action }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Table</label>
                        <select name="table_name" class="form-select">
                            <option value="">All</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}" {{ request('table_name')==$table ? 'selected' : '' }}>
                                    {{ $table }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"/>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"/>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('audit.logs.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="page-title">Audit Logs</h4>
                        <a href="{{ route('audit.logs.export.pdf', request()->query()) }}" class="btn btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Download PDF
                        </a>
                    </div>

                </div>
            </form>

            <!-- Audit Logs Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Old Values</th>
                            <th>New Values</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $index => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge bg-info">{{ $log->action }}</span></td>
                                <td>{{ $log->table_name }}</td>
                                <td>{{ $log->record_id ?? '-' }}</td>
                                <td><pre class="small text-danger">{{ $log->old_values }}</pre></td>
                                <td><pre class="small text-success">{{ $log->new_values }}</pre></td>
                                <td>{{ $log->request_ip }}</td>
                                <td><small>{{ Str::limit($log->user_agent, 30) }}</small></td>
                                <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No logs found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-3">
{{--                        {{ $logs->withQueryString()->links() }}--}}
                        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
