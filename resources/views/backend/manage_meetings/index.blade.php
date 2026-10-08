@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h3 class="mb-4">Manage Board Meetings</h3>

        @if($meetings->isEmpty())
            <div class="alert alert-info">No meetings available for publishing.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Committee</th>
                        <th>Status</th>
                        <th>Published Notice</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($meetings as $meeting)
                        <tr>
                            <td>{{ $meeting->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</td>
                            <td>{{ $meeting->committee->name ?? 'N/A' }}</td>
                            <td>
                                @if($meeting->published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                            <td>
                                {{ $meeting->notice_title ?? '-' }}
                            </td>
                            <td>
{{--                                <a href="{{ route('admin.meetings.publish', $meeting->id) }}" class="btn btn-primary btn-sm">Publish</a>--}}
{{--                                <a href="{{ route('admin.meetings.confirmation.preview', $meeting->id) }}" class="btn btn-secondary btn-sm">Track Confirmations</a>--}}
{{--                           --}}

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
