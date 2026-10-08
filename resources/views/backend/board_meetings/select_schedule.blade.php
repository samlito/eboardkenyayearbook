{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}

{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="page-title-box d-flex justify-content-between align-items-center">--}}
{{--                        <h4 class="page-title">All Scheduled Meetings</h4>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        <h4 class="mb-4">Board Meetings from Active Almanac ({{ $currentAlmanac->serialno }})</h4>--}}

{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}


{{--                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th>Date</th>--}}
{{--                <th>Purpose</th>--}}
{{--                <th>Committee</th>--}}
{{--                <th>Budget</th>--}}
{{--                <th>Status</th>--}}
{{--                <th>Meeting</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @forelse ($schedules as $schedule)--}}
{{--                <tr>--}}
{{--                    <td>{{ \Carbon\Carbon::parse($schedule->meeting_date)->format('d M Y') }}</td>--}}
{{--                    <td data-order="{{ optional($schedule->meeting_date)->format('Y-m-d') }}">--}}
{{--                        {{ optional($schedule->meeting_date)->format('d M Y') }}--}}
{{--                    </td>--}}

{{--                    --}}{{--                    <td>{{ $schedule->purpose }}</td>--}}
{{--                    <td>{{ $schedule->boardCommittee->name ?? 'Full Board' }}</td>--}}
{{--                    <td>Ksh {{ number_format($schedule->budget, 2) }}</td>--}}
{{--                    <td>{{ $schedule->status }}</td>--}}
{{--                    <td>--}}
{{--                        @if ($schedule->boardMeeting)--}}
{{--                            {{ route('board_meetings.show', $schedule->boardMeeting->id) }}--}}
{{--                            <a href="#" class="btn btn-sm btn-warning">--}}
{{--                             Meeting Already Initiated--}}
{{--                            </a>--}}
{{--                        @else--}}
{{--                            {{ route('board_meetings.create_from_schedule', $schedule->id) }}--}}
{{--                            <a href="" class="btn btn-sm btn-primary">--}}
{{--                                Initiate Meeting--}}
{{--                            </a>--}}
{{--                            <button--}}
{{--                                class="btn btn-sm btn-primary initiate-meeting-btn"--}}
{{--                                data-schedule-id="{{ $schedule->id }}"--}}
{{--                            >--}}
{{--                                Initiate Meeting--}}
{{--                            </button>--}}

{{--                        @endif--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @empty--}}
{{--                <tr>--}}
{{--                    <td colspan="6" class="text-center">No approved meeting schedules found in active almanac.</td>--}}
{{--                </tr>--}}
{{--            @endforelse--}}
{{--            </tbody>--}}
{{--        </table>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--    </div>--}}


{{--            <script>--}}
{{--                document.querySelectorAll('.initiate-meeting-btn').forEach(button => {--}}
{{--                    button.addEventListener('click', function () {--}}
{{--                        const scheduleId = this.dataset.scheduleId;--}}
{{--                        const button = this;--}}

{{--                        button.disabled = true;--}}
{{--                        const originalText = button.innerHTML;--}}
{{--                        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Initiating...';--}}

{{--                        fetch(`/board-meetings/initiate/${scheduleId}`, {--}}
{{--                            method: 'POST',--}}
{{--                            headers: {--}}
{{--                                'X-CSRF-TOKEN': '{{ csrf_token() }}',--}}
{{--                                'Accept': 'application/json',--}}
{{--                                'Content-Type': 'application/json'--}}
{{--                            },--}}
{{--                        })--}}
{{--                            .then(response => response.json())--}}
{{--                            .then(data => {--}}
{{--                                if (data.message) {--}}
{{--                                    alert(data.message);--}}
{{--                                    location.reload(); // or update the row dynamically--}}
{{--                                } else {--}}
{{--                                    alert('Unexpected response');--}}
{{--                                }--}}
{{--                            })--}}
{{--                            .catch(error => {--}}
{{--                                alert('Error initiating meeting');--}}
{{--                                console.error(error);--}}
{{--                            })--}}
{{--                            .finally(() => {--}}
{{--                                button.disabled = false;--}}
{{--                                button.innerHTML = originalText;--}}
{{--                            });--}}
{{--                    });--}}
{{--                });--}}
{{--            </script>--}}


{{--@endsection--}}
@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            ```
            <!-- Page Title -->
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">All Scheduled Meetings</h4>
                </div>
            </div>

            <!-- Almanac Info -->
            <div class="row mb-3">
                <div class="col-12">
                    <h5>
                        Board Meetings from Active Almanac
                        <span class="text-primary">({{ $currentAlmanac->serialno }})</span>
                    </h5>
                </div>
            </div>

            <!-- Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <table id="schedule-table" class="table table-striped table-bordered w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Committee</th>
                                    <th>Budget</th>
                                    <th>Meeting</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr>
                                        <!-- Date -->
                                        <td data-order="{{ optional($schedule->meeting_date)->format('Y-m-d') }}">
                                            {{ optional($schedule->meeting_date)->format('d M Y') }}
                                        </td>

                                        <!-- Committee -->
                                        <td>
                                            {{ $schedule->boardCommittee->name ?? 'Full Board' }}
                                        </td>

                                        <!-- Budget -->
                                        <td>
                                            <strong>Ksh {{ number_format($schedule->budget, 2) }}</strong>
                                        </td>

                                        <!-- Action -->
                                        <td>
                                            @if ($schedule->boardMeeting)
                                                <span class="badge bg-warning text-dark">
                                                Meeting Already Initiated
                                            </span>
                                            @else
                                                <button
                                                    class="btn btn-sm btn-primary initiate-meeting-btn"
                                                    data-schedule-id="{{ $schedule->id }}">
                                                    Initiate Meeting
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No approved meeting schedules found in active almanac.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
        ```

    </div>

    <!-- Scripts -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ✅ Initialize DataTable with correct sorting
            $('#schedule-table').DataTable({
                order: [[0, 'desc']], // Latest dates first
                pageLength: 10,
                responsive: true
            });

            // ✅ Handle Initiate Meeting
            document.querySelectorAll('.initiate-meeting-btn').forEach(button => {
                button.addEventListener('click', function () {

                    const scheduleId = this.dataset.scheduleId;
                    const btn = this;

                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Initiating...';

                    fetch(`/board-meetings/initiate/${scheduleId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message || 'Meeting initiated successfully');
                            location.reload();
                        })
                        .catch(err => {
                            alert('Error initiating meeting');
                            console.error(err);
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        });

                });
            });

        });
    </script>

@endsection
