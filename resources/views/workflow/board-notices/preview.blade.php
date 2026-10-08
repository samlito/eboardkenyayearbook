@extends('admin_dashboard')

@section('admin')
@php
    $statusColors = [
        'draft'     => 'warning',
        'submitted' => 'info',
        'approved'  => 'success',
        'rejected'  => 'danger',
        'sent'      => 'primary',
    ];
    $statusColor = $statusColors[$notice->status] ?? 'secondary';

    // Resolve addressees — each element can be an integer (board member ID) or an associative array
    $addressees = is_array($notice->addressees) ? $notice->addressees : (json_decode($notice->addressees, true) ?? []);
    $resolvedAddressees = [];
    foreach ($addressees as $entry) {
        if (is_array($entry)) {
            $resolvedAddressees[] = $entry;
        } elseif (is_numeric($entry)) {
            $member = $boardMembers->firstWhere('id', $entry);
            if ($member) {
                $resolvedAddressees[] = [
                    'id'                => $member->id,
                    'name'              => $member->names ?? $member->name ?? '',
                    'designation'       => $member->designation ?? '',
                    'email'             => $member->email ?? '',
                    'postal_address'    => $member->postal_address ?? '',
                    'place_of_residence'=> $member->place_of_residence ?? '',
                ];
            }
        }
    }

    // Agenda items
    $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : (json_decode($notice->agenda_items, true) ?? []);

    // CC entries
    $ccEntries = is_array($notice->cc) ? $notice->cc : (json_decode($notice->cc, true) ?? []);

    // Format the notice date with ordinal superscript
    $dateObj = \Carbon\Carbon::parse($notice->notice_date ?? $notice->reference_date ?? $notice->created_at);
    $day = $dateObj->day;
    $ordinalSuffix = match(true) {
        in_array($day, [11, 12, 13]) => 'th',
        $day % 10 === 1              => 'st',
        $day % 10 === 2              => 'nd',
        $day % 10 === 3              => 'rd',
        default                      => 'th',
    };
    $formattedDate = $day . '<sup>' . $ordinalSuffix . '</sup> ' . $dateObj->format('F Y');

    // Rejection comments
    $rejectionComment = null;
    if ($notice->status === 'rejected' && $notice->comments) {
        $rejectionComment = $notice->comments->where('action', 'rejected')->first();
    }

    // Is audit committee (doesn't require CEO approval)
    $isAudit = !$notice->requires_ceo_approval;
@endphp

