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
                            <a href="{{ route('workflow.ceo.internal-notices', ['tab' => in_array($notice->status, ['approved', 'special_approved']) ? 'approved' : 'rejected']) }}" class="btn btn-sm btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Internal Notice Details</h4>
                        </div>
                        <p class="text-muted mb-0 ms-5">
                            {{ $notice->meeting->title ?? '' }} &middot; {{ $notice->reference_number ?? '' }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($notice->meeting)
                            @include('workflow.components.countdown-timer', ['meetingDate' => $notice->meeting->meeting_date])
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @php
            $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAuditCommittee = $notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds);
            $approverRoleLabel = $isAuditCommittee ? 'DD Internal Audit' : 'CEO';
            $approverRoleFull = $isAuditCommittee ? 'Deputy Director Internal Audit' : setting('signatory_title_ceo', 'Chief Executive Officer');
        @endphp

        {{-- Status Banner --}}
        @if(in_array($notice->status, ['approved', 'special_approved']))
            <div class="alert mb-4" style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); border: 1px solid #c8e6c9; border-radius: 8px;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                            <i class="mdi mdi-check-decagram"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0" style="color: #2e7d32;">
                            {{ $notice->status === 'special_approved' ? 'Special Approved' : 'Approved' }}
                        </h6>
                        <small style="color: #558b2f;">
                            {{ $notice->approver->name ?? $approverRoleLabel }} &middot;
                            {{ $notice->approved_at ? $notice->approved_at->format('d F Y \a\t H:i') : '-' }}
                            @if($notice->status === 'special_approved' && $notice->special_approval_reason)
                                &middot; <em>{{ Str::limit($notice->special_approval_reason, 80) }}</em>
                            @endif
                        </small>
                    </div>
                    <div class="flex-shrink-0">
                        @if($notice->meeting)
                            <a href="{{ route('workflow.internal-notice.preview', $notice->id) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="mdi mdi-printer-outline"></i> Print
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($notice->status === 'rejected')
            <div class="alert mb-4" style="background: linear-gradient(135deg, #fce4ec 0%, #fff3e0 100%); border: 1px solid #ffcdd2; border-radius: 8px;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                            <i class="mdi mdi-close-circle"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0" style="color: #c62828;">Rejected</h6>
                        <small style="color: #bf360c;">
                            Notice was sent back for changes &middot; Version {{ $notice->version ?? 1 }}
                        </small>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Notice Document --}}
            <div class="col-xl-8 col-lg-7 mb-3">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="mdi mdi-file-document-outline me-1"></i> Notice Document</h6>
                        @if($notice->meeting)
                            <a href="{{ route('workflow.internal-notice.preview', $notice->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                <i class="mdi mdi-printer-outline"></i> Print Preview
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- Letterhead --}}
                        @include('workflow.components.letterhead')

                        {{-- Reference & Date --}}
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <p class="mb-1" style="font-size: 13px;">
                                    <strong>Ref:</strong> {{ $notice->reference_number ?? '-' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="mb-1" style="font-size: 13px;">
                                    <strong>Date:</strong> {{ $notice->reference_date ? \Carbon\Carbon::parse($notice->reference_date)->format('d F Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="text-center mb-4">
                            <p class="mb-0" style="font-size: 14px;"><strong>RE: {{ $notice->subject ?? '-' }}</strong></p>
                        </div>

                        {{-- Body --}}
                        <div class="mb-4 p-3 rounded" style="background: #fbfbfb; border: 1px solid var(--border-color); line-height: 1.7; font-size: 13px;">
                            {!! $notice->body !!}
                        </div>

                        {{-- Agenda Items --}}
                        @php
                            $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : json_decode($notice->agenda_items, true);
                        @endphp
                        @if($agendaItems && count($agendaItems) > 0)
                            <div class="mb-4">
                                <h6 class="mb-3" style="font-size: 14px; font-weight: 600;">Agenda</h6>
                                <ol class="ps-3" style="font-size: 13px; line-height: 1.8;">
                                    @foreach($agendaItems as $item)
                                        <li class="mb-1">{{ $item }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        {{-- Meeting Details --}}
                        <div class="p-3 rounded mb-0" style="background: var(--twwda-navy-light); font-size: 13px;">
                            <div class="row">
                                <div class="col-md-4">
                                    <i class="mdi mdi-map-marker-outline me-1 text-twwda-navy"></i>
                                    <strong>Venue:</strong> {{ $notice->meeting_venue ?? $notice->meeting->venue ?? '-' }}
                                </div>
                                <div class="col-md-4">
                                    <i class="mdi mdi-calendar-outline me-1 text-twwda-navy"></i>
                                    <strong>Date:</strong> {{ $notice->meeting_date ? \Carbon\Carbon::parse($notice->meeting_date)->format('d F Y') : '-' }}
                                </div>
                                <div class="col-md-4">
                                    <i class="mdi mdi-clock-outline me-1 text-twwda-navy"></i>
                                    <strong>Time:</strong> {{ $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('h:i A') : '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Approver Signature (if approved) --}}
                        @if(in_array($notice->status, ['approved', 'special_approved']))
                            <div class="separator"></div>
                            <div class="mt-3">
                                @if($notice->ceo_signature)
                                    <div class="mb-2">
                                        <img src="{{ $notice->ceo_signature }}" alt="Approver Signature"
                                             style="max-height: 60px; max-width: 200px;">
                                    </div>
                                @else
                                    <div style="height: 50px; border-bottom: 1px solid #ccc; width: 200px;"></div>
                                @endif
                                <p class="mb-0 mt-2" style="font-size: 13px; font-weight: bold;">
                                    {{ $notice->approver->name ?? $approverRoleFull }}
                                </p>
                                <p class="mb-0" style="font-size: 12px; color: #666;">
                                    {{ $notice->status === 'special_approved' ? 'Special Approval' : $approverRoleFull }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Review History --}}
                @if($notice->comments && $notice->comments->count() > 0)
                    <div class="card mb-0">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="mdi mdi-comment-multiple-outline me-1"></i> Review History</h6>
                        </div>
                        <div class="card-body">
                            @foreach($notice->comments->sortByDesc('created_at') as $comment)
                                <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white
                                            {{ $comment->action === 'rejected' ? 'bg-danger' : ($comment->action === 'resubmitted' ? 'bg-info' : 'bg-secondary') }}"
                                            style="width: 36px; height: 36px; font-size: 15px;">
                                            @if($comment->action === 'rejected')
                                                <i class="mdi mdi-close"></i>
                                            @elseif($comment->action === 'resubmitted')
                                                <i class="mdi mdi-refresh"></i>
                                            @else
                                                <i class="mdi mdi-comment-text-outline"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-semibold" style="font-size: 13px;">{{ $comment->user->name ?? 'System' }}</span>
                                            <span class="badge {{ $comment->action === 'rejected' ? 'bg-danger' : ($comment->action === 'resubmitted' ? 'bg-info' : 'bg-secondary') }}"
                                                  style="font-size: 10px;">
                                                {{ ucfirst($comment->action ?? 'Comment') }}
                                            </span>
                                            @if($comment->iteration)
                                                <small class="text-muted">v{{ $comment->iteration }}</small>
                                            @endif
                                        </div>
                                        <p class="mb-1" style="font-size: 13px; color: var(--text-body);">{{ $comment->comment }}</p>
                                        <small class="text-muted">{{ $comment->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Sidebar --}}
            <div class="col-xl-4 col-lg-5 mb-3">

                {{-- Approval Details --}}
                @if(in_array($notice->status, ['approved', 'special_approved']))
                    <div class="card mb-3">
                        <div class="card-header" style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);">
                            <h6 class="mb-0" style="color: #2e7d32;">
                                <i class="mdi mdi-check-decagram me-1"></i> Approval Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-success" style="font-size: 12px;">
                                    <i class="mdi mdi-check me-1"></i>
                                    {{ $notice->status === 'special_approved' ? 'Special Approved' : 'Approved' }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Approved By</small>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 30px; height: 30px; font-size: 11px; font-weight: 600;">
                                        {{ strtoupper(substr($notice->approver->name ?? $approverRoleLabel, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold d-block" style="font-size: 13px;">{{ $notice->approver->name ?? $approverRoleLabel }}</span>
                                        <small class="text-muted">{{ $approverRoleFull }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Approved On</small>
                                <span class="fw-semibold" style="font-size: 13px;">
                                    {{ $notice->approved_at ? $notice->approved_at->format('d F Y') : '-' }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $notice->approved_at ? $notice->approved_at->format('h:i A') : '' }}
                                </small>
                            </div>
                            @if($notice->status === 'special_approved' && $notice->special_approval_reason)
                                <div class="mb-0">
                                    <small class="text-muted d-block">Special Approval Reason</small>
                                    <p class="mb-0 mt-1 p-2 rounded" style="font-size: 13px; background: #f8f9fa; border: 1px solid #e9ecef;">
                                        {{ $notice->special_approval_reason }}
                                    </p>
                                </div>
                            @endif
                            <div class="mb-0">
                                <small class="text-muted d-block">Version</small>
                                <span style="font-size: 13px;">v{{ $notice->version ?? 1 }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($notice->status === 'rejected')
                    <div class="card mb-3">
                        <div class="card-header" style="background: linear-gradient(135deg, #fce4ec 0%, #fff3e0 100%);">
                            <h6 class="mb-0" style="color: #c62828;">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Rejection Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-danger" style="font-size: 12px;">
                                    <i class="mdi mdi-close me-1"></i> Rejected
                                </span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Last Updated</small>
                                <span class="fw-semibold" style="font-size: 13px;">
                                    {{ $notice->updated_at->format('d F Y, h:i A') }}
                                </span>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted d-block">Version</small>
                                <span style="font-size: 13px;">v{{ $notice->version ?? 1 }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Submission Info --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-information-outline me-1"></i> Submission Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-twwda-navy text-white d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                                {{ strtoupper(substr($notice->creator->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <span class="fw-semibold d-block">{{ $notice->creator->name ?? '-' }}</span>
                                <small class="text-muted">{{ $notice->creator?->roles?->pluck('name')->implode(', ') ?? '' }}</small>
                            </div>
                        </div>
                        <div class="separator"></div>
                        <div class="mb-2">
                            <small class="text-muted">Submitted On</small>
                            <p class="mb-0 fw-semibold" style="font-size: 13px;">
                                {{ $notice->submitted_at ? $notice->submitted_at->format('d F Y, H:i') : $notice->updated_at->format('d F Y, H:i') }}
                            </p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Committee</small>
                            <p class="mb-0 fw-semibold" style="font-size: 13px;">
                                {{ $notice->meeting->boardCommittee->name ?? '-' }}
                            </p>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Meeting</small>
                            <p class="mb-0">
                                <a href="{{ $notice->meeting ? route('workflow.meetings.show', $notice->meeting_id) : '#' }}"
                                   class="fw-semibold" style="font-size: 13px; color: {{ setting('primary_color', '#1a1a1a') }};">
                                    {{ $notice->meeting->title ?? '-' }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card mb-0">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-flash-outline me-1"></i> Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('workflow.internal-notice.preview', $notice->id) }}" class="btn btn-secondary w-100 mb-2" target="_blank">
                            <i class="mdi mdi-printer-outline"></i> Print Notice
                        </a>
                        @if($notice->meeting)
                            <a href="{{ route('workflow.meetings.show', $notice->meeting_id) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="mdi mdi-eye-outline"></i> View Meeting
                            </a>
                        @endif
                        <a href="{{ route('workflow.ceo.internal-notices', ['tab' => in_array($notice->status, ['approved', 'special_approved']) ? 'approved' : 'rejected']) }}" class="btn btn-outline-secondary w-100">
                            <i class="mdi mdi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
