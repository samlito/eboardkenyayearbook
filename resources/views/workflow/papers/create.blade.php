@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('workflow.meetings.show', $meeting) }}" class="btn btn-sm btn-light rounded-pill">
                    <i class="mdi mdi-arrow-left"></i> Back to Meeting
                </a>
            </div>
            @php
                $pageTitle = $paper
                    ? (in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']) ? 'Revise Board Paper' : 'Edit Board Paper')
                    : 'Prepare Board Paper';
            @endphp
            <h3 class="mb-0"><i class="mdi mdi-file-document-edit-outline text-primary me-1"></i> {{ $pageTitle }}</h3>
            <p class="text-muted mb-0">{{ $meeting->title }}</p>
        </div>
        <div>
            @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date, 'label' => 'Meeting'])
        </div>
    </div>

    {{-- Stage Indicator --}}
    @include('workflow.components.stage-indicator', ['currentStage' => 5, 'stageLabel' => 'Board Papers', 'meeting' => $meeting])

    <form action="{{ $paper ? route('workflow.papers.update', $paper) : route('workflow.papers.store', $meeting) }}"
          method="POST" enctype="multipart/form-data" id="paperForm">
        @csrf
        @if($paper)
            @method('PUT')
        @endif
        @if($assignment)
            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
        @endif

        {{-- Hidden inputs for each section (Quill syncs to these) --}}
        {{-- Pre-populated with existing values so sections that are never opened during edit are NOT wiped --}}
        @php
            $existingSectionsForHidden = $paper ? $paper->structured_content : [];
        @endphp
        @foreach($sectionLabels as $key => $label)
            @php
                $hiddenVal = old("section_{$key}", $existingSectionsForHidden[$key] ?? '');
                // For new papers: default committee_request
                if ($key === 'committee_request' && empty($hiddenVal) && !$paper && !empty($defaultCommitteeRequest)) {
                    $hiddenVal = '<p>' . nl2br(e($defaultCommitteeRequest)) . '</p>';
                }
            @endphp
            <input type="hidden" name="section_{{ $key }}" id="section-{{ $key }}" value="{{ $hiddenVal }}">
        @endforeach

        {{-- Rejection Feedback Banner --}}
        @if(isset($rejectionComment) && $rejectionComment)
            <div class="alert border-0 rounded-3 mb-3" style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border-left: 4px solid #dc2626 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 40px; height: 40px; background: #fee2e2;">
                        <i class="mdi mdi-alert-circle text-danger" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-danger fw-bold" style="font-size: 14px;">
                            {{ $paper->status === 'rejected_by_ceo' ? 'Rejected by CEO' : 'Rejected by Director' }} &mdash; Revision Required
                        </h6>
                        <p class="mb-1" style="font-size: 13px; color: #991b1b;">
                            <strong>{{ $rejectionComment->user->name ?? 'Reviewer' }}:</strong>
                            "{{ $rejectionComment->comment }}"
                        </p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($rejectionComment->created_at)->format('d M Y H:i') }} &mdash; Please address the feedback and resubmit.</small>
                    </div>
                </div>
            </div>
        @elseif($paper && in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']))
            <div class="alert alert-danger mb-3">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                <strong>{{ $paper->status === 'rejected_by_ceo' ? 'Rejected by CEO' : 'Rejected by Director' }}.</strong>
                Please revise and resubmit your board paper.
            </div>
        @endif

        <div class="row">
            {{-- ═══ Main Content ═══ --}}
            <div class="col-lg-9">

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ── Card 1: Paper Identity ── --}}
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-file-document-outline me-1 text-primary"></i> Paper Identity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Agenda Item No. <span class="text-danger">*</span></label>
                                <input type="text" name="agenda_item_number" class="form-control"
                                       value="{{ old('agenda_item_number', $paper->agenda_item_number ?? ($assignment->agenda_item_number ?? '')) }}"
                                       placeholder="e.g. 5.1" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Committee / Meeting</label>
                                <input type="text" name="committee_name" class="form-control bg-light"
                                       value="{{ old('committee_name', $paper->committee_name ?? $committeeName) }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Financial Year</label>
                                <input type="text" name="financial_year" class="form-control"
                                       value="{{ old('financial_year', $paper->financial_year ?? $financialYear) }}"
                                       placeholder="e.g. 2025/2026">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Paper Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg"
                                       value="{{ old('title', $paper->title ?? ($assignment->agenda_item_title ?? '')) }}"
                                       placeholder="Enter the board paper title" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Author / Department</label>
                                <input type="text" name="author_department" class="form-control"
                                       value="{{ old('author_department', $paper->author_department ?? $authorDepartment) }}"
                                       placeholder="Name / Department">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Reporting Period</label>
                                <input type="text" name="reporting_period" class="form-control"
                                       value="{{ old('reporting_period', $paper->reporting_period ?? $reportingPeriod) }}"
                                       placeholder="e.g. period ended 31 March 2026">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Paper Type</label>
                                <input type="text" name="paper_type" class="form-control"
                                       value="{{ old('paper_type', $paper->paper_type ?? '') }}"
                                       placeholder="e.g. For update on the status of...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Card 2: Structured Paper Sections ── --}}
                <div class="card">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="mdi mdi-text-box-outline me-1 text-primary"></i> Paper Content</h5>
                        <small class="text-muted"><span class="text-danger">*</span> = required section</small>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $existingSections = $paper ? $paper->structured_content : [];
                            $sectionIcons = [
                                'background'        => 'mdi-book-open-page-variant-outline',
                                'executive_summary'  => 'mdi-text-box-check-outline',
                                'body'              => 'mdi-file-document-multiple-outline',
                                'challenges'        => 'mdi-alert-outline',
                                'board_items'       => 'mdi-clipboard-list-outline',
                                'committee_request' => 'mdi-hand-pointing-right',
                            ];
                        @endphp

                        <div class="accordion" id="sectionAccordion">
                            @foreach($sectionLabels as $key => $label)
                                @php
                                    $isRequired = in_array($key, $requiredSections);
                                    $icon = $sectionIcons[$key] ?? 'mdi-text';
                                    $oldValue = old("section_{$key}", $existingSections[$key] ?? '');
                                    // Default pre-fill for committee_request
                                    if ($key === 'committee_request' && empty($oldValue) && !$paper && !empty($defaultCommitteeRequest)) {
                                        $oldValue = '<p>' . nl2br(e($defaultCommitteeRequest)) . '</p>';
                                    }
                                @endphp
                                <div class="accordion-item border-0 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <h2 class="accordion-header" id="heading-{{ $key }}">
                                        <button class="accordion-button {{ $loop->index > 0 ? 'collapsed' : '' }} fw-semibold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $key }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                style="font-size: 14px; color: {{ setting('primary_color', '#1a1a1a') }}; background: #fafafa;">
                                            <i class="mdi {{ $icon }} me-2" style="font-size: 18px;"></i>
                                            {{ $label }}
                                            @if($isRequired)
                                                <span class="text-danger ms-1">*</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary ms-2" style="font-size: 10px;">Optional</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $key }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         data-bs-parent="#sectionAccordion">
                                        <div class="accordion-body pt-2 pb-3 px-3">
                                            @if(isset($sectionGuidance[$key]))
                                                <p class="text-muted mb-2" style="font-size: 12px;">
                                                    <i class="mdi mdi-information-outline me-1"></i> {{ $sectionGuidance[$key] }}
                                                </p>
                                            @endif
                                            <div id="quill-{{ $key }}" class="quill-section-editor" style="min-height: {{ $key === 'body' ? '280px' : '160px' }};">{!! $oldValue !!}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ── Card 3: PDF Upload (Corporation Secretary & DD Internal Audit only) ── --}}
                @if(auth()->user()->hasRole('Corporation Secretary') || auth()->user()->hasRole('DD Internal Audit'))
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-file-pdf-box me-1 text-danger"></i> Upload Signed Board Paper (PDF)</h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-soft-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="mdi mdi-file-pdf-box text-danger" style="font-size: 24px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="file" class="form-control" accept=".pdf" id="signedPdfUpload">
                                    <small class="text-muted">Upload a pre-signed board paper. Max 20MB.</small>
                                </div>
                            </div>
                            @if($paper && $paper->file_path)
                                <div class="mt-2 p-2 bg-white rounded border">
                                    <i class="mdi mdi-file-pdf-box text-danger me-1"></i>
                                    <a href="{{ asset('storage/' . $paper->file_path) }}" target="_blank">Current file: {{ basename($paper->file_path) }}</a>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 p-3 rounded border" id="autoApproveSection" style="display: none; background: #f0fdf4; border-color: #86efac !important;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_ceo_signed" id="isCeoSigned" value="1">
                                <label class="form-check-label fw-semibold" for="isCeoSigned">
                                    <i class="mdi mdi-check-decagram text-success me-1"></i>
                                    This paper is already signed by the CEO
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1 ms-4">
                                When checked, this paper will be automatically approved and will not require CEO review.
                            </small>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('signedPdfUpload').addEventListener('change', function() {
                        document.getElementById('autoApproveSection').style.display = this.files.length > 0 ? 'block' : 'none';
                        if (this.files.length === 0) document.getElementById('isCeoSigned').checked = false;
                    });
                </script>
                @endif

                {{-- ── Card 4: Annexures ── --}}
                <div class="card">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="mdi mdi-attachment me-1"></i> Annexures</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addAnnexureBtn">
                            <i class="mdi mdi-plus me-1"></i> Add Annexure
                        </button>
                    </div>
                    <div class="card-body">
                        @if($paper && $paper->annexures && $paper->annexures->count())
                            <div class="mb-3">
                                <h6 class="text-muted mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Existing Annexures</h6>
                                @foreach($paper->annexures as $annexure)
                                    <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2 bg-light">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="mdi mdi-file-outline text-primary"></i>
                                            <div>
                                                <span class="fw-semibold" style="font-size: 13px;">{{ $annexure->title }}</span>
                                                <br><small class="text-muted">{{ basename($annexure->file_path) }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ asset('storage/' . $annexure->file_path) }}" target="_blank" class="btn btn-sm btn-light" title="Download">
                                                <i class="mdi mdi-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExistingAnnexure({{ $annexure->id }})" title="Remove">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div id="annexureContainer"></div>

                        <div id="noAnnexures" class="text-center text-muted py-3" style="{{ ($paper && $paper->annexures && $paper->annexures->count()) ? 'display:none;' : '' }}">
                            <i class="mdi mdi-paperclip" style="font-size: 32px; opacity: 0.3;"></i>
                            <p class="mb-0 mt-1">No annexures added yet. Click "Add Annexure" to attach supporting documents.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ Sidebar ═══ --}}
            <div class="col-lg-3">
                {{-- Assignment Info --}}
                @if($assignment)
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Your Assignment</h6>
                            <div class="mb-2">
                                <small class="text-muted">Agenda Item:</small>
                                <div class="fw-semibold">{{ $assignment->agenda_item_number ?? 'N/A' }}</div>
                            </div>
                            @if($assignment->agenda_item_title)
                                <div class="mb-2">
                                    <small class="text-muted">Topic:</small>
                                    <div>{{ $assignment->agenda_item_title }}</div>
                                </div>
                            @endif
                            <div>
                                <small class="text-muted">Assigned:</small>
                                <div>{{ \Carbon\Carbon::parse($assignment->created_at)->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Status --}}
                @if($paper)
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Paper Status</h6>
                            @php
                                $statusColors = [
                                    'draft' => 'warning',
                                    'submitted' => 'info',
                                    'under_review' => 'primary',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                ];
                                $color = $statusColors[$paper->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} px-3 py-2">{{ ucfirst(str_replace('_', ' ', $paper->status)) }}</span>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: 0.05em;">Actions</h6>
                        @php $isRevision = $paper && in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']); @endphp
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                                <i class="mdi mdi-content-save-outline me-1"></i> {{ $isRevision ? 'Save Revision' : 'Save Draft' }}
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary" onclick="return confirmSubmit()">
                                <i class="mdi mdi-send-check-outline me-1"></i> {{ $isRevision ? 'Resubmit for Review' : 'Submit for Review' }}
                            </button>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            @if($isRevision)
                                Address the reviewer's feedback and resubmit. You can save your revisions and return later.
                            @else
                                Submitting will send the paper to your Director for review. You can save a draft and return later.
                            @endif
                        </small>
                    </div>
                </div>

                {{-- Template Guide --}}
                <div class="card bg-soft-primary border-0">
                    <div class="card-body">
                        <h6 class="mb-2"><i class="mdi mdi-lightbulb-on-outline text-primary me-1"></i> Template Guide</h6>
                        <ul class="mb-0" style="font-size: 12px; padding-left: 16px;">
                            <li class="mb-1"><strong>1.0</strong> Background / Introduction</li>
                            <li class="mb-1"><strong>2.0</strong> Executive Summary</li>
                            <li class="mb-1"><strong>2.1</strong> Body (issues + workplan)</li>
                            <li class="mb-1"><strong>3.0</strong> Challenges Faced</li>
                            <li class="mb-1"><strong>4.0</strong> Board Items / Decisions</li>
                            <li><strong>5.0</strong> Request to the Committee</li>
                        </ul>
                        <hr class="my-2">
                        <small class="text-muted">Each section has a dedicated editor. Complete all required (<span class="text-danger">*</span>) sections before submitting.</small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* ── Section Editors ─────────────────────────────── */
