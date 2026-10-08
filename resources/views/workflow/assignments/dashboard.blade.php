@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="btn btn-sm btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Assignment Dashboard</h4>
                        </div>
                        <p class="text-muted mb-0 ms-5">
                            {{ $meeting->title }} &middot; {{ $meeting->reference_number ?? '' }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('workflow.assignments.index', $meeting->id) }}" class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                            <i class="mdi mdi-account-plus-outline"></i> Assign More
                        </a>
                        @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
                    </div>
                </div>
            </div>
        </div>

        {{-- Stage Indicator --}}
        @include('workflow.components.stage-indicator', ['currentStage' => 4, 'stageLabel' => 'Officer Assignment', 'meeting' => $meeting])

        {{-- Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Row --}}
        @php
            $totalAssigned = $stats['total'];
            $readCount = $stats['read'];
            $unreadCount = $stats['unread'];
            $papersCount = $stats['papers_submitted'];
            $readPercent = $totalAssigned > 0 ? round(($readCount / $totalAssigned) * 100) : 0;
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; background: rgba(200, 16, 46,0.1);">
                                <i class="mdi mdi-account-group-outline" style="font-size: 20px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $totalAssigned }}</h4>
                                <small class="text-muted">Total Assigned</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; background: rgba(9,145,57,0.1);">
                                <i class="mdi mdi-eye-check-outline" style="font-size: 20px; color: #099139;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $readCount }}</h4>
                                <small class="text-muted">Read</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; background: rgba(255,193,7,0.1);">
                                <i class="mdi mdi-eye-off-outline" style="font-size: 20px; color: #ffc107;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $unreadCount }}</h4>
                                <small class="text-muted">Unread</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; background: rgba(23,184,79,0.1);">
                                <i class="mdi mdi-file-document-check-outline" style="font-size: 20px; color: #17b84f;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $papersCount }}</h4>
                                <small class="text-muted">Papers Submitted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Read Progress --}}
        @if($totalAssigned > 0)
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-muted fw-semibold">Read Receipts Progress</small>
                        <small class="fw-semibold" style="color: #099139;">{{ $readPercent }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $readPercent }}%; background: #099139;"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Assignments Table --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="mdi mdi-table-account me-1"></i> All Assignments</h6>
            </div>
            <div class="card-body">
                @if($assignments->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-clipboard-text-off-outline" style="font-size: 56px; color: #b5b5c3;"></i>
                        <h5 class="mt-3 mb-2">No Assignments Yet</h5>
                        <p class="text-muted mb-3">Start by assigning officers to agenda items.</p>
                        <a href="{{ route('workflow.assignments.index', $meeting->id) }}" class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                            <i class="mdi mdi-account-plus-outline"></i> Assign Officers
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="font-size: 12px; width: 36px;">#</th>
                                    <th style="font-size: 12px;">Item</th>
                                    <th style="font-size: 12px;">Agenda Title</th>
                                    <th style="font-size: 12px;">Assigned Officer</th>
                                    <th style="font-size: 12px;">Division / Directorate</th>
                                    <th style="font-size: 12px;">Notified</th>
                                    <th style="font-size: 12px;">Read</th>
                                    <th style="font-size: 12px;">Papers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $i => $assignment)
                                    <tr>
                                        <td class="text-muted" style="font-size: 12px;">{{ $i + 1 }}</td>
                                        <td>
                                            <span class="badge bg-twwda-navy" style="font-size: 11px;">{{ $assignment->agenda_item_number }}</span>
                                        </td>
                                        <td style="font-size: 13px;">{{ Str::limit($assignment->agenda_item_title, 45) }}</td>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle bg-twwda-navy text-white d-flex align-items-center justify-content-center me-2"
                                                     style="width: 32px; height: 32px; font-size: 10px; font-weight: 600; flex-shrink: 0; margin-top: 2px;">
                                                    {{ strtoupper(substr($assignment->user->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <div style="min-width: 0;">
                                                    <span style="font-size: 13px; font-weight: 500;">{{ $assignment->user->name ?? '-' }}</span>
                                                    @if($assignment->user && $assignment->user->email)
                                                        <br>
                                                        <small class="text-muted" style="font-size: 10px;">
                                                            <i class="mdi mdi-email-outline"></i> {{ $assignment->user->email }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($assignment->director_id && $assignment->director_id === $assignment->user_id)
                                                {{-- This is a director assigned directly --}}
                                                <span class="badge bg-success" style="font-size: 10px;">Director</span>
                                                @if($assignment->director)
                                                    @php
                                                        $dirMapping = \App\Models\Twwda\TwwdaUserDirectorateMapping::where('user_id', $assignment->user_id)->active()->directors()->with('directorate')->first();
                                                    @endphp
                                                    @if($dirMapping && $dirMapping->directorate)
                                                        <br>
                                                        <small class="text-muted" style="font-size: 10px;">
                                                            <i class="mdi mdi-domain"></i> {{ $dirMapping->directorate->name }}
                                                        </small>
                                                    @endif
                                                @endif
                                            @elseif($assignment->user && $assignment->user->division)
                                                <span style="font-size: 12px;">{{ $assignment->user->division->name }}</span>
                                                @if($assignment->user->division->directorate)
                                                    <br>
                                                    <small class="text-muted" style="font-size: 10px;">
                                                        <i class="mdi mdi-domain"></i> {{ $assignment->user->division->directorate->name }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->notified_at)
                                                <span class="text-success"><i class="mdi mdi-bell-check-outline" style="font-size: 16px;"></i></span>
                                                <br><small class="text-muted" style="font-size: 10px;">{{ $assignment->notified_at->format('d M H:i') }}</small>
                                            @else
                                                <span class="text-warning"><i class="mdi mdi-bell-off-outline" style="font-size: 16px;"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->isRead())
                                                <span class="text-success"><i class="mdi mdi-check-circle" style="font-size: 16px;"></i></span>
                                                <br><small class="text-muted" style="font-size: 10px;">
                                                    {{ ($assignment->email_read_at ?? $assignment->system_read_at)?->format('d M H:i') }}
                                                </small>
                                            @else
                                                <span class="text-danger"><i class="mdi mdi-close-circle-outline" style="font-size: 16px;"></i></span>
                                                <br><small class="text-muted" style="font-size: 10px;">Unread</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->boardPapers && $assignment->boardPapers->count() > 0)
                                                <span class="badge bg-success" style="font-size: 10px;">
                                                    {{ $assignment->boardPapers->count() }} paper(s)
                                                </span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 10px;">Not started</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
