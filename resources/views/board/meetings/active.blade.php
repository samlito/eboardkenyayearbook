

{{--@extends('admin_dashboard')--}}

{{--@section('admin')--}}
{{--    <div class="container mt-4">--}}
{{--        <h2 class="mb-4">📅 My Upcoming Meetings</h2>--}}

{{--        @if($meetings->isEmpty())--}}
{{--            <div class="alert alert-info">No Upcoming Meetings Yet</div>--}}
{{--        @else--}}
{{--            <div class="card shadow">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="table-responsive">--}}
{{--                        <table class="table table-hover align-middle">--}}
{{--                            <thead class="table-light">--}}
{{--                            <tr>--}}
{{--                                <th>#</th>--}}
{{--                                <th>Meeting Title</th>--}}
{{--                                <th>Committee</th>--}}
{{--                                <th>Date</th>--}}
{{--                                <th>Venue</th>--}}
{{--                                <th>Type</th>--}}
{{--                                <th>Action</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            @foreach($meetings as $index => $meeting)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $index + 1 }}</td>--}}
{{--                                    <td>{{ $meeting->title }}</td>--}}
{{--                                    <td>{{ $meeting->committee->name ?? 'N/A' }}</td>--}}
{{--                                    <td>{{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</td>--}}
{{--                                    <td>{{ $meeting->venue }}</td>--}}
{{--                                    <td><span class="badge bg-info text-dark">{{ $meeting->type }}</span></td>--}}
{{--                                    --}}{{--                                    <td>--}}
{{--                                    --}}{{--                                        <a href="{{ route('board.my.meetings.show', $meeting->id) }}" class="btn btn-sm btn-primary">--}}
{{--                                    --}}{{--                                            View Details--}}
{{--                                    --}}{{--                                        </a>--}}
{{--                                    --}}{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <div class="d-flex flex-column gap-1">--}}
{{--                                            --}}{{-- View Notice --}}
{{--                                            <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}" target="_blank" class="btn btn-sm btn-info">--}}
{{--                                                📄 View Notice--}}
{{--                                            </a>--}}

{{--                                            --}}{{-- Conflict of Interest Register --}}
{{--                                            <a href="{{ route('board.conflict.register', $meeting->id) }}" class="btn btn-sm btn-warning">--}}
{{--                                                ✍️ Conflict of Interest--}}
{{--                                            </a>--}}

{{--                                            --}}{{-- Start Meeting --}}
{{--                                            <a href="{{ route('board.meetings.start', $meeting->id) }}" class="btn btn-sm btn-success">--}}
{{--                                                🟢 Start Meeting--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}

{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--@endsection--}}


@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-calendar-check text-primary"></i> My Upcoming Meetings</h2>

        @if($meetings->isEmpty())
            <div class="alert alert-info">No Upcoming Meetings Yet</div>
        @else
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Committee</th>
                                <th>Date</th>

                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($meetings as $index => $meeting)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $meeting->title }}</td>
                                    <td>{{ $meeting->committee->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</td>

                                    <td>
                                        <div class="btn-group-vertical w-100 gap-1" role="group">
                                            @php
                                                $declaration = \App\Models\ConflictOfInterestDeclaration::where('meeting_id', $meeting->id)
                                                    ->where('board_member_id', auth()->user()->boardMember->id ?? null)
                                                    ->first();
                                            @endphp

                                            @if(!$meeting->is_started)
                                                <span class="badge bg-secondary w-100">Meeting not yet started</span>
                                            @else
                                                {{-- Step 1: No declaration --}}
                                                @if(!$declaration)
                                                    <a href="{{ route('board.conflict.register', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-warning">
                                                        ✍️ Declare Conflict
                                                    </a>

                                                    {{-- Step 2: Declaration exists but not verified --}}
                                                @elseif(!$declaration->otp_verified_at)
                                                    <span class="badge bg-warning text-dark w-100">
                    Pending OTP Confirmation
                </span>
                                                    <a href="{{ route('conflict.verify.form', $declaration->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        🔑 Verify Now
                                                    </a>
                                                    <form action="{{ route('conflict.resend', $declaration->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                            📩 Resend OTP
                                                        </button>
                                                    </form>

                                                    {{-- Step 3: Verified declaration, unlock other actions --}}
                                                @else
                                                    <!-- View Notice -->
                                                    <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye-fill"></i> View Notice
                                                    </a>

                                                    <!-- View My Declaration -->
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#conflictModal{{ $meeting->id }}">
                                                        ✅ View My Declaration
                                                    </button>

                                                    <!-- View Meeting -->
                                                    <a href="{{ route('board.meetings.start', $meeting->id) }}"
                                                       class="btn btn-sm btn-outline-success">
                                                        🟢 View Meeting
                                                    </a>

                                                    <!-- Modal for Viewing Declaration -->
                                                    <div class="modal fade" id="conflictModal{{ $meeting->id }}" tabindex="-1"
                                                         aria-labelledby="conflictModalLabel{{ $meeting->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-light">
                                                                    <h5 class="modal-title" id="conflictModalLabel{{ $meeting->id }}">
                                                                        📄 Your Conflict Declaration
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Status:</strong> {{ $declaration->conflict_status }}</p>
                                                                    @if($declaration->person_name)
                                                                        <p><strong>Name/Issue:</strong> {{ $declaration->person_name }}</p>
                                                                        <p><strong>Address:</strong> {{ $declaration->person_address }}</p>
                                                                        <p><strong>Nature:</strong> {{ $declaration->interest_nature }}</p>
                                                                        <p><strong>Value:</strong> {{ number_format($declaration->interest_value) }}</p>
                                                                        <p><strong>Ceased On:</strong> {{ $declaration->interest_ceased_on }}</p>
                                                                        <p><strong>Remarks:</strong> {{ $declaration->remarks }}</p>
                                                                    @endif
                                                                    <p class="text-muted">
                                                                        <small>Submitted: {{ $declaration->created_at->format('d M Y h:i A') }}</small>
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    {{--                                    <td>--}}
{{--                                        <div class="btn-group-vertical w-100 gap-1" role="group">--}}
{{--                                            @php--}}
{{--                                                $declaration = \App\Models\ConflictOfInterestDeclaration::where('meeting_id', $meeting->id)--}}
{{--                                                    ->where('board_member_id', auth()->user()->boardMember->id ?? null)--}}
{{--                                                    ->first();--}}
{{--                                            @endphp--}}

{{--                                            @if(!$meeting->is_started)--}}
{{--                                                <span class="badge bg-secondary w-100">Meeting not yet started</span>--}}
{{--                                            @else--}}
{{--                                                --}}{{-- Step 1: Conflict of Interest --}}
{{--                                                @if(!$declaration)--}}
{{--                                                    <a href="{{ route('board.conflict.register', $meeting->id) }}"--}}
{{--                                                       class="btn btn-sm btn-outline-warning">--}}
{{--                                                        ✍️ Declare Conflict--}}
{{--                                                    </a>--}}
{{--                                                @else--}}
{{--                                                    --}}{{-- Step 2: Unlock other actions --}}
{{--                                                    <!-- View Notice -->--}}
{{--                                                    <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}"--}}
{{--                                                       target="_blank"--}}
{{--                                                       class="btn btn-sm btn-outline-primary">--}}
{{--                                                        <i class="bi bi-eye-fill"></i> View Notice--}}
{{--                                                    </a>--}}

{{--                                                    <!-- View My Declaration -->--}}
{{--                                                    <button type="button" class="btn btn-sm btn-outline-warning"--}}
{{--                                                            data-bs-toggle="modal"--}}
{{--                                                            data-bs-target="#conflictModal{{ $meeting->id }}">--}}
{{--                                                        ✅ View My Declaration--}}
{{--                                                    </button>--}}

{{--                                                    <!-- View Meeting -->--}}
{{--                                                    <a href="{{ route('board.meetings.start', $meeting->id) }}"--}}
{{--                                                       class="btn btn-sm btn-outline-success">--}}
{{--                                                        🟢 View Meeting--}}
{{--                                                    </a>--}}

{{--                                                    <!-- Modal for Viewing Declaration -->--}}
{{--                                                    <div class="modal fade" id="conflictModal{{ $meeting->id }}" tabindex="-1"--}}
{{--                                                         aria-labelledby="conflictModalLabel{{ $meeting->id }}" aria-hidden="true">--}}
{{--                                                        <div class="modal-dialog modal-dialog-centered">--}}
{{--                                                            <div class="modal-content">--}}
{{--                                                                <div class="modal-header bg-light">--}}
{{--                                                                    <h5 class="modal-title" id="conflictModalLabel{{ $meeting->id }}">--}}
{{--                                                                        📄 Your Conflict Declaration--}}
{{--                                                                    </h5>--}}
{{--                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="modal-body">--}}
{{--                                                                    <p><strong>Status:</strong> {{ $declaration->conflict_status }}</p>--}}
{{--                                                                    @if($declaration->details)--}}
{{--                                                                        <p><strong>Details:</strong><br>{{ $declaration->details }}</p>--}}
{{--                                                                    @endif--}}
{{--                                                                    <p class="text-muted">--}}
{{--                                                                        <small>Submitted: {{ $declaration->created_at->format('d M Y h:i A') }}</small>--}}
{{--                                                                    </p>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="modal-footer">--}}
{{--                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </td>--}}


                                    {{--                                    <td>--}}
{{--                                        <div class="btn-group-vertical w-100 gap-1" role="group">--}}
{{--                                            <!-- View Notice -->--}}
{{--                                            @if($meeting->is_started)--}}
{{--                                                        <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">--}}
{{--                                                            <i class="bi bi-eye-fill"></i> View Notice--}}
{{--                                                        </a>--}}

{{--                                                        <!-- Conflict of Interest -->--}}
{{--                                                        @php--}}
{{--                                                            $declaration = \App\Models\ConflictOfInterestDeclaration::where('meeting_id', $meeting->id)--}}
{{--                                                                ->where('board_member_id', auth()->user()->boardMember->id ?? null)--}}
{{--                                                                ->first();--}}
{{--                                                        @endphp--}}

{{--                                                        @if($declaration)--}}
{{--                                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#conflictModal{{ $meeting->id }}">--}}
{{--                                                                ✅ View My Declaration--}}
{{--                                                            </button>--}}
{{--                                                        @else--}}
{{--                                                            <a href="{{ route('board.conflict.register', $meeting->id) }}" class="btn btn-sm btn-outline-warning">--}}
{{--                                                                ✍️ Declare Conflict--}}
{{--                                                            </a>--}}
{{--                                                        @endif--}}

{{--                                                        <!-- Start Meeting -->--}}
{{--            --}}{{--                                            <a href="{{ route('board.meetings.start', $meeting->id) }}" class="btn btn-sm btn-outline-success">--}}
{{--            --}}{{--                                                🟢 Start Meeting--}}
{{--            --}}{{--                                            </a>--}}
{{--                                                        <a href="{{ route('board.meetings.start', $meeting->id) }}" class="btn btn-sm btn-outline-success">--}}
{{--                                                            🟢 View Meeting--}}
{{--                                                        </a>--}}

{{--                                                @if($declaration)--}}
{{--                                                    <!-- Modal for Viewing Declaration -->--}}
{{--                                                    <div class="modal fade" id="conflictModal{{ $meeting->id }}" tabindex="-1" aria-labelledby="conflictModalLabel{{ $meeting->id }}" aria-hidden="true">--}}
{{--                                                        <div class="modal-dialog modal-dialog-centered">--}}
{{--                                                            <div class="modal-content">--}}
{{--                                                                <div class="modal-header bg-light">--}}
{{--                                                                    <h5 class="modal-title" id="conflictModalLabel{{ $meeting->id }}">--}}
{{--                                                                        📄 Your Conflict Declaration--}}
{{--                                                                    </h5>--}}
{{--                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="modal-body">--}}
{{--                                                                    <p><strong>Status:</strong> {{ $declaration->conflict_status }}</p>--}}
{{--                                                                    @if($declaration->details)--}}
{{--                                                                        <p><strong>Details:</strong><br>{{ $declaration->details }}</p>--}}
{{--                                                                    @endif--}}
{{--                                                                    <p class="text-muted"><small>Submitted: {{ $declaration->created_at->format('d M Y h:i A') }}</small></p>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="modal-footer">--}}
{{--                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            @else--}}
{{--                                                <span class="badge bg-secondary w-100">Meeting not yet started</span>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
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
