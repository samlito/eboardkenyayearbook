@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER — Clean government-style header
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="twwda-page-header mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a href="{{ route('workflow.meetings.show', $meeting) }}" class="twwda-back-btn">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="mb-0 twwda-page-title">Live Meeting</h4>
                    <span class="twwda-status-live">
                        <span class="twwda-live-dot"></span> ACTIVE
                    </span>
                </div>
                <p class="twwda-page-subtitle ms-5">{{ $meeting->title }}</p>
            </div>
            <div>
                @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MEETING INFO STRIP — 4-column info bar
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="twwda-info-strip mb-4">
        <div class="row g-0">
            <div class="col-md-3 twwda-info-cell">
                <div class="twwda-info-icon" style="background: rgba(200, 16, 46, 0.1);">
                    <i class="mdi mdi-calendar" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                </div>
                <div>
                    <span class="twwda-info-label">Date</span>
                    <span class="twwda-info-value">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</span>
                </div>
            </div>
            <div class="col-md-3 twwda-info-cell">
                <div class="twwda-info-icon" style="background: rgba(23, 184, 79, 0.1);">
                    <i class="mdi mdi-clock-outline" style="color: #17b84f;"></i>
                </div>
                <div>
                    <span class="twwda-info-label">Time</span>
                    <span class="twwda-info-value">{{ $meeting->meeting_time ? \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') : 'TBD' }}</span>
                </div>
            </div>
            <div class="col-md-3 twwda-info-cell">
                <div class="twwda-info-icon" style="background: rgba(9, 145, 57, 0.1);">
                    <i class="mdi mdi-map-marker-outline" style="color: #099139;"></i>
                </div>
                <div>
                    <span class="twwda-info-label">Venue</span>
                    <span class="twwda-info-value">{{ $meeting->venue ?? 'TBD' }}</span>
                </div>
            </div>
            <div class="col-md-3 twwda-info-cell border-end-0">
                <div class="twwda-info-icon" style="background: rgba(255, 193, 7, 0.1);">
                    <i class="mdi mdi-account-group" style="color: #d4a017;"></i>
                </div>
                <div>
                    <span class="twwda-info-label">Committee</span>
                    <span class="twwda-info-value">{{ $meeting->boardCommittee->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MAIN TABBED CONTENT — 5 tabs
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="twwda-card">
        {{-- Tab Navigation --}}
        <div class="twwda-tab-header">
            <ul class="nav nav-tabs twwda-nav-tabs" id="meetingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-board-attendance" role="tab">
                        <i class="mdi mdi-account-check-outline"></i>
                        <span>Board Attendance</span>
                        @if($completedDeclarations->count() > 0)
                            <span class="twwda-tab-badge bg-success">{{ $completedDeclarations->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-conflict" role="tab">
                        <i class="mdi mdi-shield-alert-outline"></i>
                        <span>Conflict Register</span>
                        @php
                            $unsignedCount = $allDeclarations->whereNull('signed_at')->count();
                        @endphp
                        @if($unsignedCount > 0)
                            <span class="twwda-tab-badge bg-warning">{{ $unsignedCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-other-attendees" role="tab">
                        <i class="mdi mdi-account-group-outline"></i>
                        <span>Other Attendees</span>
                        @if($otherAttendees->count() > 0)
                            <span class="twwda-tab-badge bg-info">{{ $otherAttendees->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-papers" role="tab">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span>Board Papers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-minutes" role="tab">
                        <i class="mdi mdi-file-document-edit-outline"></i>
                        <span>Minutes</span>
                        @if($meetingMinutes)
                            <span class="twwda-tab-badge bg-success">
                                <i class="mdi mdi-check" style="font-size:11px;"></i>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-timeline" role="tab">
                        <i class="mdi mdi-timeline-clock-outline"></i>
                        <span>Timeline</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="twwda-tab-body">
            <div class="tab-content">

                {{-- ═══════════════════════════════════════════════════
                     TAB 1: BOARD ATTENDANCE REGISTER
                     Board members present = those who signed conflict
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade show active" id="tab-board-attendance" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="twwda-section-title mb-1">Board Attendance Register</h5>
                            <p class="twwda-section-subtitle mb-0">Board members present — confirmed through signed conflict of interest declarations</p>
                        </div>
                        @if($completedDeclarations->count() > 0)
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('workflow.meeting.export.attendance.pdf', $meeting) }}"
                                   class="btn btn-sm twwda-btn-outline" target="_blank">
                                    <i class="mdi mdi-file-pdf-box me-1"></i> PDF
                                </a>
                                <a href="{{ route('workflow.meeting.export.attendance.csv', $meeting) }}"
                                   class="btn btn-sm twwda-btn-outline-success">
                                    <i class="mdi mdi-microsoft-excel me-1"></i> Excel
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Stats Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="twwda-stat-card twwda-stat-success">
                                <div class="twwda-stat-number">{{ $completedDeclarations->count() }}</div>
                                <div class="twwda-stat-label">Present</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="twwda-stat-card twwda-stat-warning">
                                <div class="twwda-stat-number">{{ $pendingMembers->count() }}</div>
                                <div class="twwda-stat-label">Pending</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="twwda-stat-card">
                                <div class="twwda-stat-number">{{ $committeeMembers->count() }}</div>
                                <div class="twwda-stat-label">Total Members</div>
                            </div>
                        </div>
                    </div>

                    {{-- Board Members Present Table --}}
                    @if($completedDeclarations->count() > 0)
                        <h6 class="twwda-subsection-title mb-3">
                            <i class="mdi mdi-check-circle text-success me-1"></i> Members Present
                        </h6>
                        <div class="twwda-table-wrapper mb-4">
                            <table class="table twwda-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Board Member</th>
                                        <th>Designation</th>
                                        <th>Conflict Status</th>
                                        <th>Signed At</th>
                                        <th style="width: 120px;" class="text-center">Signature</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedDeclarations->sortBy(fn($d) => optional($d->boardMember)->names) as $i => $decl)
                                        <tr>
                                            <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if(optional($decl->boardMember)->photo)
                                                        <img src="{{ asset('uploads/board_members/' . $decl->boardMember->photo) }}"
                                                             class="twwda-member-photo" alt="">
                                                    @else
                                                        <div class="twwda-avatar">
                                                            {{ strtoupper(substr(optional($decl->boardMember)->names ?? 'N', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="fw-semibold d-block">{{ optional($decl->boardMember)->names ?? '—' }}</span>
                                                        @if(optional($decl->boardMember)->email)
                                                            <small class="text-muted">{{ $decl->boardMember->email }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ optional($decl->boardMember)->designation ?? '—' }}</td>
                                            <td>
                                                @if($decl->has_conflict)
                                                    <span class="twwda-status-badge twwda-status-danger">
                                                        <i class="mdi mdi-alert me-1"></i>Declared
                                                    </span>
                                                @else
                                                    <span class="twwda-status-badge twwda-status-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>None
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($decl->signed_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($decl->signed_at)->format('h:i A') }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($decl->signature)
                                                    <img src="{{ $decl->signature }}" alt="Signature"
                                                         class="twwda-signature-thumb"
                                                         data-bs-toggle="modal"
                                                         data-bs-target="#signatureModal-{{ $decl->id }}"
                                                         style="cursor: pointer;">
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Pending Members --}}
                    @if($pendingMembers->count() > 0)
                        <h6 class="twwda-subsection-title mb-3">
                            <i class="mdi mdi-clock-outline text-warning me-1"></i> Awaiting Declaration
                        </h6>
                        <div class="twwda-table-wrapper">
                            <table class="table twwda-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Board Member</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingMembers as $i => $member)
                                        @php
                                            $partialDecl = $allDeclarations->where('board_member_id', $member->id)->first();
                                        @endphp
                                        <tr>
                                            <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($member->photo)
                                                        <img src="{{ asset('uploads/board_members/' . $member->photo) }}"
                                                             class="twwda-member-photo" alt="">
                                                    @else
                                                        <div class="twwda-avatar twwda-avatar-muted">
                                                            {{ strtoupper(substr($member->names ?? 'N', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <span class="fw-semibold">{{ $member->names }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $member->designation ?? '—' }}</td>
                                            <td>
                                                @if($partialDecl)
                                                    @if($partialDecl->otp_verified_at)
                                                        <span class="twwda-status-badge twwda-status-info">
                                                            <i class="mdi mdi-shield-check me-1"></i>OTP Verified
                                                        </span>
                                                    @else
                                                        <span class="twwda-status-badge twwda-status-warning">
                                                            <i class="mdi mdi-email-outline me-1"></i>OTP Sent
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="twwda-status-badge twwda-status-pending">
                                                        <i class="mdi mdi-minus-circle-outline me-1"></i>Not Started
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$partialDecl)
                                                    <a href="{{ route('workflow.meeting.conflict', ['meeting' => $meeting, 'board_member_id' => $member->id]) }}"
                                                       class="btn btn-sm twwda-btn-outline">
                                                        <i class="mdi mdi-pencil-outline me-1"></i> Declare
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($completedDeclarations->count() === 0 && $pendingMembers->count() === 0)
                        <div class="twwda-empty-state">
                            <i class="mdi mdi-account-group-outline"></i>
                            <h6>No Board Members</h6>
                            <p>No board members are assigned to this committee.</p>
                        </div>
                    @endif
                </div>

                {{-- ═══════════════════════════════════════════════════
                     TAB 2: CONFLICT OF INTEREST REGISTER
                     Full conflict details for all signed declarations
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-conflict" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="twwda-section-title mb-1">Conflict of Interest Register</h5>
                            <p class="twwda-section-subtitle mb-0">Declarations signed by board members during this meeting</p>
                        </div>
                        @if($completedDeclarations->count() > 0)
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('workflow.meeting.export.conflict.pdf', $meeting) }}"
                                   class="btn btn-sm twwda-btn-outline" target="_blank">
                                    <i class="mdi mdi-file-pdf-box me-1"></i> PDF
                                </a>
                                <a href="{{ route('workflow.meeting.export.conflict.csv', $meeting) }}"
                                   class="btn btn-sm twwda-btn-outline-success">
                                    <i class="mdi mdi-microsoft-excel me-1"></i> Excel
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Stats Cards --}}
                    @php
                        $signedCount = $completedDeclarations->count();
                        $conflictYes = $completedDeclarations->where('has_conflict', true)->count();
                        $conflictNo = $completedDeclarations->where('has_conflict', false)->count();
                    @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="twwda-stat-card">
                                <div class="twwda-stat-number">{{ $signedCount }}</div>
                                <div class="twwda-stat-label">Total Signed</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="twwda-stat-card twwda-stat-danger">
                                <div class="twwda-stat-number">{{ $conflictYes }}</div>
                                <div class="twwda-stat-label">With Conflict</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="twwda-stat-card twwda-stat-success">
                                <div class="twwda-stat-number">{{ $conflictNo }}</div>
                                <div class="twwda-stat-label">No Conflict</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="twwda-stat-card twwda-stat-warning">
                                <div class="twwda-stat-number">{{ $pendingMembers->count() }}</div>
                                <div class="twwda-stat-label">Pending</div>
                            </div>
                        </div>
                    </div>

                    {{-- Signed Declarations Table --}}
                    @if($completedDeclarations->count() > 0)
                        <div class="twwda-table-wrapper">
                            <table class="table twwda-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Board Member</th>
                                        <th class="text-center">Has Conflict?</th>
                                        <th>Person / Entity</th>
                                        <th>Nature of Interest</th>
                                        <th>Value (KES)</th>
                                        <th>Remarks</th>
                                        <th>Signed At</th>
                                        <th width="80" class="text-center">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedDeclarations->sortBy(fn($d) => optional($d->boardMember)->names) as $i => $decl)
                                        <tr>
                                            <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="twwda-avatar">
                                                        {{ strtoupper(substr(optional($decl->boardMember)->names ?? 'N', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold d-block">{{ optional($decl->boardMember)->names ?? '—' }}</span>
                                                        <small class="text-muted">{{ optional($decl->boardMember)->designation ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($decl->has_conflict)
                                                    <span class="twwda-status-badge twwda-status-danger">
                                                        <i class="mdi mdi-alert me-1"></i>Yes
                                                    </span>
                                                @else
                                                    <span class="twwda-status-badge twwda-status-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>No
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($decl->has_conflict && $decl->person_name)
                                                    {{ $decl->person_name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($decl->has_conflict && $decl->interest_nature)
                                                    {{ Str::limit($decl->interest_nature, 50) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($decl->has_conflict && $decl->interest_value)
                                                    {{ $decl->interest_value }}
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($decl->remarks ?? '—', 40) }}</td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($decl->signed_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($decl->signed_at)->format('h:i A') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm twwda-btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#conflictDetail-{{ $decl->id }}">
                                                    <i class="mdi mdi-eye-outline"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary Footer --}}
                        <div class="twwda-table-footer mt-0">
                            <div class="d-flex align-items-center gap-4">
                                <span>
                                    <strong>{{ $signedCount }}</strong>
                                    <span class="text-muted">Signed</span>
                                </span>
                                <span>
                                    <strong>{{ $conflictYes }}</strong>
                                    <span class="text-muted">With Conflict</span>
                                </span>
                                <span>
                                    <strong>{{ $conflictNo }}</strong>
                                    <span class="text-muted">No Conflict</span>
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="twwda-empty-state">
                            <i class="mdi mdi-shield-check-outline"></i>
                            <h6>No Signed Declarations Yet</h6>
                            <p>Conflict of interest declarations will appear here once board members complete the signing process.</p>
                        </div>
                    @endif
                </div>

                {{-- ═══════════════════════════════════════════════════
                     TAB 3: OTHER ATTENDEES
                     Staff, invitees, support personnel — manual entry
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-other-attendees" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="twwda-section-title mb-1">Other Attendees</h5>
                            <p class="twwda-section-subtitle mb-0">Staff, invitees and support personnel in attendance</p>
                        </div>
                        <button class="btn twwda-btn-outline" data-bs-toggle="collapse" data-bs-target="#addAttendeeForm">
                            <i class="mdi mdi-account-plus-outline me-1"></i> Add Attendee
                        </button>
                    </div>

                    {{-- Add Attendee Form --}}
                    <div class="collapse mb-4" id="addAttendeeForm">
                        <div class="twwda-form-panel">
                            <h6 class="twwda-form-panel-title mb-3">
                                <i class="mdi mdi-account-plus-outline me-1"></i> New Attendee
                            </h6>
                            <form action="{{ route('workflow.meeting.attendance', $meeting) }}" method="POST">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="twwda-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="attendee_name" class="form-control twwda-input" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="twwda-label">Title / Designation</label>
                                        <input type="text" name="attendee_designation" class="form-control twwda-input" placeholder="e.g. Finance Manager">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="twwda-label">Attendee Type</label>
                                        <select name="attendance_type" class="form-select twwda-input">
                                            <option value="staff">Staff</option>
                                            <option value="external">External / Invitee</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn twwda-btn-primary w-100">
                                            <i class="mdi mdi-check me-1"></i> Add Attendee
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Other Attendees Table --}}
                    @if($otherAttendees->count() > 0)
                        <div class="twwda-table-wrapper">
                            <table class="table twwda-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Name</th>
                                        <th>Title / Designation</th>
                                        <th>Type</th>
                                        <th>Time Joined</th>
                                        <th width="130" class="text-center">Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otherAttendees as $i => $attendee)
                                        <tr>
                                            <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="twwda-avatar {{ $attendee->attendance_type === 'external' ? 'twwda-avatar-amber' : 'twwda-avatar-teal' }}">
                                                        {{ strtoupper(substr($attendee->attendee_name ?? 'N', 0, 2)) }}
                                                    </div>
                                                    <span class="fw-semibold">{{ $attendee->attendee_name ?? $attendee->display_name ?? '—' }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $attendee->attendee_designation ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $typeLabels = ['staff' => 'Staff', 'external' => 'External / Invitee'];
                                                    $typeColors = ['staff' => 'twwda-badge-teal', 'external' => 'twwda-badge-amber'];
                                                @endphp
                                                <span class="twwda-type-badge {{ $typeColors[$attendee->attendance_type] ?? '' }}">
                                                    {{ $typeLabels[$attendee->attendance_type] ?? ucfirst($attendee->attendance_type ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($attendee->joined_at)
                                                    {{ \Carbon\Carbon::parse($attendee->joined_at)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center mb-0">
                                                    <input class="form-check-input attendance-toggle" type="checkbox"
                                                           data-attendee-id="{{ $attendee->id }}"
                                                           {{ $attendee->is_present ? 'checked' : '' }}
                                                           style="width: 3.2em; height: 1.6em; cursor: pointer;">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary Footer --}}
                        <div class="twwda-table-footer">
                            <div class="d-flex align-items-center gap-4">
                                <span>
                                    <strong class="attendee-present-count">{{ $otherAttendees->where('is_present', true)->count() }}</strong>
                                    <span class="text-muted">Present</span>
                                </span>
                                <span>
                                    <strong class="attendee-absent-count">{{ $otherAttendees->where('is_present', false)->count() }}</strong>
                                    <span class="text-muted">Absent</span>
                                </span>
                                <span>
                                    <strong>{{ $otherAttendees->count() }}</strong>
                                    <span class="text-muted">Total</span>
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="twwda-empty-state">
                            <i class="mdi mdi-account-group-outline"></i>
                            <h6>No Other Attendees</h6>
                            <p>Use the button above to add staff, invitees, or support personnel to the attendance register.</p>
                        </div>
                    @endif
                </div>

                {{-- ═══════════════════════════════════════════════════
                     TAB 4: BOARD PAPERS
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-papers" role="tabpanel">
                    <div class="mb-4">
                        <h5 class="twwda-section-title mb-1">Board Papers</h5>
                        <p class="twwda-section-subtitle mb-0">Papers scheduled for discussion in this meeting</p>
                    </div>

                    @if($meeting->boardPapers && $meeting->boardPapers->count() > 0)
                        <div class="twwda-papers-list">
                            @foreach($meeting->boardPapers->sortBy('display_order') as $paper)
                                <div class="twwda-paper-item">
                                    <div class="twwda-paper-number">
                                        {{ $paper->display_order ?? $loop->iteration }}
                                    </div>
                                    <div class="twwda-paper-content">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="twwda-paper-ref">{{ $paper->paper_number }}</span>
                                            @if($paper->agenda_item_number)
                                                <span class="twwda-paper-agenda">Agenda {{ $paper->agenda_item_number }}</span>
                                            @endif
                                        </div>
                                        <h6 class="twwda-paper-title">{{ $paper->title }}</h6>
                                        <span class="twwda-paper-author">
                                            <i class="mdi mdi-account-outline me-1"></i>{{ $paper->author->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="twwda-paper-actions">
                                        @php
                                            $paperStatusMap = [
                                                'ceo_approved' => ['label' => 'CEO Approved', 'class' => 'twwda-status-success'],
                                                'approved' => ['label' => 'Approved', 'class' => 'twwda-status-success'],
                                                'director_approved' => ['label' => 'Director Approved', 'class' => 'twwda-status-info'],
                                                'submitted' => ['label' => 'Submitted', 'class' => 'twwda-status-info'],
                                                'draft' => ['label' => 'Draft', 'class' => 'twwda-status-pending'],
                                            ];
                                            $ps = $paperStatusMap[$paper->status] ?? ['label' => ucwords(str_replace('_', ' ', $paper->status)), 'class' => 'twwda-status-warning'];
                                        @endphp
                                        <span class="twwda-status-badge {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                                        <a href="{{ route('workflow.papers.show', $paper) }}" target="_blank" class="btn btn-sm twwda-btn-ghost ms-2">
                                            <i class="mdi mdi-open-in-new"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="twwda-empty-state">
                            <i class="mdi mdi-file-document-off-outline"></i>
                            <h6>No Board Papers</h6>
                            <p>No board papers have been added for this meeting yet.</p>
                        </div>
                    @endif
                </div>

                {{-- ═══════════════════════════════════════════════════
                     TAB 6: MINUTES CAPTURE
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-minutes" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="twwda-section-title mb-1">Meeting Minutes</h5>
                            <p class="twwda-section-subtitle mb-0">Capture minutes aligned with the meeting agenda items</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span id="minutes-autosave-status" class="d-none" style="font-size:14px; font-weight:600;"></span>
                            @if($meetingMinutes)
                                <a href="{{ route('workflow.meeting.export.minutes.word', $meeting) }}"
                                   class="btn btn-sm twwda-btn-outline">
                                    <i class="mdi mdi-microsoft-word me-1"></i> Download Word
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Auto-populated attendance summary --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="twwda-stat-card twwda-stat-success">
                                <div class="twwda-stat-number">{{ $completedDeclarations->count() }}</div>
                                <div class="twwda-stat-label">Present</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="twwda-stat-card twwda-stat-warning">
                                <div class="twwda-stat-number">{{ $pendingMembers->count() }}</div>
                                <div class="twwda-stat-label">Absent (Apology)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="twwda-stat-card twwda-stat-info">
                                <div class="twwda-stat-number">{{ $otherAttendees->where('is_present', true)->count() }}</div>
                                <div class="twwda-stat-label">In Attendance</div>
                            </div>
                        </div>
                    </div>

                    {{-- Attendance Detail Accordion --}}
                    <div class="accordion mb-4" id="minutesAttendanceAccordion">
                        <div class="accordion-item" style="border: 1px solid #e9ecef; border-radius: 10px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#minutesAttendanceBody" style="font-size: 15px; font-weight: 600;">
                                    <i class="mdi mdi-account-group-outline me-2"></i> View Auto-populated Attendance
                                </button>
                            </h2>
                            <div id="minutesAttendanceBody" class="accordion-collapse collapse" data-bs-parent="#minutesAttendanceAccordion">
                                <div class="accordion-body" style="padding: 20px;">
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <h6 class="twwda-subsection-title text-success mb-2">
                                                <i class="mdi mdi-check-circle me-1"></i> Members Present
                                            </h6>
                                            @forelse($completedDeclarations->sortBy(fn($d) => optional($d->boardMember)->names) as $decl)
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="twwda-avatar" style="width:28px;height:28px;min-width:28px;font-size:10px;border-radius:7px;">
                                                        {{ strtoupper(substr(optional($decl->boardMember)->names ?? 'N', 0, 2)) }}
                                                    </div>
                                                    <div style="font-size:14px;">
                                                        <span class="fw-semibold">{{ optional($decl->boardMember)->names ?? '—' }}</span>
                                                        @if(optional($decl->boardMember)->designation)
                                                            <span class="text-muted"> — {{ $decl->boardMember->designation }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0" style="font-size:14px;">No members present yet</p>
                                            @endforelse
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="twwda-subsection-title text-warning mb-2">
                                                <i class="mdi mdi-clock-outline me-1"></i> Absent with Apology
                                            </h6>
                                            @forelse($pendingMembers as $member)
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="twwda-avatar twwda-avatar-muted" style="width:28px;height:28px;min-width:28px;font-size:10px;border-radius:7px;">
                                                        {{ strtoupper(substr($member->names ?? 'N', 0, 2)) }}
                                                    </div>
                                                    <div style="font-size:14px;">
                                                        <span class="fw-semibold">{{ $member->names }}</span>
                                                        @if($member->designation)
                                                            <span class="text-muted"> — {{ $member->designation }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0" style="font-size:14px;">All members present</p>
                                            @endforelse
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="twwda-subsection-title text-info mb-2">
                                                <i class="mdi mdi-account-supervisor me-1"></i> In Attendance
                                            </h6>
                                            @forelse($otherAttendees->where('is_present', true) as $attendee)
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="twwda-avatar twwda-avatar-teal" style="width:28px;height:28px;min-width:28px;font-size:10px;border-radius:7px;">
                                                        {{ strtoupper(substr($attendee->attendee_name ?? 'N', 0, 2)) }}
                                                    </div>
                                                    <div style="font-size:14px;">
                                                        <span class="fw-semibold">{{ $attendee->attendee_name ?? '—' }}</span>
                                                        @if($attendee->attendee_designation)
                                                            <span class="text-muted"> — {{ $attendee->attendee_designation }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0" style="font-size:14px;">No other attendees recorded</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Minutes Capture Form --}}
                    <form action="{{ route('workflow.meeting.minutes.save', $meeting) }}" method="POST" id="minutesForm">
                        @csrf

                        {{-- Minutes Title --}}
                        <div class="twwda-form-panel mb-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="twwda-label">Minutes Title <span class="text-danger">*</span></label>
                                    <input type="text" name="minutes_title" class="form-control twwda-input"
                                           value="{{ $meetingMinutes->minutes_title ?? 'Minutes of ' . $meeting->title }}"
                                           placeholder="e.g. Minutes of the 1st Finance Committee Meeting" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="twwda-label">In Attendance (Others)</label>
                                    <input type="text" name="in_attendance" class="form-control twwda-input"
                                           value="{{ $meetingMinutes->in_attendance ?? $otherAttendees->where('is_present', true)->pluck('attendee_name')->join(', ') }}"
                                           placeholder="e.g. Jane Doe (Finance), John Smith (Legal)">
                                </div>
                            </div>
                        </div>

                        {{-- Minute Items — one per agenda item --}}
                        <h6 class="twwda-subsection-title mb-3">
                            <i class="mdi mdi-format-list-numbered me-1"></i> Minute Items
                        </h6>

                        <div id="minute-items-container">
                            @php
                                $existingItems = $meetingMinutes ? $meetingMinutes->items->keyBy('agenda_item_id') : collect();
                            @endphp

                            @foreach($agendaItems as $index => $agenda)
                                @php
                                    $savedItem = $existingItems->get($agenda->id);
                                @endphp
                                <div class="twwda-minute-item-card mb-3" data-index="{{ $index }}">
                                    <div class="twwda-minute-item-header" data-bs-toggle="collapse"
                                         data-bs-target="#minuteItem-{{ $index }}" style="cursor: pointer;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="twwda-minute-number-badge">{{ $agenda->item_number ?? ($index + 1) }}</span>
                                            <div>
                                                <span class="fw-semibold" style="font-size: 16px;">{{ $agenda->title }}</span>
                                                @if($savedItem)
                                                    <span class="twwda-status-badge twwda-status-success ms-2" style="font-size:12px;padding:3px 10px;">
                                                        <i class="mdi mdi-check me-1"></i>Captured
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-down twwda-collapse-icon" style="font-size:22px;color:#6b7280;"></i>
                                    </div>

                                    <div class="collapse {{ $savedItem ? '' : ($index === 0 ? 'show' : '') }}" id="minuteItem-{{ $index }}">
                                        <div class="twwda-minute-item-body">
                                            <input type="hidden" name="items[{{ $index }}][agenda_item_id]" value="{{ $agenda->id }}">
                                            <input type="hidden" name="items[{{ $index }}][agenda_title]" value="{{ $agenda->title }}">

                                            <div class="row g-3">
                                                <div class="col-md-2">
                                                    <label class="twwda-label">Minute No.</label>
                                                    <input type="text" name="items[{{ $index }}][minute_number]"
                                                           class="form-control twwda-input"
                                                           value="{{ $savedItem->minute_number ?? 'MIN/' . ($index + 1) }}"
                                                           placeholder="MIN/1">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="twwda-label">Discussion</label>
                                                    <textarea name="items[{{ $index }}][discussion]"
                                                              class="form-control twwda-input" rows="3"
                                                              placeholder="Summary of discussion on this agenda item...">{{ $savedItem->discussion ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="twwda-label">Comments & Recommendations</label>
                                                    <textarea name="items[{{ $index }}][comments]"
                                                              class="form-control twwda-input" rows="3"
                                                              placeholder="Board comments and recommendations...">{{ $savedItem->comments ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="twwda-label">Resolution</label>
                                                    <textarea name="items[{{ $index }}][resolution]"
                                                              class="form-control twwda-input" rows="2"
                                                              placeholder="Board resolution on this item...">{{ $savedItem->resolution ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="twwda-label">Proposed By</label>
                                                    <select name="items[{{ $index }}][proposer]" class="form-select twwda-input">
                                                        <option value="">— Select —</option>
                                                        @foreach($completedDeclarations->sortBy(fn($d) => optional($d->boardMember)->names) as $decl)
                                                            <option value="{{ optional($decl->boardMember)->names }}"
                                                                {{ ($savedItem->proposer ?? '') === optional($decl->boardMember)->names ? 'selected' : '' }}>
                                                                {{ optional($decl->boardMember)->names ?? '—' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="twwda-label">Seconded By</label>
                                                    <select name="items[{{ $index }}][seconder]" class="form-select twwda-input">
                                                        <option value="">— Select —</option>
                                                        @foreach($completedDeclarations->sortBy(fn($d) => optional($d->boardMember)->names) as $decl)
                                                            <option value="{{ optional($decl->boardMember)->names }}"
                                                                {{ ($savedItem->seconder ?? '') === optional($decl->boardMember)->names ? 'selected' : '' }}>
                                                                {{ optional($decl->boardMember)->names ?? '—' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($agendaItems->count() === 0)
                            <div class="twwda-empty-state">
                                <i class="mdi mdi-file-document-off-outline"></i>
                                <h6>No Agenda Items</h6>
                                <p>Agenda items must be added before minutes can be captured.</p>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <p class="text-muted mb-0" style="font-size: 14px;">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Minutes auto-save as you type. Members present/absent are auto-populated from conflict declarations.
                                </p>
                                <button type="submit" class="btn twwda-btn-primary" id="minutesSaveBtn">
                                    <i class="mdi mdi-content-save-outline me-1"></i>
                                    {{ $meetingMinutes ? 'Update Minutes' : 'Save Minutes' }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>

                {{-- ═══════════════════════════════════════════════════
                     TAB 5: TIMELINE / AUDIT TRAIL
                     ═══════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                    <div class="mb-4">
                        <h5 class="twwda-section-title mb-1">Audit Trail</h5>
                        <p class="twwda-section-subtitle mb-0">Complete history of actions taken during this meeting</p>
                    </div>

                    @if($meeting->auditLogs && $meeting->auditLogs->count())
                        <div class="twwda-timeline">
                            @foreach($meeting->auditLogs->sortByDesc('created_at') as $log)
                                @php
                                    $logIcons = [
                                        'created' => ['icon' => 'mdi-plus-circle', 'color' => '#099139'],
                                        'updated' => ['icon' => 'mdi-pencil-circle', 'color' => '#17b84f'],
                                        'approved' => ['icon' => 'mdi-check-circle', 'color' => '#099139'],
                                        'rejected' => ['icon' => 'mdi-close-circle', 'color' => '#dc3545'],
                                        'submitted' => ['icon' => 'mdi-send-circle', 'color' => setting('primary_color', '#1a1a1a')],
                                        'assigned' => ['icon' => 'mdi-account-plus', 'color' => '#17b84f'],
                                        'notified' => ['icon' => 'mdi-bell-circle', 'color' => '#ffc107'],
                                        'signed' => ['icon' => 'mdi-draw', 'color' => '#099139'],
                                        'attendance' => ['icon' => 'mdi-account-check', 'color' => setting('primary_color', '#1a1a1a')],
                                    ];
                                    $li = $logIcons[$log->action ?? 'updated'] ?? ['icon' => 'mdi-circle', 'color' => '#6c757d'];
                                @endphp
                                <div class="twwda-timeline-item">
                                    <div class="twwda-timeline-icon" style="background: {{ $li['color'] }};">
                                        <i class="mdi {{ $li['icon'] }}"></i>
                                    </div>
                                    <div class="twwda-timeline-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="twwda-timeline-action">{{ $log->description ?? $log->action ?? 'Action' }}</span>
                                                @if($log->user)
                                                    <span class="twwda-timeline-user">by {{ $log->user->name ?? 'System' }}</span>
                                                @endif
                                            </div>
                                            <div class="twwda-timeline-time text-end">
                                                <div>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</div>
                                                <div class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        @if($log->details)
                                            <p class="twwda-timeline-details">{{ $log->details }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="twwda-empty-state">
                            <i class="mdi mdi-timeline-clock-outline"></i>
                            <h6>No Activity Yet</h6>
                            <p>The audit trail will appear here as actions are performed during the meeting.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MODALS — Conflict Detail + Signature Zoom
     ═══════════════════════════════════════════════════════════════ --}}

{{-- Conflict Detail Modals --}}
@foreach($completedDeclarations as $decl)
    <div class="modal fade" id="conflictDetail-{{ $decl->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content twwda-modal">
                <div class="modal-header {{ $decl->has_conflict ? 'twwda-modal-header-warning' : 'twwda-modal-header-success' }}">
                    <h5 class="modal-title">
                        @if($decl->has_conflict)
                            <i class="mdi mdi-shield-alert-outline me-2"></i>Conflict Declared
                        @else
                            <i class="mdi mdi-shield-check me-2"></i>No Conflict Declared
                        @endif
                        &mdash; {{ optional($decl->boardMember)->names ?? 'Board Member' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <table class="table twwda-detail-table mb-0">
                        <tr>
                            <td class="twwda-detail-label">Board Member</td>
                            <td>{{ optional($decl->boardMember)->names ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="twwda-detail-label">Designation</td>
                            <td>{{ optional($decl->boardMember)->designation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="twwda-detail-label">Has Conflict?</td>
                            <td>
                                @if($decl->has_conflict)
                                    <span class="twwda-status-badge twwda-status-danger"><i class="mdi mdi-alert me-1"></i>Yes</span>
                                @else
                                    <span class="twwda-status-badge twwda-status-success"><i class="mdi mdi-check-circle me-1"></i>No</span>
                                @endif
                            </td>
                        </tr>
                        @if($decl->has_conflict)
                            <tr>
                                <td class="twwda-detail-label">Person / Entity Name</td>
                                <td>{{ $decl->person_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="twwda-detail-label">Person Address</td>
                                <td>{{ $decl->person_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="twwda-detail-label">Nature of Interest</td>
                                <td>{{ $decl->interest_nature ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="twwda-detail-label">Value (KES)</td>
                                <td>{{ $decl->interest_value ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="twwda-detail-label">Interest Ceased On</td>
                                <td>{{ $decl->interest_ceased_on ? \Carbon\Carbon::parse($decl->interest_ceased_on)->format('d M Y') : 'N/A' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="twwda-detail-label">Remarks</td>
                            <td>{{ $decl->remarks ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="twwda-detail-label">OTP Verified</td>
                            <td>{{ $decl->otp_verified_at ? \Carbon\Carbon::parse($decl->otp_verified_at)->format('d M Y, h:i A') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="twwda-detail-label">Signed At</td>
                            <td>{{ $decl->signed_at ? \Carbon\Carbon::parse($decl->signed_at)->format('d M Y, h:i A') : '—' }}</td>
                        </tr>
                    </table>
                    @if($decl->signature)
                        <div class="twwda-signature-display mt-3">
                            <span class="twwda-detail-label d-block mb-2">Digital Signature</span>
                            <img src="{{ $decl->signature }}" alt="Signature" style="max-height: 120px;">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn twwda-btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Signature Zoom Modals --}}
@foreach($completedDeclarations->where('signature', '!=', null) as $decl)
    <div class="modal fade" id="signatureModal-{{ $decl->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content twwda-modal">
                <div class="modal-header" style="background: #f7f8fa; border-bottom: 1px solid #e9ecef; padding: 14px 20px;">
                    <h6 class="modal-title fw-bold mb-0" style="font-size: 15px;">
                        {{ optional($decl->boardMember)->names ?? 'Signature' }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="{{ $decl->signature }}" alt="Signature" style="max-width: 100%; max-height: 180px;">
                    <div class="mt-2 text-muted" style="font-size: 13px;">
                        Signed {{ \Carbon\Carbon::parse($decl->signed_at)->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════
   TWWDA LIVE MEETING — Clean Government Portal Design
   Font: 14-15px base, professional spacing, Navy/Green accent
   ══════════════════════════════════════════════════════════════════════ */

/* ── Page Header ────────────────────────────────────────────────────── */
.twwda-page-header {
    padding: 0;
}
.twwda-page-title {
    font-size: 28px;
    font-weight: 700;
    color: #0d0d0d;
    letter-spacing: -0.3px;
}
.twwda-page-subtitle {
    font-size: 16px;
    color: #4b5563;
    margin: 0;
}
.twwda-back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f0f2f5;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 20px;
}
.twwda-back-btn:hover {
    background: {{ setting('primary_color', '#1a1a1a') }};
    color: #fff;
}
.twwda-status-live {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.twwda-live-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #2e7d32;
    animation: twwda-blink 1.5s infinite;
}
@keyframes twwda-blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* ── Info Strip ─────────────────────────────────────────────────────── */
.twwda-info-strip {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid #e9ecef;
}
.twwda-info-cell {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 24px;
    border-right: 1px solid #f0f2f5;
}
.twwda-info-cell:last-child { border-right: none; }
.twwda-info-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.twwda-info-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 3px;
}
.twwda-info-value {
    display: block;
    font-size: 17px;
    font-weight: 700;
    color: #0d0d0d;
}

/* ── Card & Tabs ────────────────────────────────────────────────────── */
.twwda-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    overflow: hidden;
}
.twwda-tab-header {
    background: #fbfbfb;
    border-bottom: 1px solid #e9ecef;
    padding: 0 8px;
}
.twwda-nav-tabs {
    border: none !important;
}
.twwda-nav-tabs .nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    font-size: 16px;
    font-weight: 600;
    color: #4b5563;
    border: none !important;
    border-bottom: 3px solid transparent !important;
    background: transparent !important;
    border-radius: 0 !important;
    transition: all 0.2s;
    white-space: nowrap;
}
.twwda-nav-tabs .nav-link i {
    font-size: 20px;
}
.twwda-nav-tabs .nav-link:hover {
    color: {{ setting('primary_color', '#1a1a1a') }};
    border-bottom-color: rgba(200, 16, 46, 0.3) !important;
}
.twwda-nav-tabs .nav-link.active {
    color: {{ setting('primary_color', '#1a1a1a') }};
    font-weight: 600;
    border-bottom-color: {{ setting('primary_color', '#1a1a1a') }} !important;
    background: transparent !important;
}
.twwda-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 7px;
    border-radius: 11px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
}
.twwda-tab-body {
    padding: 28px 28px 24px;
}

/* ── Section Titles ─────────────────────────────────────────────────── */
.twwda-section-title {
    font-size: 22px;
    font-weight: 700;
    color: #0d0d0d;
}
.twwda-section-subtitle {
    font-size: 15px;
    color: #6b7280;
}
.twwda-subsection-title {
    font-size: 17px;
    font-weight: 700;
    color: #374151;
}

/* ── Tables ──────────────────────────────────────────────────────────── */
.twwda-table-wrapper {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}
.twwda-table {
    font-size: 15px;
    color: #111111;
}
.twwda-table thead th {
    background: #f7f8fa;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 15px 18px;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}
.twwda-table tbody td {
    padding: 16px 18px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f2f5;
}
.twwda-table tbody tr:last-child td {
    border-bottom: none;
}
.twwda-table tbody tr:hover {
    background: #fcfcfc;
}
.twwda-table-footer {
    padding: 16px 22px;
    background: #f7f8fa;
    border-top: 1px solid #e9ecef;
    font-size: 15px;
    border-radius: 0 0 10px 10px;
    color: #111111;
}

/* ── Avatar & Member Photo ──────────────────────────────────────────── */
.twwda-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f3f3f3, #e8e8e8);
    color: {{ setting('primary_color', '#1a1a1a') }};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
}
.twwda-avatar-muted {
    background: linear-gradient(135deg, #f0f2f5, #e5e7eb);
    color: #6b7280;
}
.twwda-avatar-teal {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}
.twwda-avatar-amber {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}
.twwda-member-photo {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #e9ecef;
}
.twwda-signature-thumb {
    max-height: 36px;
    max-width: 90px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 2px;
    background: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
}
.twwda-signature-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ── Type Badges ─────────────────────────────────────────────────────── */
.twwda-type-badge {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
}
.twwda-badge-navy {
    background: rgba(200, 16, 46, 0.08);
    color: {{ setting('primary_color', '#1a1a1a') }};
}
.twwda-badge-teal {
    background: rgba(23, 184, 79, 0.08);
    color: #0fa042;
}
.twwda-badge-amber {
    background: rgba(255, 193, 7, 0.1);
    color: #b58a00;
}

/* ── Status Badges ───────────────────────────────────────────────────── */
.twwda-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
}
.twwda-status-success {
    background: #e6f7ed;
    color: #166534;
}
.twwda-status-info {
    background: #f0f0f0;
    color: #b81926;
}
.twwda-status-warning {
    background: #fff8e1;
    color: #92400e;
}
.twwda-status-danger {
    background: #fef2f2;
    color: #b91c1c;
}
.twwda-status-pending {
    background: #f5f6f8;
    color: #4b5563;
}

/* ── Stat Cards ──────────────────────────────────────────────────────── */
.twwda-stat-card {
    text-align: center;
    padding: 20px 16px;
    border-radius: 12px;
    background: #f7f8fa;
    border: 1px solid #e9ecef;
}
.twwda-stat-card.twwda-stat-warning {
    background: #fff8e1;
    border-color: #ffe082;
}
.twwda-stat-card.twwda-stat-info {
    background: #f0f0f0;
    border-color: #f32536;
}
.twwda-stat-card.twwda-stat-success {
    background: #e6f7ed;
    border-color: #a5d6a7;
}
.twwda-stat-card.twwda-stat-danger {
    background: #fef2f2;
    border-color: #fca5a5;
}
.twwda-stat-number {
    font-size: 34px;
    font-weight: 800;
    color: #0d0d0d;
    line-height: 1;
    margin-bottom: 6px;
}
.twwda-stat-warning .twwda-stat-number { color: #92400e; }
.twwda-stat-info .twwda-stat-number { color: #b81926; }
.twwda-stat-success .twwda-stat-number { color: #166534; }
.twwda-stat-danger .twwda-stat-number { color: #b91c1c; }
.twwda-stat-label {
    font-size: 15px;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── Board Papers List ───────────────────────────────────────────────── */
.twwda-papers-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.twwda-paper-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.2s;
}
.twwda-paper-item:hover {
    border-color: #d0d5dd;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.twwda-paper-number {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}, #2b2b2b);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
}
.twwda-paper-content {
    flex: 1;
    min-width: 0;
}
.twwda-paper-ref {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 5px;
    background: #f0f0f0;
    color: {{ setting('primary_color', '#1a1a1a') }};
    font-size: 14px;
    font-weight: 600;
}
.twwda-paper-agenda {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 5px;
    background: #f5f6f8;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}
.twwda-paper-title {
    font-size: 17px;
    font-weight: 600;
    color: #0d0d0d;
    margin: 6px 0 4px;
}
.twwda-paper-author {
    font-size: 15px;
    color: #4b5563;
}
.twwda-paper-actions {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

/* ── Timeline ────────────────────────────────────────────────────────── */
.twwda-timeline {
    position: relative;
    padding-left: 36px;
}
.twwda-timeline::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.twwda-timeline-item {
    position: relative;
    display: flex;
    gap: 16px;
    padding-bottom: 24px;
}
.twwda-timeline-item:last-child { padding-bottom: 0; }
.twwda-timeline-icon {
    position: absolute;
    left: -36px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    z-index: 1;
    border: 3px solid #fff;
}
.twwda-timeline-body {
    flex: 1;
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #f0f2f5;
}
.twwda-timeline-action {
    font-size: 16px;
    font-weight: 600;
    color: #0d0d0d;
    display: block;
}
.twwda-timeline-user {
    font-size: 15px;
    color: #4b5563;
}
.twwda-timeline-time {
    font-size: 14px;
    color: #4b5563;
    white-space: nowrap;
}
.twwda-timeline-details {
    font-size: 15px;
    color: #374151;
    margin: 8px 0 0;
}

/* ── Buttons ─────────────────────────────────────────────────────────── */
.twwda-btn-primary {
    background: {{ setting('primary_color', '#1a1a1a') }};
    color: #fff;
    border: none;
    padding: 11px 22px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
.twwda-btn-primary:hover {
    background: #2b2b2b;
    color: #fff;
}
.twwda-btn-outline {
    background: #fff;
    color: {{ setting('primary_color', '#1a1a1a') }};
    border: 1.5px solid {{ setting('primary_color', '#1a1a1a') }};
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
.twwda-btn-outline:hover {
    background: {{ setting('primary_color', '#1a1a1a') }};
    color: #fff;
}
.twwda-btn-outline-info {
    background: #fff;
    color: #0e903c;
    border: 1.5px solid #17b84f;
    padding: 7px 16px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
}
.twwda-btn-outline-info:hover {
    background: #17b84f;
    color: #fff;
}
.twwda-btn-outline-success {
    background: #fff;
    color: #099139;
    border: 1.5px solid #099139;
    padding: 7px 16px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
}
.twwda-btn-outline-success:hover {
    background: #099139;
    color: #fff;
}
.twwda-btn-secondary {
    background: #f0f2f5;
    color: #111111;
    border: 1px solid #dee2e6;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
}
.twwda-btn-ghost {
    background: transparent;
    color: #374151;
    border: 1px solid #dee2e6;
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 18px;
    line-height: 1;
}
.twwda-btn-ghost:hover {
    background: #f0f2f5;
    color: {{ setting('primary_color', '#1a1a1a') }};
}

/* ── Form Elements ───────────────────────────────────────────────────── */
.twwda-form-panel {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px 24px;
}
.twwda-form-panel-title {
    font-size: 17px;
    font-weight: 600;
    color: #0d0d0d;
}
.twwda-label {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: #111111;
    margin-bottom: 6px;
}
.twwda-input {
    font-size: 16px;
    padding: 11px 14px;
    border-radius: 8px;
    border: 1.5px solid #dee2e6;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.twwda-input:focus {
    border-color: {{ setting('primary_color', '#1a1a1a') }};
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
}

/* ── Empty State ─────────────────────────────────────────────────────── */
.twwda-empty-state {
    text-align: center;
    padding: 48px 24px;
}
.twwda-empty-state i {
    font-size: 60px;
    color: #9ca3af;
    display: block;
    margin-bottom: 12px;
}
.twwda-empty-state h6 {
    font-size: 18px;
    font-weight: 600;
    color: #111111;
    margin-bottom: 6px;
}
.twwda-empty-state p {
    font-size: 16px;
    color: #4b5563;
    margin: 0;
}

/* ── Modal ───────────────────────────────────────────────────────────── */
.twwda-modal {
    border-radius: 14px;
    overflow: hidden;
    border: none;
}
.twwda-modal-header-warning {
    background: linear-gradient(135deg, #fff3cd, #ffeeba);
    border-bottom: 1px solid #ffe082;
    padding: 16px 24px;
}
.twwda-modal-header-warning .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #664d03;
}
.twwda-modal-header-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-bottom: 1px solid #86efac;
    padding: 16px 24px;
}
.twwda-modal-header-success .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #166534;
}
.twwda-detail-table {
    font-size: 16px;
}
.twwda-detail-table td {
    padding: 12px 14px;
    border-color: #f0f2f5;
}
.twwda-detail-label {
    font-weight: 600;
    color: #374151;
    width: 40%;
    font-size: 15px;
}
.twwda-signature-display {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 16px;
    text-align: center;
}

/* ── Autosave Status ─────────────────────────────────────────────────── */
.autosave-saving { color: #d4a017; }
.autosave-saved  { color: #166534; }
.autosave-error  { color: #b91c1c; }
@keyframes autosave-fade {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}
.autosave-fade-out {
    animation: autosave-fade 3s forwards;
}

/* ── Minute Item Cards ───────────────────────────────────────────────── */
.twwda-minute-item-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s;
}
.twwda-minute-item-card:hover {
    border-color: #d0d5dd;
}
.twwda-minute-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #f0f2f5;
    user-select: none;
}
.twwda-minute-item-header[aria-expanded="true"] .twwda-collapse-icon,
.twwda-minute-item-header:not(.collapsed) .twwda-collapse-icon {
    transform: rotate(180deg);
}
.twwda-collapse-icon {
    transition: transform 0.25s ease;
}
.twwda-minute-item-body {
    padding: 20px;
    background: #fff;
}
.twwda-minute-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--tw-primary, #1a1a1a), #2b2b2b);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 0 10px;
}

/* ── Responsive ──────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .twwda-info-cell {
        padding: 14px 16px;
        border-right: none;
        border-bottom: 1px solid #f0f2f5;
    }
    .twwda-tab-body { padding: 20px 16px; }
    .twwda-nav-tabs .nav-link {
        padding: 12px 12px;
        font-size: 14px;
    }
    .twwda-nav-tabs .nav-link span { display: none; }
    .twwda-nav-tabs .nav-link i { font-size: 22px; }
    .twwda-paper-item { flex-wrap: wrap; }
    .twwda-paper-actions { width: 100%; justify-content: flex-end; margin-top: 8px; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // ── Attendance toggle (present/absent) via AJAX — for Other Attendees ──
    $('.attendance-toggle').on('change', function() {
        var toggle = $(this);
        var attendeeId = toggle.data('attendee-id');
        var present = toggle.is(':checked') ? 1 : 0;

        $.ajax({
            url: '{{ route("workflow.meeting.attendance.toggle", $meeting) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { attendee_id: attendeeId, present: present },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    toastr.success(present ? 'Marked as present.' : 'Marked as absent.');
                    updateAttendeeSummary();
                } else {
                    toastr.error(data.message || 'Failed to update attendance.');
                    toggle.prop('checked', !toggle.is(':checked'));
                }
            },
            error: function() {
                toastr.error('Failed to update attendance.');
                toggle.prop('checked', !toggle.is(':checked'));
            }
        });
    });

    function updateAttendeeSummary() {
        var total = $('.attendance-toggle').length;
        var present = $('.attendance-toggle:checked').length;
        var absent = total - present;
        $('.attendee-present-count').text(present);
        $('.attendee-absent-count').text(absent);
    }

    // ── Minutes Autosave ────────────────────────────────────────────────
    var minutesForm = document.getElementById('minutesForm');
    if (minutesForm) {
        var autosaveTimer = null;
        var statusEl = document.getElementById('minutes-autosave-status');

        function showAutosaveStatus(type, text) {
            if (!statusEl) return;
            statusEl.className = '';
            statusEl.classList.add('autosave-' + type);
            statusEl.textContent = text;
            statusEl.style.opacity = '1';
            if (type === 'saved') {
                statusEl.classList.add('autosave-fade-out');
            }
        }

        function doAutosave() {
            var formData = new FormData(minutesForm);
            showAutosaveStatus('saving', 'Saving...');

            $.ajax({
                url: minutesForm.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        showAutosaveStatus('saved', 'Saved');
                    } else {
                        showAutosaveStatus('error', 'Save failed');
                    }
                },
                error: function() {
                    showAutosaveStatus('error', 'Save failed');
                }
            });
        }

        $(minutesForm).on('input change', 'input, textarea, select', function() {
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(doAutosave, 2000);
        });

        $('#minutesSaveBtn').on('click', function(e) {
            e.preventDefault();
            clearTimeout(autosaveTimer);
            doAutosave();
        });
    }
});
</script>
@endpush
