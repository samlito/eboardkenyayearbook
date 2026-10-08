



{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}
{{--    @include('backend.actual_meetings.partials.preview_notice_modal')--}}

{{--    <div class="content">--}}

{{--        <!-- Start Content-->--}}
{{--        <div class="container-fluid">--}}

{{--            <!-- start page title -->--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="page-title-box">--}}
{{--                        <div class="page-title-right">--}}
{{--                            <ol class="breadcrumb m-0">--}}

{{--                            </ol>--}}
{{--                        </div>--}}
{{--                        <h4 class="page-title">All Board Members List</h4>--}}
{{--                        <h4 class="mb-4">📅 Actual Board Meetings</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}


{{--            <!-- end page title -->--}}

{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}


{{--                            --}}{{--                            <table  class="table dt-responsive nowrap w-100">--}}
{{--                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">--}}

{{--                                <thead class="table-light">--}}
{{--                                <tr>--}}
{{--                                    <th>#</th>--}}
{{--                                    <th>Title</th>--}}
{{--                                    <th>Date</th>--}}
{{--                                    <th>Committee</th>--}}
{{--                                    <th>Status</th>--}}
{{--                                    <th>Notice Sent On</th>--}}
{{--                                    <th>Actions</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}

{{--                                @foreach($meetings as $index => $meeting)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $index + 1 }}</td>--}}
{{--                                        <td>{{ $meeting->title }}</td>--}}
{{--                                        <td>{{ \Carbon\Carbon::parse($meeting->date)->format('d M Y') }}</td>--}}
{{--                                        <td>{{ $meeting->committee->name ?? 'N/A' }}</td>--}}
{{--                                        <td><span class="badge bg-success">Published</span></td>--}}
{{--                                        <td>{{ $meeting->board_notified_at->format('d M Y h:i A') }}</td>--}}
{{--                                        <td>--}}
{{--                                            <a href="{{ route('actual.meetings.notice.create', $meeting->id) }}" class="btn btn-sm btn-primary">View</a>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            @if ($meeting->officialNotice)--}}
{{--                                                <a href="{{ route('actual.meetings.notice.edit', $meeting->id) }}" class="btn btn-sm btn-warning">--}}
{{--                                                    📝 Edit Notice--}}
{{--                                                </a>--}}

{{--                                                <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}" class="btn btn-sm btn-info" target="_blank">--}}
{{--                                                    <i class="bi bi-eye-fill"></i> Preview--}}
{{--                                                </a>--}}
{{--                                                <button--}}
{{--                                                    type="button"--}}
{{--                                                    class="btn btn-sm btn-info preview-btn"--}}
{{--                                                    data-id="{{ $meeting->id }}"--}}
{{--                                                    data-bs-toggle="modal"--}}
{{--                                                    data-bs-target="#noticePreviewModal">--}}
{{--                                                    <i class="bi bi-eye-fill"></i> Preview Notice--}}
{{--                                                </button>--}}


{{--                                                <form action="{{ route('actual.meetings.notice.send', $meeting->id) }}" method="POST" class="d-inline">--}}
{{--                                                    @csrf--}}
{{--                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send notice to board members?');">--}}
{{--                                                        📤 Send--}}
{{--                                                    </button>--}}
{{--                                                </form>--}}

{{--                                                <form action="{{ route('actual.meetings.notice.send', $meeting->id) }}" method="POST" class="d-inline">--}}
{{--                                                    @csrf--}}
{{--                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send notice to board members?');">--}}
{{--                                                        📤 Send--}}
{{--                                                    </button>--}}
{{--                                                </form>--}}

{{--                                            @else--}}
{{--                                                <a href="{{ route('actual.meetings.notice.create', $meeting->id) }}" class="btn btn-sm btn-primary">--}}
{{--                                                    ➕ Add Notice--}}
{{--                                                </a>--}}
{{--                                            @endif--}}


{{--                                        </td>--}}

{{--                                    </tr>--}}
{{--                                @endforeach--}}


{{--                                </tbody>--}}
{{--                            </table>--}}

{{--                        </div> <!-- end card body-->--}}
{{--                    </div> <!-- end card -->--}}
{{--                </div><!-- end col-->--}}
{{--            </div>--}}
{{--            <!-- end row-->--}}




{{--        </div> <!-- container -->--}}

{{--    </div> <!-- content -->--}}



{{--@endsection--}}



{{--@foreach($meetings as $index => $meeting)--}}
{{--    <tr>--}}
{{--        <td>{{ $index + 1 }}</td>--}}
{{--        <td>{{ $meeting->title }}</td>--}}
{{--        <td>{{ \Carbon\Carbon::parse($meeting->date)->format('d M Y') }}</td>--}}
{{--        <td>{{ $meeting->committee->name ?? 'N/A' }}</td>--}}
{{--        <td><span class="badge bg-success">Published</span></td>--}}
{{--        <td>{{ $meeting->board_notified_at->format('d M Y h:i A') }}</td>--}}
{{--        <td>--}}
{{--            @if ($meeting->officialNotice)--}}
{{--                <a href="{{ route('actual.meetings.notice.edit', $meeting->id) }}" class="btn btn-sm btn-warning">--}}
{{--                    📝 Edit Notice--}}
{{--                </a>--}}

{{--                --}}{{----}}{{-- Open Modal --}}
{{--                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#noticePreviewModal-{{ $meeting->id }}">--}}
{{--                    <i class="bi bi-eye-fill"></i> Preview Notice--}}
{{--                </button>--}}

{{--                <form action="{{ route('actual.meetings.notice.send', $meeting->id) }}" method="POST" class="d-inline">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send notice to board members?');">--}}
{{--                        📤 Send--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            @else--}}
{{--                <a href="{{ route('actual.meetings.notice.create', $meeting->id) }}" class="btn btn-sm btn-primary">--}}
{{--                    ➕ Add Notice--}}
{{--                </a>--}}
{{--            @endif--}}
{{--        </td>--}}
{{--    </tr>--}}

{{--    --}}{{----}}{{-- Include modal per meeting --}}
{{--    @include('backend.actual_meetings.partials.preview_notice_modal', ['meeting' => $meeting])--}}
{{--@endforeach--}}

@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-3">📅 Actual Board Meetings</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Committee</th>
                                    <th>Status</th>
                                    <th>10 Day Notice Sent On</th>
                                    <th>3 Day Notice Sent On</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($meetings as $index => $meeting)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($meeting->date)->format('d M Y') }}</td>
                                        <td>{{ $meeting->committee->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-success">Published</span></td>
                                        <td>{{ $meeting->board_notified_at->format('d M Y h:i A') }}</td>
{{--                                        <td>{{ optional($meeting->final_board_notice_sent_at)->format('d M Y h:i A') ?? '—' }}</td>--}}
                                        <td>{{ $meeting->final_board_notice_sent_at?->format('d M Y h:i A') ?? 'Not Sent' }}</td>

                                        <td>
                                            @if(!$meeting->officialNotice)
                                                {{-- Step 1: Add Notice --}}
                                                <a href="{{ route('actual.meetings.notice.create', $meeting->id) }}" class="btn btn-sm btn-primary">
                                                    ➕ Add Notice
                                                </a>
                                            @else
                                                @if(is_null($meeting->final_board_notice_sent_at))
                                                    {{-- Step 2: Notice exists but not sent yet --}}
                                                    <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}"
                                                       class="btn btn-sm btn-info mb-1" target="_blank">
                                                        👁️ Preview Notice
                                                    </a>

                                                    <form action="{{ route('actual.meetings.notice.send', $meeting->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success mb-1"
                                                                onclick="return confirm('Send notice to board members?');">
                                                            📤 Send Notice
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Step 2: Notice exists but not sent yet --}}
                                                    <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}"
                                                       class="btn btn-sm btn-info mb-1" target="_blank">
                                                        👁️ Preview Notice
                                                    </a>
                                                    @if(!$meeting->is_started)
                                                        {{-- Step 3: Start Meeting --}}
                                                        <form action="{{ route('actual.meetings.start', $meeting->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-dark mb-1">
                                                                🚀 Start Meeting
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Step 4: Minutes & Registers --}}
                                                        @php $hasMinutes = $meeting->minutes()->exists(); @endphp

                                                        @if ($hasMinutes)
                                                            <a href="{{ route('actual.meetings.minutes.capture', $meeting->id) }}" class="btn btn-sm btn-outline-secondary mb-1">
                                                                📝 Continue Minutes
                                                            </a>
                                                            <a href="{{ route('actual.meetings.minutes.preview', $meeting->id) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                📄 Preview Minutes
                                                            </a>
                                                        @else
                                                            <a href="{{ route('actual.meetings.minutes.capture', $meeting->id) }}" class="btn btn-sm btn-dark mb-1">
                                                                🖊️ Capture Minutes
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('actual.meetings.conflict.download', $meeting->id) }}" class="btn btn-sm btn-outline-dark mb-1">
                                                            📄 Conflict Register (PDF)
                                                        </a>

                                                        <a href="{{ route('actual.meetings.attendance.download', $meeting->id) }}" class="btn btn-sm btn-outline-dark mb-1">
                                                            📋 Attendance Register (PDF)
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>

                                        {{--                                        <td>--}}
{{--                                            @if ($meeting->officialNotice)--}}
{{--                                                <a href="{{ route('actual.meetings.notice.edit', $meeting->id) }}" class="btn btn-sm btn-warning mb-1">--}}
{{--                                                    📝 Edit Notice--}}
{{--                                                </a>--}}

{{--                                                <a href="{{ route('actual.meetings.notice.preview', $meeting->id) }}" class="btn btn-sm btn-info mb-1" target="_blank">--}}
{{--                                                    <i class="bi bi-eye-fill"></i> Preview--}}
{{--                                                </a>--}}

{{--                                                <form action="{{ route('actual.meetings.notice.send', $meeting->id) }}" method="POST" class="d-inline">--}}
{{--                                                    @csrf--}}
{{--                                                    <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Send notice to board members?');">--}}
{{--                                                        📤 Send--}}
{{--                                                    </button>--}}
{{--                                                </form>--}}

{{--                                                @php--}}
{{--                                                    $hasMinutes = $meeting->minutes()->exists();--}}
{{--                                                @endphp--}}

{{--                                                @if ($hasMinutes)--}}
{{--                                                    <a href="{{ route('actual.meetings.minutes.capture', $meeting->id) }}" class="btn btn-sm btn-outline-secondary">--}}
{{--                                                        📝 Continue Minutes--}}
{{--                                                    </a>--}}
{{--                                                    <a href="{{ route('actual.meetings.minutes.preview', $meeting->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">--}}
{{--                                                        📄 Preview Minutes--}}
{{--                                                    </a>--}}
{{--                                                @else--}}
{{--                                                    <a href="{{ route('actual.meetings.minutes.capture', $meeting->id) }}" class="btn btn-sm btn-dark">--}}
{{--                                                        🖊️ Capture Minutes--}}
{{--                                                    </a>--}}
{{--                                                @endif--}}



{{--                                                <a href="{{ route('actual.meetings.conflict.download', $meeting->id) }}" class="btn btn-sm btn-dark">--}}
{{--                                                    📄 Download Conflict Register (PDF)--}}
{{--                                                </a>--}}

{{--                                                <a href="{{ route('actual.meetings.attendance.download', $meeting->id) }}" class="btn btn-sm btn-dark">--}}
{{--                                                    📋 Attendance Register--}}
{{--                                                </a>--}}

{{--                                            @else--}}
{{--                                                <a href="{{ route('actual.meetings.notice.create', $meeting->id) }}" class="btn btn-sm btn-primary">--}}
{{--                                                    ➕ Add Notice--}}
{{--                                                </a>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
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
