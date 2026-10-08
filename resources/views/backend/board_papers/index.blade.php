@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h2 class="mb-4">📅 My Upcoming Meetings</h2>

        @if($meetings->isEmpty())
            <div class="alert alert-info">No Upcoming Meetings Yet</div>
        @else
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Meeting Title</th>
                                <th>Committee</th>
                                <th>Date</th>
                                {{--                                <th>Venue</th>--}}
                                {{--                                <th>Type</th>--}}
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($meetings as $index => $meeting)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $meeting->title }}</td>
                                    <td>{{ $meeting->committee->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</td>
                                    {{--                                    <td>{{ $meeting->venue }}</td>--}}
                                    {{--                                    <td><span class="badge bg-info text-dark">{{ $meeting->type }}</span></td>--}}
                                    <td>
                                        <a href="{{ route('cs.my.meetings.show', $meeting->id) }}" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
