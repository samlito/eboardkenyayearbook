@extends('admin_dashboard')

@push('styles')
<style>
    .ceo-review-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.04); overflow:hidden; }
    .ceo-review-card .card-header { background:#fff; border-bottom:2px solid {{ setting('primary_color', '#1a1a1a') }}; padding:.85rem 1.25rem; }
    .ceo-review-card .card-header h5 { margin:0; font-size:.95rem; font-weight:600; color:{{ setting('primary_color', '#1a1a1a') }}; }
    .notice-preview-mini { font-family:'Times New Roman',Times,serif; font-size:14px; line-height:1.8; }
    .decision-card { border:2px solid transparent; border-radius:12px; padding:1.25rem; cursor:pointer; transition:all .2s; }
    .decision-card.selected-approve { border-color:#099139; background:rgba(9,145,57,.04); }
    .decision-card.selected-reject { border-color:#dc3545; background:rgba(220,53,69,.04); }
</style>
@endpush

@section('admin')
@php
    // Resolve addressees for display
    $addressees = is_array($notice->addressees) ? $notice->addressees : (json_decode($notice->addressees, true) ?? []);
    $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : (json_decode($notice->agenda_items, true) ?? []);
@endphp

<div class="content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <a href="{{ route('workflow.ceo.board-notices') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Pending Notices
                </a>
                <h3 class="mb-0">
                    <i class="mdi mdi-file-check-outline me-1" style="color:{{ setting('primary_color', '#1a1a1a') }};"></i> Review Board Notice
                </h3>
                <p class="text-muted mb-0">{{ $notice->meeting->title ?? '' }} &mdash; {{ $notice->reference_number }}</p>
            </div>
            <span class="badge bg-info px-3 py-2" style="font-size:12px;">
                <i class="mdi mdi-clock-outline me-1"></i> Awaiting Your Decision
            </span>
        </div>

        <div class="row">
            {{-- Left: Notice Summary --}}
            <div class="col-lg-7">
                <div class="card ceo-review-card mb-3">
                    <div class="card-header">
                        <h5><i class="mdi mdi-file-document-outline me-1"></i> Notice Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-3" style="font-size:.88rem;">
                            <tr>
                                <td class="text-muted" style="width:140px;">Reference No.</td>
                                <td class="fw-semibold"><code>{{ $notice->reference_number }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Notice Type</td>
                                <td>
                                    <span class="badge" style="background:{{ $notice->notice_type === '21_day' ? 'rgba(34,34,34,.1)' : 'rgba(9,145,57,.1)' }};color:{{ $notice->notice_type === '21_day' ? setting('primary_color', '#1a1a1a') : '#099139' }};">
                                        {{ $notice->notice_type === '21_day' ? '21-Day Notice' : '7-Day Notice' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Notice Date</td>
                                <td>{{ $notice->notice_date ? $notice->notice_date->format('d M Y') : ($notice->reference_date ? $notice->reference_date->format('d M Y') : 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Committee</td>
                                <td>{{ $notice->meeting->boardCommittee->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted By</td>
                                <td>{{ $notice->creator->name ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Addressees</td>
                                <td>{{ count($addressees) }} board member{{ count($addressees) !== 1 ? 's' : '' }}</td>
                            </tr>
                        </table>

                        <hr>

                        {{-- Subject --}}
                        <div class="mb-3">
                            <label class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em;">Subject</label>
                            <div class="fw-bold" style="font-size:14px;">{{ $notice->subject }}</div>
                        </div>

                        {{-- Body --}}
                        <div class="mb-3">
                            <label class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em;">Body</label>
                            <div class="notice-preview-mini p-3 bg-light rounded">{!! $notice->body !!}</div>
                        </div>

                        {{-- Agenda --}}
                        @if(count($agendaItems))
                            <div class="mb-3">
                                <label class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em;">Agenda Items</label>
                                <ol style="font-size:14px;line-height:2;padding-left:20px;" class="mb-0">
                                    @foreach($agendaItems as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        {{-- Closing --}}
                        @if($notice->closing_text)
                            <div class="mb-0">
                                <label class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em;">Closing</label>
                                <p style="font-size:14px;">{{ $notice->closing_text }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Link to full preview --}}
                <a href="{{ route('workflow.board-notice.preview', $notice) }}" class="btn btn-outline-primary btn-sm mb-3" target="_blank">
                    <i class="mdi mdi-eye-outline me-1"></i> View Full Formatted Notice
                </a>
            </div>

            {{-- Right: Decision Panel --}}
            <div class="col-lg-5">
                <div class="card ceo-review-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-gavel me-1"></i> Your Decision</h5>
                    </div>
                    <div class="card-body">
                        <form id="decisionForm" action="{{ route('workflow.ceo.board-notice.decision', $notice) }}" method="POST">
                            @csrf
                            <input type="hidden" name="decision" id="decisionInput" value="">

                            {{-- Approve Card --}}
                            <div class="decision-card mb-3 border" id="approveCard" onclick="selectDecision('approve')">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:44px;height:44px;border-radius:50%;background:rgba(9,145,57,.1);display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-check-decagram" style="font-size:22px;color:#099139;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-success">Approve & Sign</h6>
                                        <small class="text-muted">Approve this notice for distribution to board members</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Signature fields (shown when approve selected) --}}
                            <div id="signatureFields" style="display:none;" class="mb-3">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Signatory Name</label>
                                    <input type="text" name="signatory_name" class="form-control form-control-sm" value="{{ auth()->user()->name }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Signatory Title</label>
                                    <input type="text" name="signatory_title" class="form-control form-control-sm" value="{{ strtoupper(setting('signatory_title_ceo', 'Chief Executive Officer')) }}">
                                </div>
                                @include('workflow.components.signature-pad', ['inputName' => 'signature', 'label' => 'Your Signature'])
                            </div>

                            <hr>

                            {{-- Reject Card --}}
                            <div class="decision-card mb-3 border" id="rejectCard" onclick="selectDecision('reject')">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:44px;height:44px;border-radius:50%;background:rgba(220,53,69,.1);display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-close-circle-outline" style="font-size:22px;color:#dc3545;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-danger">Reject with Comments</h6>
                                        <small class="text-muted">Return notice to the creator for revisions</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Rejection comment (shown when reject selected) --}}
                            <div id="rejectionFields" style="display:none;" class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="Please explain what needs to be changed..."></textarea>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-lg" id="submitDecisionBtn" disabled
                                        style="background:{{ setting('primary_color', '#1a1a1a') }};color:#fff;border-radius:8px;">
                                    <i class="mdi mdi-check-bold me-1"></i> Confirm Decision
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Previous comments --}}
                @if($notice->comments && $notice->comments->count())
                    <div class="card ceo-review-card mt-3">
                        <div class="card-header">
                            <h5><i class="mdi mdi-comment-multiple-outline me-1"></i> Previous Comments</h5>
                        </div>
                        <div class="card-body">
                            @foreach($notice->comments as $comment)
                                <div class="d-flex gap-2 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(200, 16, 46,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="mdi mdi-account" style="color:{{ setting('primary_color', '#1a1a1a') }};"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.85rem;">{{ $comment->user->name ?? 'System' }}</div>
                                        <p class="mb-1" style="font-size:.85rem;">{{ $comment->comment }}</p>
                                        <small class="text-muted">{{ $comment->created_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var selectedDecision = null;

function selectDecision(decision) {
    selectedDecision = decision;
    $('#decisionInput').val(decision);

    // Reset states
    $('#approveCard').removeClass('selected-approve');
    $('#rejectCard').removeClass('selected-reject');
    $('#signatureFields').hide();
    $('#rejectionFields').hide();

    if (decision === 'approve') {
        $('#approveCard').addClass('selected-approve');
        $('#signatureFields').slideDown(200);
        $('#submitDecisionBtn').prop('disabled', false)
            .css('background', '#099139')
            .html('<i class="mdi mdi-check-decagram me-1"></i> Approve & Sign Notice');
    } else {
        $('#rejectCard').addClass('selected-reject');
        $('#rejectionFields').slideDown(200);
        $('#submitDecisionBtn').prop('disabled', false)
            .css('background', '#dc3545')
            .html('<i class="mdi mdi-close-circle-outline me-1"></i> Reject Notice');
    }
}

$('#decisionForm').on('submit', function(e) {
    e.preventDefault();
    var form = this;

    if (!selectedDecision) return;

    var title = selectedDecision === 'approve' ? 'Approve This Notice?' : 'Reject This Notice?';
    var text = selectedDecision === 'approve'
        ? 'This notice will be approved and the creator can send it to board members.'
        : 'This notice will be returned to the creator for revisions.';
    var icon = selectedDecision === 'approve' ? 'question' : 'warning';
    var confirmColor = selectedDecision === 'approve' ? '#099139' : '#dc3545';

    // If rejecting, ensure comment is filled
    if (selectedDecision === 'reject') {
        var comment = $('textarea[name="comment"]').val().trim();
        if (!comment) {
            Swal.fire({ title: 'Comment Required', text: 'Please provide a reason for rejection.', icon: 'warning', confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}' });
            return;
        }
    }

    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: selectedDecision === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endpush