<div class="content">

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 1: ACTION BAR (d-print-none)
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="d-print-none mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            {{-- Left: Back --}}
            <div class="d-flex gap-2">
                <a href="{{ route('workflow.board-notices.index') }}" class="btn btn-sm btn-light rounded-pill shadow-sm">
                    <i class="mdi mdi-bulletin-board me-1"></i> All Notices
                </a>
                <a href="{{ route('workflow.meetings.show', $notice->meeting_id) }}" class="btn btn-sm btn-light rounded-pill shadow-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Meeting
                </a>
            </div>

            {{-- Right: Status + Actions --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Status badge --}}
                <span class="badge bg-{{ $statusColor }} px-3 py-2" style="font-size: 12px;">
                    <i class="mdi {{ $notice->status_icon }} me-1"></i>{{ ucfirst($notice->status) }}
                </span>

                @switch($notice->status)
                    @case('draft')
                        <a href="{{ route('workflow.board-notice.create', $notice->meeting_id) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-pencil-outline me-1"></i> Edit Notice
                        </a>
                        @if($isAudit)
                            <button type="button" class="btn btn-sm btn-success" onclick="selfApprove()">
                                <i class="mdi mdi-check-decagram me-1"></i> Approve & Sign
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-primary" onclick="submitForApproval()">
                                <i class="mdi mdi-send-check-outline me-1"></i> Submit for Approval
                            </button>
                        @endif
                        @break

                    @case('submitted')
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2" style="font-size: 12px;">
                            <i class="mdi mdi-clock-outline me-1"></i> Awaiting CEO Approval
                        </span>
                        @break

                    @case('approved')
                        <button type="button" class="btn btn-sm btn-success" onclick="sendToBoard()">
                            <i class="mdi mdi-email-send-outline me-1"></i> Send to Board Members
                        </button>
                        @break

                    @case('rejected')
                        <a href="{{ route('workflow.board-notice.create', $notice->meeting_id) }}"
                           class="btn btn-sm btn-outline-warning">
                            <i class="mdi mdi-pencil-outline me-1"></i> Edit & Revise
                        </a>
                        @break

                    @case('sent')
                        <button type="button" class="btn btn-sm btn-warning" onclick="sendReminder()">
                            <i class="mdi mdi-bell-ring-outline me-1"></i> Send Reminder
                        </button>
                        <span class="text-muted" style="font-size: 12px;">
                            <i class="mdi mdi-clock-check-outline me-1"></i>
                            Sent {{ $notice->sent_at->diffForHumans() }}
                            @if($notice->reminder_count)
                                &middot; {{ $notice->reminder_count }} reminder{{ $notice->reminder_count > 1 ? 's' : '' }} sent
                            @endif
                        </span>
                        @break
                @endswitch

                {{-- Reschedule button (only for sent notices) --}}
                @if($notice->status === 'sent')
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="rescheduleNotice()">
                        <i class="mdi mdi-calendar-refresh me-1"></i> Reschedule Notice
                    </button>
                @endif

                {{-- History / Audit Trail --}}
                <a href="{{ route('workflow.board-notice.history', $notice) }}" class="btn btn-sm btn-outline-dark">
                    <i class="mdi mdi-history me-1"></i> History
                </a>

                {{-- Print button always visible --}}
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="mdi mdi-printer-outline me-1"></i> Print Notice
                </button>
            </div>
        </div>

        {{-- Rejection feedback --}}
        @if($notice->status === 'rejected' && $rejectionComment)
            <div class="alert alert-danger mt-3 mb-0 d-flex align-items-start">
                <i class="mdi mdi-alert-circle-outline fs-4 me-2 mt-1"></i>
                <div>
                    <strong>Notice Rejected</strong>
                    <p class="mb-1">{{ $rejectionComment->comment }}</p>
                    <small class="text-muted">
                        By {{ $rejectionComment->user->name ?? 'CEO' }}
                        on {{ $rejectionComment->created_at->format('d M Y, h:i A') }}
                    </small>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 2: OFFICIAL NOTICE DOCUMENT
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="card mx-auto shadow-sm notice-document-card" style="max-width: 800px;">
        <div class="card-body p-5 position-relative" id="noticeDocument">

            {{-- Status watermark overlay --}}
            @if($notice->status !== 'sent')
                <div class="notice-watermark">{{ strtoupper($notice->status) }}</div>
            @endif

            {{-- ── LETTERHEAD ──────────────────────────────────────────── --}}
            @include('workflow.components.letterhead')

            {{-- ── REFERENCE & DATE ────────────────────────────────────── --}}
            <div class="d-flex justify-content-between align-items-start mb-4" style="font-size: 14px;">
                <div>
                    <span class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Our Ref:</span><br>
                    <strong>{{ $notice->reference_number }}</strong>
                </div>
                <div class="text-end">
                    <span class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Date:</span><br>
                    <strong>{!! $formattedDate !!}</strong>
                </div>
            </div>

            {{-- ── ADDRESSEES ──────────────────────────────────────────── --}}
            @foreach($resolvedAddressees as $addressee)
                <div class="mb-3" style="font-size: 14px; line-height: 1.7;">
                    <strong>{{ $addressee['name'] ?? '' }}</strong>
                    @if(!empty($addressee['designation']))
                        <br>{{ $addressee['designation'] }}
                    @endif
                    @if(!empty($addressee['postal_address']))
                        <br>{{ $addressee['postal_address'] }}
                    @endif
                    @if(!empty($addressee['place_of_residence']))
                        <br><strong><u>{{ strtoupper($addressee['place_of_residence']) }}</u></strong>
                    @endif
                    @if(!empty($addressee['email']))
                        <br>Email: {{ $addressee['email'] }}
                    @endif
                </div>
            @endforeach

            {{-- ── SUBJECT LINE ────────────────────────────────────────── --}}
            <div class="mt-4 mb-4" style="font-size: 14px;">
                <strong><u>RE: {{ strtoupper($notice->subject) }}</u></strong>
            </div>

            {{-- ── BODY PARAGRAPH ──────────────────────────────────────── --}}
            <div class="notice-body mb-4">
                {!! $notice->body !!}
            </div>

            {{-- ── AGENDA ITEMS ────────────────────────────────────────── --}}
            @if(count($agendaItems) > 0)
                <div class="mb-4" style="font-size: 14px; line-height: 1.8;">
                    <p class="mb-2">The agenda of the meeting will be: -</p>
                    <ol class="notice-agenda-list">
                        @foreach($agendaItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif

            {{-- ── CLOSING TEXT ─────────────────────────────────────────── --}}
            <div class="mb-5" style="font-size: 14px; line-height: 1.8; text-align: justify;">
                <p>{{ $notice->closing_text ?? 'Kindly take note and plan to attend.' }}</p>
            </div>

            {{-- ── SIGNATURE BLOCK ─────────────────────────────────────── --}}
            <div class="mt-5 pt-3">
                <div style="width: 320px;">
                    @if($notice->ceo_signature)
                        <img src="{{ $notice->ceo_signature }}" alt="Signature"
                             style="max-height: 80px; max-width: 250px;" class="mb-1">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                    <div style="border-top: 2px solid #333; width: 250px;"></div>
                    @if($notice->signatory_name)
                        <p class="mb-0 mt-1" style="font-size: 14px;">
                            <strong>{{ strtoupper($notice->signatory_name) }}</strong>
                        </p>
                    @endif
                    @if($notice->signatory_title)
                        <p class="mb-0" style="font-size: 14px;">
                            <strong><u>{{ strtoupper($notice->signatory_title) }}</u></strong>
                        </p>
                    @endif
                </div>
            </div>

            {{-- ── CC SECTION ──────────────────────────────────────────── --}}
            @if(count($ccEntries) > 0)
                <div class="mt-5 pt-3" style="font-size: 14px; line-height: 1.8;">
                    <div class="d-flex">
                        <strong class="me-3" style="white-space: nowrap;">CC:</strong>
                        <div>
                            @foreach($ccEntries as $index => $cc)
                                <div class="{{ $index > 0 ? 'mt-2' : '' }}">
                                    @if(is_array($cc))
                                        <div>{{ $cc['name'] ?? '' }}</div>
                                        @if(!empty($cc['address']))
                                            <div>{{ $cc['address'] }}</div>
                                        @endif
                                    @else
                                        <div>{{ $cc }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── KEBS ISO FOOTER ─────────────────────────────────────── --}}
            <div class="text-center mt-5 pt-4" style="border-top: 1px solid #ddd;">
                <img src="{{ asset('backend/assets/images/kebs-badge.png') }}" alt="KEBS ISO Certified"
                     style="max-height: 50px; opacity: 0.7;" onerror="this.style.display='none'">
                <p class="text-muted mb-0 mt-1" style="font-size: 10px;">ISO 9001:2015 Certified</p>
            </div>

        </div>{{-- /#noticeDocument --}}
    </div>{{-- /.card --}}

</div>{{-- /.content --}}

{{-- Self-approve signature modal --}}
@if($isAudit && in_array($notice->status, ['draft', 'rejected']))
<div class="modal fade" id="selfApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="selfApproveForm" action="{{ route('workflow.board-notice.self-approve', $notice) }}" method="POST">
                @csrf
                <div class="modal-header bg-success bg-opacity-10">
                    <h5 class="modal-title"><i class="mdi mdi-check-decagram me-1 text-success"></i> Approve & Sign Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Signatory Name</label>
                        <input type="text" name="signatory_name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Signatory Title</label>
                        <input type="text" name="signatory_title" class="form-control" value="DIRECTOR, INTERNAL AUDIT" required>
                    </div>
                    @include('workflow.components.signature-pad', ['inputName' => 'signature', 'label' => 'Your Signature'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check-decagram me-1"></i> Approve & Sign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    /* ── Notice document typography ───────────────────────────────────── */
    .notice-body {
        font-size: 14px;
        line-height: 1.8;
        text-align: justify;
        font-family: 'Times New Roman', Times, Georgia, serif;
    }
    .notice-body strong,
    .notice-body b {
        font-weight: 700;
    }

    .notice-agenda-list {
        padding-left: 20px;
        line-height: 2;
        margin-bottom: 0;
    }
    .notice-agenda-list li {
        padding-left: 8px;
    }

    /* ── Status watermark overlay ─────────────────────────────────────── */
    .notice-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-35deg);
        font-size: 100px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 12px;
        opacity: 0.06;
        pointer-events: none;
        z-index: 1;
        white-space: nowrap;
        color: #333;
    }

    /* ── Card styling ─────────────────────────────────────────────────── */
    .notice-document-card {
        border: 1px solid #e0e0e0;
    }
    .notice-document-card .card-body > *:not(.notice-watermark) {
        position: relative;
        z-index: 2;
    }

    /* ── Print styles ─────────────────────────────────────────────────── */
    @media print {
        /* Hide navigation chrome */
        .left-side-menu,
        .navbar-custom,
        .topbar,
        .footer,
        .d-print-none {
            display: none !important;
        }

        /* Reset page wrapper margins */
        .content-page {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }
        .content {
            padding: 0 !important;
        }

        /* Remove card chrome for clean print */
        .notice-document-card {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
        }
        .notice-document-card .card-body {
            padding: 0 !important;
        }

        /* Watermark prints lighter */
        .notice-watermark {
            opacity: 0.04;
        }

        /* Ensure letterhead images print */
        .twwda-letterhead img {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* KEBS badge prints */
        img[alt="KEBS ISO Certified"] {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Signature image prints */
        img[alt="Signature"] {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Body text */
        body {
            font-size: 12pt;
        }

        /* Don't break inside addressee blocks */
        .mb-3 {
            page-break-inside: avoid;
        }
    }

    @page {
        size: A4;
        margin: 15mm 20mm;
    }
</style>
@endpush

@push('scripts')
<script>
/**
 * Submit notice to CEO for approval
 */
function submitForApproval() {
    Swal.fire({
        title: 'Submit for CEO Approval?',
        text: 'This notice will be sent to the CEO for review and signing. You will not be able to edit it until they respond.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="mdi mdi-send-check-outline"></i> Yes, Submit',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("workflow.board-notice.submit", $notice) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Send approved notice to all board members
 */
function sendToBoard() {
    Swal.fire({
        title: 'Send Notice to Board Members?',
        html: 'This will email the official board notice to <strong>all listed addressees</strong>. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#099139',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="mdi mdi-email-send-outline"></i> Yes, Send Now',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("workflow.board-notice.send", $notice) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Send a reminder to board members
 */
function sendReminder() {
    Swal.fire({
        title: 'Send Reminder?',
        html: 'A reminder email will be sent to all board members about this meeting notice.' +
              @if($notice->reminder_count)
              '<br><br><small class="text-muted">{{ $notice->reminder_count }} reminder(s) already sent.</small>' +
              @endif
              '',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#e2a03f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="mdi mdi-bell-ring-outline"></i> Send Reminder',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("workflow.board-notice.reminder", $notice) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Reschedule — create a new notice from this one
 */
function rescheduleNotice() {
    Swal.fire({
        title: 'Reschedule This Notice?',
        html: 'This will create a <strong>new notice</strong> linked to this one, pre-filled with the same details. The new notice will be in draft and can be edited before submission.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="mdi mdi-calendar-refresh"></i> Yes, Reschedule',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("workflow.board-notice.reschedule", $notice) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Self-approve (audit committee) - open signature modal
 */
function selfApprove() {
    Swal.fire({
        title: 'Approve & Sign This Notice?',
        text: 'As this is an audit committee notice, it does not require CEO approval. You will sign it directly.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#099139',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="mdi mdi-check-decagram"></i> Proceed to Sign',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var modal = new bootstrap.Modal(document.getElementById('selfApproveModal'));
            modal.show();
        }
    });
}
</script>
@endpush
