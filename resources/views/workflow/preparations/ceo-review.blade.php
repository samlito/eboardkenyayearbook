@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('workflow.ceo.agenda-items') }}" class="btn btn-sm btn-light rounded-pill mb-2">
                <i class="mdi mdi-arrow-left"></i> Back to Pending Items
            </a>
            <h3 class="mb-0">
                <i class="mdi {{ $item->status === 'pending_pulldown' ? 'mdi-eye-off-outline text-info' : 'mdi-plus-circle-outline text-warning' }} me-1"></i>
                {{ $item->status === 'pending_pulldown' ? 'Pulldown Request' : 'New Agenda Item' }} Review
            </h3>
            <p class="text-muted mb-0">{{ $item->meeting->title ?? '' }} &mdash; {{ $item->meeting->boardCommittee->name ?? '' }}</p>
        </div>
    </div>

    <div class="row">
        {{-- Left: Item Details --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="mdi mdi-format-list-bulleted me-1"></i> Agenda Item Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless" style="font-size: 13px;">
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 35%;">Title:</td>
                            <td class="fw-bold">{{ $item->title }}</td>
                        </tr>
                        @if($item->item_number)
                            <tr>
                                <td class="text-muted fw-semibold">Item Number:</td>
                                <td>{{ $item->item_number }}</td>
                            </tr>
                        @endif
                        @if($item->description)
                            <tr>
                                <td class="text-muted fw-semibold">Description:</td>
                                <td>{{ $item->description }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted fw-semibold">Source:</td>
                            <td>
                                <span class="badge bg-soft-{{ $item->source === 'cs_added' ? 'warning' : 'success' }} text-{{ $item->source === 'cs_added' ? 'warning' : 'success' }}">
                                    {{ $item->source_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Status:</td>
                            <td>
                                <span class="badge bg-soft-{{ $item->status_color }} text-{{ $item->status_color }}">
                                    <i class="mdi {{ $item->status_icon }} me-1"></i>{{ $item->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">
                                {{ $item->status === 'pending_pulldown' ? 'Requested By:' : 'Added By:' }}
                            </td>
                            <td>
                                @if($item->status === 'pending_pulldown')
                                    {{ $item->pulldownRequestedByUser->name ?? 'Unknown' }}
                                @else
                                    {{ $item->addedByUser->name ?? 'Unknown' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Date:</td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    {{-- Pulldown Justification --}}
                    @if($item->status === 'pending_pulldown' && $item->pulldown_justification)
                        <div class="alert alert-info mt-3">
                            <h6 class="mb-1"><i class="mdi mdi-text-box-outline me-1"></i> Pulldown Justification</h6>
                            <p class="mb-0" style="font-size: 13px;">{{ $item->pulldown_justification }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Board Paper (if attached) --}}
            @if($item->boardPaper)
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-file-document-outline text-primary me-1"></i> Attached Board Paper</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless" style="font-size: 13px;">
                            <tr>
                                <td class="text-muted fw-semibold" style="width: 35%;">Title:</td>
                                <td>{{ $item->boardPaper->title }}</td>
                            </tr>
                            @if($item->boardPaper->paper_number)
                                <tr>
                                    <td class="text-muted fw-semibold">Paper Number:</td>
                                    <td>{{ $item->boardPaper->paper_number }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted fw-semibold">Prepared By:</td>
                                <td>{{ $item->boardPaper->author->name ?? 'N/A' }}</td>
                            </tr>
                        </table>

                        @if($item->boardPaper->file_path)
                            <a href="{{ asset('storage/' . $item->boardPaper->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="mdi mdi-file-pdf-box me-1"></i> View Board Paper
                            </a>
                        @endif

                        {{-- Annexures --}}
                        @if($item->boardPaper->annexures && $item->boardPaper->annexures->count() > 0)
                            <hr>
                            <h6 class="mb-2"><i class="mdi mdi-paperclip me-1"></i> Annexures</h6>
                            @foreach($item->boardPaper->annexures as $annexure)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="mdi mdi-file-outline text-muted"></i>
                                    <a href="{{ asset('storage/' . $annexure->file_path) }}" target="_blank" style="font-size: 13px;">
                                        {{ $annexure->title ?? $annexure->file_name }}
                                    </a>
                                    <small class="text-muted">({{ number_format(($annexure->file_size ?? 0) / 1024) }} KB)</small>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Decision Panel --}}
        <div class="col-lg-5">

            {{-- Approve Card --}}
            <div class="card border-success">
                <div class="card-header bg-soft-success">
                    <h5 class="mb-0 text-success"><i class="mdi mdi-check-circle-outline me-1"></i> Approve</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('workflow.ceo.agenda-item.decision', $item) }}" method="POST" id="approveForm">
                        @csrf
                        <input type="hidden" name="decision" value="approve">

                        @if($item->status === 'pending_approval' && $item->boardPaper)
                            {{-- CEO Signature for board paper --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">CEO Signature</label>
                                <p class="text-muted mb-2" style="font-size: 12px;">
                                    Your signature will be appended to the last page of the board paper.
                                </p>
                                <div class="border rounded" style="background: #fbfbfb;">
                                    <canvas id="signaturePad" width="380" height="150" style="width: 100%; cursor: crosshair;"></canvas>
                                </div>
                                <input type="hidden" name="ceo_signature" id="ceoSignatureData">
                                <div class="mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSignature">
                                        <i class="mdi mdi-eraser me-1"></i> Clear
                                    </button>
                                </div>
                            </div>
                        @endif

                        <p class="text-muted" style="font-size: 13px;">
                            @if($item->status === 'pending_pulldown')
                                Approving will hide this agenda item from board members.
                            @else
                                Approving will make this agenda item visible to board members.
                            @endif
                        </p>

                        <button type="button" class="btn btn-success w-100" id="approveBtn">
                            <i class="mdi mdi-check me-1"></i> Approve
                        </button>
                    </form>
                </div>
            </div>

            {{-- Reject Card --}}
            <div class="card border-danger">
                <div class="card-header bg-soft-danger">
                    <h5 class="mb-0 text-danger"><i class="mdi mdi-close-circle-outline me-1"></i> Reject</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('workflow.ceo.agenda-item.decision', $item) }}" method="POST" id="rejectForm">
                        @csrf
                        <input type="hidden" name="decision" value="reject">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" placeholder="Provide a reason for rejecting this {{ $item->status === 'pending_pulldown' ? 'pulldown request' : 'agenda item' }}..." required></textarea>
                        </div>

                        <button type="button" class="btn btn-danger w-100" id="rejectBtn">
                            <i class="mdi mdi-close me-1"></i> Reject
                        </button>
                    </form>
                </div>
            </div>

            {{-- Meeting Context --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Meeting Context</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                        <tr>
                            <td class="text-muted">Meeting:</td>
                            <td class="fw-semibold">{{ $item->meeting->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Committee:</td>
                            <td>{{ $item->meeting->boardCommittee->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date:</td>
                            <td>{{ $item->meeting->meeting_date ? \Carbon\Carbon::parse($item->meeting->meeting_date)->format('d M Y') : 'TBD' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ── Signature Pad ──────────────────────────────────
    var canvas = document.getElementById('signaturePad');
    var signaturePad = null;

    if (canvas) {
        var ctx = canvas.getContext('2d');
        var drawing = false;

        canvas.addEventListener('mousedown', function(e) {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        });
        canvas.addEventListener('mousemove', function(e) {
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        });
        canvas.addEventListener('mouseup', function() {
            drawing = false;
            $('#ceoSignatureData').val(canvas.toDataURL('image/png'));
        });
        canvas.addEventListener('mouseleave', function() {
            drawing = false;
        });

        // Touch support
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            var touch = e.touches[0];
            var rect = canvas.getBoundingClientRect();
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
        });
        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            if (!drawing) return;
            var touch = e.touches[0];
            var rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
            ctx.stroke();
        });
        canvas.addEventListener('touchend', function() {
            drawing = false;
            $('#ceoSignatureData').val(canvas.toDataURL('image/png'));
        });

        $('#clearSignature').on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            $('#ceoSignatureData').val('');
        });
    }

    // ── Approve Button ────────────────────────────────
    $('#approveBtn').on('click', function() {
        Swal.fire({
            title: 'Approve this {{ $item->status === "pending_pulldown" ? "pulldown request" : "agenda item" }}?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#099139',
            confirmButtonText: 'Yes, Approve'
        }).then(function(result) {
            if (result.isConfirmed) {
                $('#approveForm').submit();
            }
        });
    });

    // ── Reject Button ─────────────────────────────────
    $('#rejectBtn').on('click', function() {
        var reason = $('#rejectForm textarea[name="reason"]').val().trim();
        if (!reason) {
            toastr.error('Please provide a reason for rejection.');
            return;
        }

        Swal.fire({
            title: 'Reject this {{ $item->status === "pending_pulldown" ? "pulldown request" : "agenda item" }}?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Reject'
        }).then(function(result) {
            if (result.isConfirmed) {
                $('#rejectForm').submit();
            }
        });
    });
});
</script>
@endpush
