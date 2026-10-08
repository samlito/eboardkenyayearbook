@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1"><i class="mdi mdi-clipboard-text-outline me-1"></i> My Assignments</h4>
                        <p class="text-muted mb-0">Board paper assignments across all meetings</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; background: rgba(200, 16, 46,0.1);">
                                <i class="mdi mdi-clipboard-list-outline" style="font-size: 20px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                <small class="text-muted">Total Assignments</small>
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
                                <h4 class="mb-0">{{ $stats['unread'] }}</h4>
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
                                 style="width: 44px; height: 44px; background: rgba(220,53,69,0.1);">
                                <i class="mdi mdi-file-clock-outline" style="font-size: 20px; color: #dc3545;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                <small class="text-muted">Papers Pending</small>
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
                                <i class="mdi mdi-file-check-outline" style="font-size: 20px; color: #099139;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $stats['papers_submitted'] }}</h4>
                                <small class="text-muted">Papers Submitted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assignments grouped by meeting --}}
        @if($assignments->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-clipboard-text-off-outline" style="font-size: 56px; color: #b5b5c3;"></i>
                    <h5 class="mt-3 mb-2">No Assignments Yet</h5>
                    <p class="text-muted">You have not been assigned any agenda items. You will be notified when assignments are made.</p>
                </div>
            </div>
        @else
            @foreach($groupedByMeeting as $meetingId => $meetingAssignments)
                @php $meeting = $meetingAssignments->first()->meeting; @endphp
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">
                                <i class="mdi mdi-calendar-outline me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                {{ $meeting->title ?? 'Meeting' }}
                            </h6>
                            <small class="text-muted">
                                {{ $meeting->boardCommittee->name ?? '' }}
                                @if($meeting->meeting_date)
                                    &middot; {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                                @endif
                                &middot; {{ $meeting->reference_number ?? '' }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @php $meetingNotice = $meeting->internalNotice ?? null; @endphp
                            @if($meetingNotice && $meetingNotice->isApproved())
                                <span class="badge bg-success" style="font-size: 10px;" title="Internal notice approved">
                                    <i class="mdi mdi-file-sign me-1"></i> Notice Approved
                                </span>
                            @elseif($meetingNotice)
                                <span class="badge bg-warning text-dark" style="font-size: 10px;" title="Internal notice pending">
                                    <i class="mdi mdi-file-clock-outline me-1"></i> Notice Pending
                                </span>
                            @endif
                            <span class="badge bg-twwda-navy" style="font-size: 11px;">
                                {{ $meetingAssignments->count() }} item(s)
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="font-size: 12px; width: 60px;">Item</th>
                                        <th style="font-size: 12px;">Agenda Item</th>
                                        <th style="font-size: 12px; width: 100px;">Status</th>
                                        <th style="font-size: 12px; width: 120px;">Paper</th>
                                        <th style="font-size: 12px; width: 130px;">Assigned</th>
                                        <th style="font-size: 12px; width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($meetingAssignments as $assignment)
                                        <tr class="{{ !$assignment->isRead() ? 'table-warning' : '' }}">
                                            <td>
                                                <span class="badge bg-twwda-navy" style="font-size: 11px;">{{ $assignment->agenda_item_number }}</span>
                                            </td>
                                            <td>
                                                <span style="font-size: 13px; {{ !$assignment->isRead() ? 'font-weight: 600;' : '' }}">
                                                    {{ Str::limit($assignment->agenda_item_title, 60) }}
                                                </span>
                                                @if(!$assignment->isRead())
                                                    <span class="badge bg-warning text-dark ms-1" style="font-size: 9px;">NEW</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assignment->isRead())
                                                    <span class="badge bg-success" style="font-size: 10px;">
                                                        <i class="mdi mdi-check-circle"></i> Read
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark" style="font-size: 10px;">
                                                        <i class="mdi mdi-eye-off-outline"></i> Unread
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assignment->boardPapers && $assignment->boardPapers->count() > 0)
                                                    <span class="badge bg-success" style="font-size: 10px;">
                                                        <i class="mdi mdi-file-check"></i> {{ $assignment->boardPapers->count() }} paper(s)
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                        <i class="mdi mdi-file-clock-outline"></i> Not started
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    {{ $assignment->created_at->format('d M Y') }}
                                                </small>
                                                <br>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    by {{ $assignment->assigner->name ?? '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('workflow.officer.assignment.show', $assignment->id) }}"
                                                   class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }}; font-size: 11px;">
                                                    <i class="mdi mdi-eye-outline"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>

@endsection
