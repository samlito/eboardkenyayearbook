@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">My Board Meeting Requests</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Meeting</th>
                            <th>Date</th>
                            <th>Days Left</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Submissions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($assignments as $assign)
                            <tr>
                                <td>{{ $assign->meeting->title }}</td>
                                <td>{{ $assign->meeting->date }}</td>
                                <td>
                                    {{ \Carbon\Carbon::now()->diffInDays($assign->meeting->date, false) }} days
                                </td>
{{--                                <td>--}}
{{--                                    @if(in_array($assign->meeting->id, $submitted))--}}
{{--                                        <span class="badge bg-success">Submitted</span>--}}
{{--                                    @else--}}
{{--                                        <span class="badge bg-warning text-dark">Pending</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td>
                                    @if(($submittedCounts[$assign->meeting->id] ?? 0) > 0)
                                        <span class="badge bg-success">Submitted</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>

                                {{--                                <td>--}}
{{--                                    @if(!in_array($assign->meeting->id, $submitted))--}}
{{--                                        <a href="{{ route('division.head.submit', $assign->meeting->id) }}" class="btn btn-sm btn-primary">Upload Board Papers</a>--}}
{{--                                    @else--}}
{{--                                        <span class="text-muted">Submitted</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td>
                                    @php
                                        $meeting = $assign->meeting;
                                        $count = $submittedCounts[$meeting->id] ?? 0;
                                    @endphp

                                    <a href="{{ route('division.head.submit', $meeting->id) }}" class="btn btn-sm btn-primary">
                                        {{ $count > 0 ? 'Add More Board Papers' : 'Upload Board Papers' }}
                                    </a>
                                    @if($count > 0)
                                        <small class="text-muted d-block mt-1">{{ $count }} uploaded</small>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('division.head.submissions', $assign->meeting->id) }}"
                                       class="btn btn-sm btn-info">
                                        View Submissions
                                    </a>
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
