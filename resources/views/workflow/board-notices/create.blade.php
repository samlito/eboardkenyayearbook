@extends('admin_dashboard')

@push('styles')
<style>
    /* ── Brand accents ───────────────────────────────────── */
    .btn-twwda-navy  { background: {{ setting('primary_color', '#1a1a1a') }}; border-color: {{ setting('primary_color', '#1a1a1a') }}; color: #fff; }
    .btn-twwda-navy:hover { background: #2b2b2b; border-color: #9b0c23; color: #fff; }
    .btn-twwda-green { background: #099139; border-color: #099139; color: #fff; }
    .btn-twwda-green:hover { background: #077a2f; border-color: #077a2f; color: #fff; }
    .text-twwda-navy { color: {{ setting('primary_color', '#1a1a1a') }}; }
    .text-twwda-green { color: #099139; }
    .border-twwda-navy { border-color: {{ setting('primary_color', '#1a1a1a') }} !important; }
    .bg-twwda-navy-soft { background: rgba(200, 16, 46,.07); }

    /* ── Card styling ────────────────────────────────────── */
    .notice-card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.06); border-radius: .5rem; margin-bottom: 1.25rem; }
    .notice-card .card-header {
        background: #fff;
        border-bottom: 2px solid {{ setting('primary_color', '#1a1a1a') }};
        padding: .75rem 1.25rem;
    }
    .notice-card .card-header h5 { margin: 0; font-size: .95rem; font-weight: 600; color: {{ setting('primary_color', '#1a1a1a') }}; }

    /* ── Addressee list ──────────────────────────────────── */
    .addressee-list { max-height: 360px; overflow-y: auto; }
    .addressee-item { transition: background .15s; cursor: pointer; }
    .addressee-item:hover { background: rgba(200, 16, 46,.04); }
    .addressee-item .avatar-circle {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: .8rem; color: #fff;
        flex-shrink: 0;
    }

    /* ── Quill editor ────────────────────────────────────── */
    #quill-body { min-height: 160px; font-size: .9rem; }
    #quill-body .ql-editor { min-height: 140px; }

    /* ── Agenda / CC repeater ────────────────────────────── */
    .agenda-item-row, .cc-entry-row { transition: background .15s; }
    .agenda-item-row:hover, .cc-entry-row:hover { background: rgba(200, 16, 46,.03); border-radius: .35rem; }
    .agenda-number {
        min-width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; font-weight: 600; font-size: .8rem;
        background: rgba(200, 16, 46,.1); color: {{ setting('primary_color', '#1a1a1a') }};
    }
    .drag-handle {
        cursor: grab; color: #adb5bd; font-size: 1.1rem; padding: 0 4px;
    }
    .drag-handle:hover { color: {{ setting('primary_color', '#1a1a1a') }}; }
    .sortable-ghost { opacity: .4; background: rgba(200, 16, 46,.08); border-radius: .35rem; }
    .sortable-chosen { box-shadow: 0 2px 8px rgba(0,0,0,.12); }
    .badge-assignment { background: rgba(9,145,57,.12); color: #099139; font-size: .7rem; font-weight: 500; padding: 2px 6px; border-radius: 3px; margin-left: 6px; white-space: nowrap; }

    /* ── Rejection banner ────────────────────────────────── */
    .rejection-banner {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: #fff; border-radius: .5rem; padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .rejection-banner .mdi { font-size: 1.3rem; }

    /* ── Guide card ──────────────────────────────────────── */
    .guide-card { background: rgba(200, 16, 46,.06); border: 1px dashed rgba(200, 16, 46,.25); border-radius: .5rem; }
    .guide-card ul { padding-left: 1.15rem; margin-bottom: 0; }
    .guide-card li { font-size: .8rem; margin-bottom: .35rem; color: #444; }
</style>
@endpush

@section('admin')
<div class="content">

    {{-- ── Page Header ────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('workflow.meetings.show', $meeting) }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Meeting
            </a>
            <h3 class="mb-0">
                <i class="mdi mdi-file-document-edit-outline text-twwda-navy me-1"></i>
                {{ $notice ? 'Edit' : 'Create' }} Official Board Notice
            </h3>
            <p class="text-muted mb-0">
                {{ $meeting->title }} &mdash; {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
            </p>
        </div>
    </div>

    {{-- ── Validation Errors ──────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-1"></i><strong>Please correct the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Rejection Banner ───────────────────────────────── --}}
    @if($rejectionComment)
        <div class="rejection-banner d-flex align-items-start gap-3">
            <i class="mdi mdi-alert-circle-outline mt-1"></i>
            <div>
                <strong>Notice Rejected</strong>
                <p class="mb-0 mt-1" style="opacity:.92; font-size:.9rem;">{{ $rejectionComment }}</p>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         FORM
    ══════════════════════════════════════════════════════ --}}
    <form action="{{ $notice ? route('workflow.board-notice.update', $notice) : route('workflow.board-notice.store', $meeting) }}"
          method="POST" id="noticeForm">
        @csrf
        @if($notice) @method('PUT') @endif

        {{-- Hidden action field — set by JS before submit --}}
        <input type="hidden" name="action" id="formAction" value="draft">

        <div class="row">

            {{-- ════════════════════════════════════════════════
                 LEFT COLUMN (8)
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-8">

                {{-- ── Card: Notice Identity ──────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-identifier me-1"></i>Notice Identity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Reference Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-pound"></i></span>
                                    <input type="text" name="reference_number" class="form-control"
                                           value="{{ old('reference_number', $notice->reference_number ?? $defaultRefNumber) }}"
                                           placeholder="e.g. {{ setting('institution_acronym', 'ORG') }}/BN/2026/001" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Notice Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                    <input type="date" name="notice_date" class="form-control"
                                           value="{{ old('notice_date', $notice ? ($notice->notice_date ? $notice->notice_date->format('Y-m-d') : ($notice->reference_date ? $notice->reference_date->format('Y-m-d') : date('Y-m-d'))) : date('Y-m-d')) }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Notice Type <span class="text-danger">*</span>
                                </label>
                                <select name="notice_type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="21_day" {{ old('notice_type', $notice->notice_type ?? '') === '21_day' ? 'selected' : '' }}>
                                        21-Day Notice
                                    </option>
                                    <option value="7_day" {{ old('notice_type', $notice->notice_type ?? '') === '7_day' ? 'selected' : '' }}>
                                        7-Day Notice
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Committee</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ optional($meeting->boardCommittee)->name ?? 'N/A' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Card: Addressees ───────────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5><i class="mdi mdi-account-group-outline me-1"></i>Board Members</h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                                <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllBtn">
                                <i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i>Deselect All
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            // Determine which members are already selected
                            $oldAddressees = old('addressees');
                            $existingAddressees = $notice && $notice->addressees ? collect($notice->addressees) : null;
                        @endphp
                        <div class="addressee-list">
                            @forelse($boardMembers as $member)
                                @php
                                    $isChecked = false;
                                    if ($oldAddressees !== null) {
                                        // Validation error — use old input
                                        $isChecked = in_array($member->id, $oldAddressees);
                                    } elseif ($existingAddressees) {
                                        // Editing existing notice
                                        $isChecked = $existingAddressees->contains($member->id)
                                                  || $existingAddressees->contains('id', $member->id);
                                    } else {
                                        // New notice — select all by default
                                        $isChecked = true;
                                    }

                                    // Avatar colour rotation
                                    $colors = [setting('primary_color', '#1a1a1a'),'#099139','#238b47','#e83e8c','#fd7e14','#20c997'];
                                    $avatarColor = $colors[$loop->index % count($colors)];
                                    $initials = collect(explode(' ', $member->names ?? $member->name ?? ''))
                                        ->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))
                                        ->take(2)->join('');
                                @endphp
                                <label class="addressee-item d-flex align-items-center gap-3 px-3 py-2 border-bottom mb-0"
                                       for="addr-{{ $member->id }}" style="cursor:pointer;">
                                    <input class="form-check-input addressee-cb flex-shrink-0" type="checkbox"
                                           name="addressees[]" value="{{ $member->id }}"
                                           id="addr-{{ $member->id }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <span class="avatar-circle" style="background:{{ $avatarColor }};">{{ $initials }}</span>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold text-truncate" style="font-size:.88rem;">
                                            {{ $member->names ?? $member->name }}
                                        </div>
                                        <small class="text-muted">{{ $member->designation ?? '' }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0" style="min-width:140px;">
                                        <div class="text-muted" style="font-size:.8rem;">
                                            <i class="mdi mdi-map-marker-outline me-1"></i>{{ $member->place_of_residence ?? '' }}
                                        </div>
                                        @if($member->email)
                                            <small class="text-muted" style="font-size:.75rem;">
                                                <i class="mdi mdi-email-outline me-1"></i>{{ $member->email }}
                                            </small>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="mdi mdi-account-off-outline d-block mb-1" style="font-size:2rem;"></i>
                                    No board members found for this committee.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ── Card: Notice Content ───────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-text-box-outline me-1"></i>Notice Content</h5>
                    </div>
                    <div class="card-body">
                        {{-- Subject --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-format-title"></i></span>
                                <input type="text" name="subject" class="form-control"
                                       value="{{ old('subject', $notice->subject ?? $defaultSubject) }}"
                                       placeholder="Subject line" required>
                            </div>
                        </div>

                        {{-- Body — Quill Rich Text Editor --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">
                                Body Paragraph <span class="text-danger">*</span>
                            </label>
                            <div id="quill-body">{!! old('body', $notice->body ?? $defaultBody) !!}</div>
                            <input type="hidden" name="body" id="quillBodyInput"
                                   value="{{ old('body', $notice->body ?? $defaultBody) }}">
                        </div>
                    </div>
                </div>

                {{-- ── Card: Agenda Items ─────────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5><i class="mdi mdi-format-list-numbered me-1"></i>Agenda Items</h5>
                        <div class="d-flex gap-2">
                            @if($canReorderAgenda ?? false)
                                <span class="text-muted" style="font-size:.75rem; line-height:2;">
                                    <i class="mdi mdi-drag me-1"></i>Drag to reorder
                                </span>
                            @endif
                            <button type="button" class="btn btn-sm btn-twwda-navy" id="addAgendaBtn">
                                <i class="mdi mdi-plus me-1"></i>Add Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="agendaContainer">
                        @php
                            $agendaItems = old('agenda_items',
                                ($notice && $notice->agenda_items) ? $notice->agenda_items : $defaultAgendaItems
                            );
                            $assignmentTitles = isset($assignmentAgendaItems) ? $assignmentAgendaItems : [];
                        @endphp
                        @foreach($agendaItems as $idx => $item)
                            <div class="agenda-item-row d-flex align-items-center gap-2 mb-2">
                                @if($canReorderAgenda ?? false)
                                    <span class="drag-handle" title="Drag to reorder"><i class="mdi mdi-drag-vertical"></i></span>
                                @endif
                                <span class="agenda-number">{{ $idx + 1 }}</span>
                                <input type="text" name="agenda_items[]" class="form-control form-control-sm"
                                       value="{{ $item }}" placeholder="Agenda item" required>
                                @if(in_array($item, $assignmentTitles))
                                    <span class="badge-assignment">From Assignments</span>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger remove-agenda-btn" title="Remove">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Card: Closing & CC ─────────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-email-seal-outline me-1"></i>Closing & CC</h5>
                    </div>
                    <div class="card-body">
                        {{-- Closing text --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Closing Text</label>
                            <input type="text" name="closing_text" class="form-control"
                                   value="{{ old('closing_text', $notice->closing_text ?? 'Please plan to attend and be punctual.') }}"
                                   placeholder="e.g. Please plan to attend and be punctual.">
                        </div>

                        {{-- CC entries --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-semibold mb-0">CC Recipients</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addCcBtn">
                                    <i class="mdi mdi-plus me-1"></i>Add CC
                                </button>
                            </div>
                            <div id="ccContainer">
                                @php
                                    $ccEntries = old('cc');
                                    if ($ccEntries === null && $notice && $notice->cc) {
                                        $ccEntries = $notice->cc;
                                    }
                                    if ($ccEntries === null && !$notice) {
                                        // Default CC based on committee type
                                        $ccEntries = $isAuditCommittee
                                            ? [
                                                ['name' => 'Inspector General, State Corporations', 'address' => ''],
                                                ['name' => 'CEO, ' . setting('institution_acronym', 'ORG') . ' (for information)', 'address' => ''],
                                              ]
                                            : [];
                                    }
                                    $ccEntries = $ccEntries ?? [];
                                @endphp
                                @foreach($ccEntries as $ci => $cc)
                                    <div class="cc-entry-row d-flex align-items-center gap-2 mb-2">
                                        <input type="text" name="cc[{{ $ci }}][name]" class="form-control form-control-sm"
                                               value="{{ is_array($cc) ? ($cc['name'] ?? '') : $cc }}"
                                               placeholder="Name / Title">
                                        <input type="text" name="cc[{{ $ci }}][address]" class="form-control form-control-sm"
                                               value="{{ is_array($cc) ? ($cc['address'] ?? '') : '' }}"
                                               placeholder="Address (optional)">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-cc-btn" title="Remove">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            @if(empty($ccEntries))
                                <p class="text-muted mb-0" style="font-size:.8rem;" id="ccEmptyHint">
                                    No CC recipients added yet.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div> {{-- /col-lg-8 --}}

            {{-- ════════════════════════════════════════════════
                 RIGHT COLUMN (4)
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-4">

                {{-- ── Card: Actions ──────────────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-rocket-launch-outline me-1"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            {{-- Save Draft --}}
                            <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
                                <i class="mdi mdi-content-save-outline me-1"></i>Save Draft
                            </button>

                            {{-- Preview (only for existing notice) --}}
                            @if($notice)
                                <a href="{{ route('workflow.board-notice.preview', $notice) }}" class="btn btn-outline-info" target="_blank">
                                    <i class="mdi mdi-eye-outline me-1"></i>Preview Notice
                                </a>
                            @endif

                            {{-- Submit / Self-approve --}}
                            @if($isAuditCommittee)
                                <button type="button" class="btn btn-twwda-green" id="submitBtn" data-action="self_approve">
                                    <i class="mdi mdi-check-decagram-outline me-1"></i>Approve & Sign
                                </button>
                            @else
                                <button type="button" class="btn btn-twwda-navy" id="submitBtn" data-action="submit">
                                    <i class="mdi mdi-send-check-outline me-1"></i>Submit to CEO for Approval
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Card: Meeting Details ──────────────────── --}}
                <div class="card notice-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-calendar-check-outline me-1"></i>Meeting Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0" style="font-size:.85rem;">
                            <tr>
                                <td class="text-muted" style="width:100px;">
                                    <i class="mdi mdi-calendar me-1"></i>Date
                                </td>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>Time</td>
                                <td>{{ $meeting->meeting_time ?? 'TBD' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="mdi mdi-map-marker-outline me-1"></i>Venue</td>
                                <td>{{ $meeting->venue ?? 'TBD' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="mdi mdi-account-group me-1"></i>Committee</td>
                                <td>{{ optional($meeting->boardCommittee)->name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- ── Card: Notice Status ────────────────────── --}}
                @if($notice)
                    <div class="card notice-card">
                        <div class="card-header">
                            <h5><i class="mdi mdi-{{ $notice->status_icon }} me-1"></i>Notice Status</h5>
                        </div>
                        <div class="card-body text-center">
                            <span class="badge bg-{{ $notice->status_color }} px-3 py-2" style="font-size:.85rem;">
                                <i class="mdi mdi-{{ $notice->status_icon }} me-1"></i>{{ ucfirst($notice->status) }}
                            </span>
                            @if($notice->submitted_at)
                                <div class="text-muted mt-2" style="font-size:.78rem;">
                                    Submitted {{ $notice->submitted_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── Card: Guide ────────────────────────────── --}}
                <div class="card notice-card guide-card">
                    <div class="card-body">
                        <h6 class="text-twwda-navy mb-2">
                            <i class="mdi mdi-lightbulb-on-outline me-1"></i>Format Guide
                        </h6>
                        <ul>
                            <li>The notice will be generated with official {{ setting('institution_acronym', 'ORG') }} letterhead.</li>
                            <li>Addressees receive individual letter blocks with their name and title.</li>
                            <li>Agenda items are auto-numbered in the final document.</li>
                            @if($isAuditCommittee)
                                <li class="text-twwda-green fw-semibold">
                                    As DDIA, you can self-approve audit committee notices without CEO sign-off.
                                </li>
                            @else
                                <li>After submission, the CEO will review and approve before distribution.</li>
                            @endif
                            <li>Use "Preview" after saving to see the formatted notice.</li>
                        </ul>
                    </div>
                </div>

            </div> {{-- /col-lg-4 --}}

        </div> {{-- /row --}}
    </form>

</div> {{-- /content --}}
@endsection

@push('scripts')
<script>
$(function() {

    // ══════════════════════════════════════════════════════════
    //  Quill Editor — Body
    // ══════════════════════════════════════════════════════════
    var quillBody = new Quill('#quill-body', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean']
            ]
        },
        placeholder: 'Write the notice body paragraph...'
    });

    // Sync Quill content to hidden input whenever text changes
    quillBody.on('text-change', function() {
        $('#quillBodyInput').val(quillBody.root.innerHTML);
    });

    // ══════════════════════════════════════════════════════════
    //  Addressees — Select / Deselect All
    // ══════════════════════════════════════════════════════════
    $('#selectAllBtn').on('click', function() {
        $('.addressee-cb').prop('checked', true);
    });
    $('#deselectAllBtn').on('click', function() {
        $('.addressee-cb').prop('checked', false);
    });

    // ══════════════════════════════════════════════════════════
    //  Agenda Items Repeater + Drag-to-Reorder
    // ══════════════════════════════════════════════════════════
    var canReorder = {{ ($canReorderAgenda ?? false) ? 'true' : 'false' }};

    $('#addAgendaBtn').on('click', function() {
        var count = $('#agendaContainer .agenda-item-row').length + 1;
        var html = '<div class="agenda-item-row d-flex align-items-center gap-2 mb-2">';
        if (canReorder) {
            html += '<span class="drag-handle" title="Drag to reorder"><i class="mdi mdi-drag-vertical"></i></span>';
        }
        html += '<span class="agenda-number">' + count + '</span>'
            + '<input type="text" name="agenda_items[]" class="form-control form-control-sm" placeholder="Agenda item" required>'
            + '<button type="button" class="btn btn-sm btn-outline-danger remove-agenda-btn" title="Remove">'
            + '<i class="mdi mdi-close"></i></button></div>';
        $('#agendaContainer').append(html);
    });

    $(document).on('click', '.remove-agenda-btn', function() {
        $(this).closest('.agenda-item-row').remove();
        renumberAgenda();
    });

    function renumberAgenda() {
        $('#agendaContainer .agenda-item-row').each(function(i) {
            $(this).find('.agenda-number').text(i + 1);
        });
    }

    // Drag-to-reorder using inline SortableJS
    if (canReorder) {
        (function() {
            // Minimal SortableJS implementation (inline, no CDN)
            var container = document.getElementById('agendaContainer');
            var dragged = null;
            var placeholder = null;

            function getDragAfterElement(y) {
                var elements = Array.from(container.querySelectorAll('.agenda-item-row:not(.dragging)'));
                return elements.reduce(function(closest, child) {
                    var box = child.getBoundingClientRect();
                    var offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    }
                    return closest;
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            container.addEventListener('mousedown', function(e) {
                var handle = e.target.closest('.drag-handle');
                if (!handle) return;
                e.preventDefault();
                dragged = handle.closest('.agenda-item-row');
                dragged.classList.add('dragging');
                dragged.style.opacity = '0.4';

                var moveHandler = function(e2) {
                    e2.preventDefault();
                    var afterElement = getDragAfterElement(e2.clientY);
                    if (afterElement == null) {
                        container.appendChild(dragged);
                    } else {
                        container.insertBefore(dragged, afterElement);
                    }
                };

                var upHandler = function() {
                    dragged.classList.remove('dragging');
                    dragged.style.opacity = '';
                    dragged = null;
                    document.removeEventListener('mousemove', moveHandler);
                    document.removeEventListener('mouseup', upHandler);
                    renumberAgenda();
                };

                document.addEventListener('mousemove', moveHandler);
                document.addEventListener('mouseup', upHandler);
            });

            // Touch support for mobile
            container.addEventListener('touchstart', function(e) {
                var handle = e.target.closest('.drag-handle');
                if (!handle) return;
                dragged = handle.closest('.agenda-item-row');
                dragged.classList.add('dragging');
                dragged.style.opacity = '0.4';

                var moveHandler = function(e2) {
                    e2.preventDefault();
                    var touch = e2.touches[0];
                    var afterElement = getDragAfterElement(touch.clientY);
                    if (afterElement == null) {
                        container.appendChild(dragged);
                    } else {
                        container.insertBefore(dragged, afterElement);
                    }
                };

                var endHandler = function() {
                    dragged.classList.remove('dragging');
                    dragged.style.opacity = '';
                    dragged = null;
                    document.removeEventListener('touchmove', moveHandler);
                    document.removeEventListener('touchend', endHandler);
                    renumberAgenda();
                };

                document.addEventListener('touchmove', moveHandler, { passive: false });
                document.addEventListener('touchend', endHandler);
            }, { passive: false });
        })();
    }

    // ══════════════════════════════════════════════════════════
    //  CC Entries Repeater
    // ══════════════════════════════════════════════════════════
    var ccIndex = {{ count($ccEntries ?? []) }};

    $('#addCcBtn').on('click', function() {
        $('#ccEmptyHint').hide();
        var html = '<div class="cc-entry-row d-flex align-items-center gap-2 mb-2">'
            + '<input type="text" name="cc[' + ccIndex + '][name]" class="form-control form-control-sm" placeholder="Name / Title">'
            + '<input type="text" name="cc[' + ccIndex + '][address]" class="form-control form-control-sm" placeholder="Address (optional)">'
            + '<button type="button" class="btn btn-sm btn-outline-danger remove-cc-btn" title="Remove">'
            + '<i class="mdi mdi-close"></i></button></div>';
        $('#ccContainer').append(html);
        ccIndex++;
    });

    $(document).on('click', '.remove-cc-btn', function() {
        $(this).closest('.cc-entry-row').remove();
        if ($('#ccContainer .cc-entry-row').length === 0) {
            $('#ccEmptyHint').show();
        }
    });

    // ══════════════════════════════════════════════════════════
    //  Form Submission Handlers
    // ══════════════════════════════════════════════════════════
    function syncAndSubmit(action) {
        // Sync Quill body to hidden input
        $('#quillBodyInput').val(quillBody.root.innerHTML);
        $('#formAction').val(action);
        $('#noticeForm').submit();
    }

    // Save Draft — immediate submit
    $('#saveDraftBtn').on('click', function() {
        syncAndSubmit('draft');
    });

    // Submit / Self-approve — SweetAlert confirmation
    $('#submitBtn').on('click', function() {
        var action = $(this).data('action');
        var isAudit = action === 'self_approve';

        Swal.fire({
            title: isAudit ? 'Approve & Sign Notice?' : 'Submit Notice to CEO?',
            text: isAudit
                ? 'As Deputy Director Internal Audit, this notice will be approved and signed immediately.'
                : 'The CEO will be notified to review and approve this board notice.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isAudit ? '#099139' : '{{ setting('primary_color', '#1a1a1a') }}',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isAudit ? 'Yes, Approve & Sign' : 'Yes, Submit',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                syncAndSubmit(action);
            }
        });
    });

});
</script>
@endpush
