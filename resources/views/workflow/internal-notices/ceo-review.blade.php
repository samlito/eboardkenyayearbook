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
                            <a href="{{ route('workflow.ceo.internal-notices') }}" class="btn btn-sm btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Review Internal Notice</h4>
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

        {{-- Stage Indicator --}}
        @php
            $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAuditCommittee = $notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds);
            $approverRoleLabel = $isAuditCommittee ? 'DD Internal Audit' : 'CEO';
            $isDDIA = auth()->user()->hasRole('DD Internal Audit') && !auth()->user()->hasAnyRole(['CEO', 'Superadmin']);
        @endphp
        @include('workflow.components.stage-indicator', ['currentStage' => 3, 'stageLabel' => $approverRoleLabel . ' Notice Approval', 'meeting' => $notice->meeting ?? null])

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Notice Document Preview --}}
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
                    </div>
                </div>

                {{-- Comment/Iteration History --}}
                @if($notice->comments && $notice->comments->count() > 0)
                    <div class="card mb-0">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="mdi mdi-comment-multiple-outline me-1"></i> Review History</h6>
                        </div>
                        <div class="card-body">
                            <div class="twwda-comment-timeline">
                                @foreach($notice->comments->sortByDesc('created_at') as $comment)
                                    <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white
                                                {{ $comment->action === 'approval' ? 'bg-success' : ($comment->action === 'rejection' ? 'bg-danger' : 'bg-info') }}"
                                                style="width: 36px; height: 36px; font-size: 15px;">
                                                @if($comment->action === 'approval')
                                                    <i class="mdi mdi-check"></i>
                                                @elseif($comment->action === 'rejection')
                                                    <i class="mdi mdi-close"></i>
                                                @else
                                                    <i class="mdi mdi-comment-text-outline"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="fw-semibold" style="font-size: 13px;">{{ $comment->user->name ?? 'System' }}</span>
                                                <span class="badge {{ $comment->action === 'approval' ? 'bg-success' : ($comment->action === 'rejection' ? 'bg-danger' : 'bg-info') }}"
                                                      style="font-size: 10px;">
                                                    {{ ucfirst($comment->action ?? 'Comment') }}
                                                </span>
                                            </div>
                                            <p class="mb-1" style="font-size: 13px; color: var(--text-body);">{{ $comment->comment }}</p>
                                            <small class="text-muted">{{ $comment->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Sidebar: Actions --}}
            <div class="col-xl-4 col-lg-5 mb-3">
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
                                {{ $notice->submitted_at ? \Carbon\Carbon::parse($notice->submitted_at)->format('d F Y, H:i') : $notice->updated_at->format('d F Y, H:i') }}
                            </p>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge bg-warning">Pending Approval</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-gavel me-1"></i> Decision</h6>
                    </div>
                    <div class="card-body">
                        {{-- Approve Button --}}
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="mdi mdi-check-decagram-outline"></i> Approve Notice
                        </button>

                        {{-- Reject Button --}}
                        <button type="button" class="btn btn-danger w-100 mb-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="mdi mdi-close-circle-outline"></i> Reject / Request Changes
                        </button>

                        @if($isDDIA && $isAuditCommittee)
                            <div class="alert alert-info mb-0" style="font-size: 12px;">
                                <i class="mdi mdi-information-outline me-1"></i>
                                You are approving this notice as DD Internal Audit for the Audit Committee.
                            </div>
                        @else
                            <div class="separator"></div>

                            {{-- Special Approve (CS only — not shown for audit committee notices where DDIA is the direct approver) --}}
                            @if(!$isAuditCommittee)
                                <p class="text-muted mb-2" style="font-size: 12px;">
                                    <i class="mdi mdi-shield-star-outline me-1"></i>
                                    Special approval when CEO is unavailable
                                </p>
                                <button type="button" class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#specialApproveModal">
                                    <i class="mdi mdi-shield-check-outline"></i> Special Approve (CS)
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--twwda-green-light);">
                <h6 class="modal-title" id="approveModalLabel">
                    <i class="mdi mdi-check-decagram-outline me-1 text-twwda-green"></i> Approve Internal Notice
                    @if($isAuditCommittee)
                        <small class="text-muted">(as {{ $approverRoleLabel }})</small>
                    @endif
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.ceo.internal-notice.decision', $notice->id) }}" method="POST">
                @csrf
                <input type="hidden" name="decision" value="approve">
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size: 13px;">
                        Are you sure you want to approve this internal notice?
                        This will advance the meeting workflow to the next stage.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Comments (Optional)</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Add optional approval comments..."></textarea>
                    </div>

                    {{-- Signature Pad — only for CEO, not for DDIA --}}
                    @if(!$isAuditCommittee)
                        @include('workflow.components.signature-pad', ['inputName' => 'ceo_signature', 'label' => 'CEO Signature (Optional)'])
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #fff5f8;">
                <h6 class="modal-title" id="rejectModalLabel">
                    <i class="mdi mdi-close-circle-outline me-1 text-danger"></i> Reject / Request Changes
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.ceo.internal-notice.decision', $notice->id) }}" method="POST">
                @csrf
                <input type="hidden" name="decision" value="reject">
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size: 13px;">
                        Please provide your reason for rejection or details on the changes required.
                        The notice will be sent back to the preparer.
                    </p>
                    <div class="mb-0">
                        <label class="form-label">
                            Reason / Comments <span class="text-danger">*</span>
                        </label>
                        <textarea name="comment" class="form-control" rows="4"
                                  placeholder="Describe the required changes or reason for rejection..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-close"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Special Approve Modal --}}
<div class="modal fade" id="specialApproveModal" tabindex="-1" aria-labelledby="specialApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--twwda-navy-light);">
                <h6 class="modal-title" id="specialApproveModalLabel">
                    <i class="mdi mdi-shield-check-outline me-1 text-twwda-navy"></i> Special Approval (CS/DD IA)
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.internal-notice.special-approve', $notice->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3" style="font-size: 13px;">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Special approval is for cases where the CS or Deputy Director IA authorizes the notice on behalf of the CEO.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Reason for Special Approval <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" class="form-control" rows="3"
                                  placeholder="Explain why special approval is needed..."
                                  required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Approving As</label>
                        <select name="approving_as" class="form-select" required>
                            <option value="">Select role...</option>
                            <option value="cs">Corporation Secretary</option>
                            <option value="dd_ia">Deputy Director Internal Audit</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-shield-check"></i> Confirm Special Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
