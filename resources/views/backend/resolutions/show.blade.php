@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('resolutions.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back to Resolutions
                            </a>
                            <a href="{{ route('resolutions.edit', $resolution->id) }}" class="btn btn-info btn-sm">
                                <i class="mdi mdi-pencil"></i> Edit
                            </a>
                        </div>
                        <h4 class="page-title">Resolution Details</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $badge = match($resolution->status) {
                                    'pending'     => 'warning',
                                    'in_progress' => 'info',
                                    'completed'   => 'success',
                                    'overdue'     => 'danger',
                                    default       => 'secondary',
                                };
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Board Meeting: {{ $resolution->boardMeeting->meeting_name ?? $resolution->boardMeeting->name ?? 'N/A' }}</h5>
                                <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst(str_replace('_', ' ', $resolution->status)) }}</span>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:200px;">Resolution</th>
                                    <td>{{ $resolution->resolution }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned Officer</th>
                                    <td>{{ $resolution->assignedOfficer->name }} ({{ $resolution->assignedOfficer->email }})</td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <td>{{ $resolution->due_date ? $resolution->due_date->format('d M Y') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td>{{ $resolution->remarks ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Recorded By</th>
                                    <td>{{ $resolution->creator->name }}</td>
                                </tr>
                                <tr>
                                    <th>Recorded On</th>
                                    <td>{{ $resolution->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>

                            <!-- Quick Status Update -->
                            <div class="mt-3">
                                <h6>Update Status:</h6>
                                <div class="d-flex gap-2">
                                    @foreach(['pending','in_progress','completed','overdue'] as $st)
                                        <form action="{{ route('resolutions.updateStatus', $resolution->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $st }}">
                                            <button type="submit"
                                                    class="btn btn-sm {{ $resolution->status === $st ? 'btn-dark' : 'btn-outline-secondary' }}">
                                                {{ ucfirst(str_replace('_',' ',$st)) }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