.quill-section-editor {
    background: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 6px 6px;
    font-size: 14px;
    color: #111111;
    line-height: 1.7;
}
.quill-section-editor .ql-editor {
    min-height: 140px;
    font-size: 14px;
    color: #111111;
    padding: 12px 14px;
}
.quill-section-editor .ql-editor.ql-blank::before {
    font-size: 13px;
    color: #9ca3af;
    font-style: normal;
}
/* Accordion styling */
#sectionAccordion .accordion-button:not(.collapsed) {
    background: #f4f4f4;
    color: {{ setting('primary_color', '#1a1a1a') }};
    box-shadow: none;
}
#sectionAccordion .accordion-button:focus {
    box-shadow: none;
}
#sectionAccordion .accordion-button::after {
    filter: none;
}
</style>
@endpush

@push('scripts')
<script>
// ── Global state ───────────────────────────────────────
var sectionKeys = @json(array_keys($sectionLabels));
var editors = {};

// ── Quill toolbar config (shared) ──────────────────────
var quillToolbar = [
    [{ 'header': [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'indent': '-1'}, { 'indent': '+1' }],
    [{ 'align': [] }],
    ['blockquote'],
    ['link'],
    ['clean']
];

// ── Lazy-init: create a Quill editor only when needed ──
function initQuillEditor(key) {
    if (editors[key]) return editors[key]; // already initialised
    var container = document.getElementById('quill-' + key);
    if (!container) return null;

    editors[key] = new Quill('#quill-' + key, {
        theme: 'snow',
        modules: { toolbar: quillToolbar },
        placeholder: 'Enter content for this section...'
    });
    return editors[key];
}

// ── Helper: get clean HTML from a Quill instance ───────
function getCleanHtml(quill) {
    var html = quill.root.innerHTML;
    if (!html || html === '<p><br></p>' || html.replace(/<[^>]*>/g, '').trim() === '') {
        return '';
    }
    return html;
}

// ── Sync ALL editors to hidden inputs ──────────────────
// Force-initialise any lazy editor so we always capture every section.
function syncAllEditors() {
    sectionKeys.forEach(function(key) {
        // Force-init editors that were never opened by the user;
        // Quill will read the existing HTML from the div, preserving content.
        if (!editors[key]) {
            initQuillEditor(key);
        }
        if (editors[key]) {
            document.getElementById('section-' + key).value = getCleanHtml(editors[key]);
        }
        // Safety: if Quill somehow failed to init (no container), the hidden
        // input retains its server-pre-populated value — no data loss.
    });
}

$(document).ready(function() {

    // ── Init ONLY the first section (it's open by default) ──
    initQuillEditor(sectionKeys[0]);

    // ── Lazy-init when an accordion panel opens ─────────
    $('#sectionAccordion').on('shown.bs.collapse', function(e) {
        var panelId = e.target.id; // e.g. "collapse-executive_summary"
        var key = panelId.replace('collapse-', '');
        if (sectionKeys.indexOf(key) !== -1) {
            initQuillEditor(key);
        }
    });

    // ── Sync all editors to hidden inputs on submit ─────
    $('#paperForm').on('submit', function() {
        syncAllEditors();
    });

    // ── Annexure repeater ───────────────────────────────
    var annexureIndex = 0;
    $('#addAnnexureBtn').on('click', function() {
        $('#noAnnexures').hide();
        var row = `
            <div class="annexure-row border rounded p-3 mb-2 bg-light" id="annexure-row-${annexureIndex}">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <h6 class="mb-0" style="font-size: 13px;"><i class="mdi mdi-attachment me-1"></i> Annexure ${annexureIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAnnexure(${annexureIndex})">
                        <i class="mdi mdi-close"></i> Remove
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label" style="font-size: 12px;">Annexure Title</label>
                        <input type="text" name="annexures[${annexureIndex}][title]" class="form-control form-control-sm" placeholder="e.g. Budget Breakdown" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" style="font-size: 12px;">File</label>
                        <input type="file" name="annexures[${annexureIndex}][file]" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.png" required>
                        <small class="text-muted">PDF, Word, Excel, PowerPoint or images. Max 10MB.</small>
                    </div>
                </div>
            </div>`;
        $('#annexureContainer').append(row);
        annexureIndex++;
    });
});

function removeAnnexure(index) {
    $('#annexure-row-' + index).fadeOut(200, function() { $(this).remove(); });
}

function removeExistingAnnexure(annexureId) {
    Swal.fire({
        title: 'Remove Annexure?',
        text: 'This will permanently delete the annexure file.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/workflow/board-papers/{{ $paper->id ?? 0 }}/annexures/${annexureId}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() { location.reload(); },
                error: function() { toastr.error('Failed to remove annexure.'); }
            });
        }
    });
}

function confirmSubmit() {
    event.preventDefault();
    Swal.fire({
        title: 'Submit Board Paper?',
        text: 'This will send the paper to your Director for review. You will not be able to edit it once submitted.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        confirmButtonText: 'Yes, submit for review'
    }).then((result) => {
        if (result.isConfirmed) {
            syncAllEditors();
            $('#paperForm').find('input[name="action"]').remove();
            $('#paperForm').append('<input type="hidden" name="action" value="submit">');
            $('#paperForm').submit();
        }
    });
    return false;
}
</script>
@endpush
