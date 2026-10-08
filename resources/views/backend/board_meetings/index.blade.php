@extends('admin_dashboard')
@section('admin')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h4 class="page-title">All Scheduled Meetings</h4>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            ← Back
                        </a>
{{--                        <a href="{{ route('board.meetings.add') }}" class="btn btn-success">Add Meeting</a>--}}
                    </div>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">


                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Date</th>
{{--                                    <th>Venue</th>--}}
                                    <th>Board Committee</th>
                                    <th>Notice</th>
                                    <th>HOD Submissions</th>
                                    <th>CEO Review</th>
                                    <th>Board Notification</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($meetings as $key => $meeting)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $meeting->title }}</td>
                                        <td data-order="{{ $meeting->date?->format('Y-m-d') }}">
                                            {{ $meeting->date?->format('d M Y') }}
                                        </td>


                                        {{--                                        <td>{{ $meeting->venue }}</td>--}}
                                        <td>{{ $meeting->boardCommittee?->name ?? '—' }}</td>


                                        {{-- Notice Status --}}
                                        <td>
                                            @if($meeting->notice)
                                                <span class="badge bg-warning text-dark">{{ $meeting->notice->status }}</span>
                                                <a href="{{ route('board.meetings.view.notice', $meeting->id) }}" class="btn btn-sm btn-outline-info mt-1">View Notice</a>
                                            @else
                                                <span class="badge bg-secondary">Not Initiated</span>
                                            @endif
                                        </td>

                                        {{-- HOD Submissions --}}
                                        <td>
                                            @php
                                                $assigned = $meeting->assignments;
                                                $submittedIds = $submittedUserIdsPerMeeting[$meeting->id] ?? [];
                                                $pendingHeads = $assigned->filter(fn($a) => !in_array($a->user_id, $submittedIds));
                                                    $needsRevisionIds = $needsRevisionUserIdsPerMeeting[$meeting->id] ?? [];
                                            @endphp
@if($meeting->notice && $meeting->notice->status === 'Approved')
                                            @if($assigned->count() > 0)
                                                <button class="btn btn-outline-primary btn-sm mb-1" data-bs-toggle="collapse" data-bs-target="#notifiedHODs-{{ $meeting->id }}">
                                                    View HODs ({{ $assigned->count() }})
                                                </button>
                                                    <a href="{{ route('board.meetings.notify.heads', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-success mt-2">
                                                        ➕ Add More HODs
                                                    </a>

                                                <div class="collapse mt-2" id="notifiedHODs-{{ $meeting->id }}">
                                                    <ul class="list-group">
{{--                                                        @foreach($assigned as $assignment)--}}
{{--                                                            <li class="list-group-item d-flex justify-content-between align-items-center">--}}
{{--                                                                {{ $assignment->user->name }}--}}
{{--                                                                @if(in_array($assignment->user_id, $submittedIds))--}}
{{--                                                                    <span class="badge bg-success">Submitted</span>--}}
{{--                                                                @else--}}
{{--                                                                    <span class="badge bg-danger">Pending</span>--}}
{{--                                                                @endif--}}
{{--                                                            </li>--}}
{{--                                                        @endforeach--}}
                                                        @foreach($assigned as $assignment)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                {{ $assignment->user->name }} ({{ $assignment->user->email }})

                                                                @if(in_array($assignment->user_id, $needsRevisionIds))
                                                                    <span class="badge bg-warning text-dark">Needs Revision</span>
                                                                @elseif(in_array($assignment->user_id, $submittedIds))
                                                                    <span class="badge bg-success">Submitted</span>
                                                                @else
                                                                    <span class="badge bg-danger">Pending</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                @if($pendingHeads->isEmpty())
                                                    <span class="badge bg-success">All Submitted</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ $pendingHeads->count() }} Pending</span>
                                                @endif
                                            @else
                                                <a href="{{ route('board.meetings.notify.heads', $meeting->id) }}" class="btn btn-warning btn-sm">Notify HODs</a>
                                            @endif
                                            @endif
                                        </td>

                                        {{-- CEO Review --}}
                                        <td>
                                            @if($meeting->notice && $meeting->notice->status === 'Approved')
                                            @if($meeting->ceo_notified_at)
                                                @if($meeting->allReviewedByCeo)
                                                    <span class="badge bg-success">Reviewed All</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending CEO Review</span>
                                                @endif
                                            @elseif($assigned->count() > 0 && $pendingHeads->isEmpty())
{{--                                                <a href="{{ route('board.meetings.notify.ceo', $meeting->id) }}" class="btn btn-success btn-sm">--}}
{{--                                                    Notify CEO--}}
{{--                                                </a>--}}

                                            @else
                                                <span class="text-muted">Awaiting HODs</span>
                                            @endif
                                            @endif
                                        </td>

                                        {{-- Board Notification --}}
                                        <td>
                                            @if($meeting->notice && $meeting->notice->status === 'Approved')
{{--                                                @if($meeting->allReviewedByCeo && !$meeting->board_notified_at)--}}
                                                    @if( !$meeting->board_notified_at)

                                                    <form action="{{ route('board.meetings.notify.board', $meeting->id) }}" method="POST">
                                                        @csrf
                                                        <button class="btn btn-primary btn-sm">Notify Board</button>
                                                    </form>
                                                    <a href="{{route('board.meetings.notify.board10',$meeting->id)}}" class="btn btn-info btn-sm mt-1">Send 10 Day Notice</a>

                                                    <a href="{{ route('10day.notice.preview', $meeting->id) }}" class="btn btn-sm btn-info mb-1" target="_blank">
                                                        <i class="bi bi-eye-fill"></i> Preview 10 Day Notice
                                                    </a>

                                                @elseif($meeting->board_notified_at)
                                                    <span class="badge bg-success">Board Notified<br>{{ \Carbon\Carbon::parse($meeting->board_notified_at)->format('d M Y h:i A') }}</span>
                                                @else
                                                    <span class="text-muted">Pending CEO Review</span>
{{--                                                    <form action="{{ route('board.meetings.notify.board', $meeting->id) }}" method="POST">--}}
{{--                                                        @csrf--}}
{{--                                                        <button class="btn btn-primary btn-sm">Notify Board</button>--}}
{{--                                                    </form>--}}
{{--                                                    <a href="{{route('board.meetings.notify.board10',$meeting->id)}}" class="btn btn-info btn-sm mt-1">Send 10 Day Notice</a>--}}

                                                @endif
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            @if($meeting->notice && $meeting->notice->status === 'Approved')
                                            <a href="{{ route('board.meetings.view.notice', $meeting->id) }}" class="btn btn-info btn-sm mb-1">Details</a>
                                            @elseif($meeting->notice && $meeting->notice->status == 'Pending')
                                                <span class="badge bg-secondary">Awaiting Notice Approval</span>
                                            @else
                                                <a href="{{ route('board.meetings.initiate', $meeting->id) }}" class="btn btn-primary btn-sm">Create Notice</a>

                                            @endif
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
    <script>
        $(document).ready(function() {
            $('#scroll-horizontal-datatable').DataTable({
                order: [[2, 'asc']],   // 👈 2 = third column = Date
                pageLength: 25,        // optional: default rows per page
                responsive: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#scroll-horizontal-datatable').DataTable({
                order: [[2, 'asc']],   // 👈 default sort on Date column
                pageLength: 25
            });
        });
    </script>


@endsection

