@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="twwda-back-btn">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0 twwda-page-title">Internal Notice</h4>
                        </div>
                        <p class="twwda-page-subtitle ms-5">
                            {{ $meeting->title }} &middot; {{ $meeting->reference_number }}
                        </p>
                    </div>
                    <div>
                        @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
                    </div>
                </div>
            </div>
        </div>

        {{-- Stage Indicator --}}
        @include('workflow.components.stage-indicator', ['currentStage' => 2, 'stageLabel' => 'Internal Notice', 'meeting' => $meeting])

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 16px; border-radius: 10px;">
                <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 16px; border-radius: 10px;">
                <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Rejection Alert --}}
        @if($notice && $notice->status === 'rejected' && $comments && $comments->count() > 0)
            <div class="twwda-rejection-alert mb-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="twwda-rejection-icon">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="twwda-rejection-title">Notice Rejected</h6>
                        <p class="twwda-rejection-subtitle">Please review the comments below and resubmit your notice.</p>
                        <div class="twwda-rejection-comments">
                            @foreach($comments->sortByDesc('created_at')->take(3) as $comment)
                                <div class="{{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                    <p class="mb-1" style="font-size: 14px; color: #495057;">{{ $comment->comment }}</p>
                                    <small class="text-muted">
                                        {{ $comment->user->name ?? 'CEO' }} &middot; {{ $comment->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="twwda-card">
            <div class="twwda-card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-file-document-edit-outline me-2"></i>
                    {{ $notice ? 'Edit Internal Notice' : 'Create Internal Notice' }}
                </h5>
            </div>
            <div class="twwda-card-body">
                <form id="internalNoticeForm"
                      action="{{ $notice ? route('workflow.internal-notice.update', $notice->id) : route('workflow.internal-notice.store', $meeting->id) }}"
                      method="POST">
                    @csrf
                    @if($notice)
                        @method('PUT')
                    @endif

                    <div class="row g-4">
                        {{-- Reference Number --}}
                        <div class="col-md-6">
                            <label for="reference_number" class="twwda-label">
                                Reference Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control twwda-input @error('reference_number') is-invalid @enderror"
                                   id="reference_number" name="reference_number"
                                   value="{{ old('reference_number', $notice->reference_number ?? '') }}"
                                   placeholder="e.g. {{ setting('institution_acronym', 'ORG') }}/TECH/PLAN/STRA VOL.I">
                            @error('reference_number')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reference Date --}}
                        <div class="col-md-6">
                            <label for="reference_date" class="twwda-label">
                                Reference Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control twwda-input @error('reference_date') is-invalid @enderror"
                                   id="reference_date" name="reference_date"
                                   value="{{ old('reference_date', $notice->reference_date ?? now()->format('Y-m-d')) }}">
                            @error('reference_date')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="col-12">
                            <label for="subject" class="twwda-label">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control twwda-input @error('subject') is-invalid @enderror"
                                   id="subject" name="subject"
                                   value="{{ old('subject', $notice->subject ?? '') }}"
                                   placeholder="Enter notice subject">
                            @error('subject')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Meeting Venue --}}
                        <div class="col-md-4">
                            <label for="meeting_venue" class="twwda-label">Meeting Venue</label>
                            <input type="text" class="form-control twwda-input @error('meeting_venue') is-invalid @enderror"
                                   id="meeting_venue" name="meeting_venue"
                                   value="{{ old('meeting_venue', $notice->meeting_venue ?? $meeting->venue ?? '') }}"
                                   placeholder="Enter meeting venue">
                            @error('meeting_venue')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Meeting Date --}}
                        <div class="col-md-4">
                            <label for="meeting_date" class="twwda-label">Meeting Date</label>
                            <input type="date" class="form-control twwda-input @error('meeting_date') is-invalid @enderror"
                                   id="meeting_date" name="meeting_date"
                                   value="{{ old('meeting_date', $notice->meeting_date ?? $meeting->meeting_date ?? '') }}">
                            @error('meeting_date')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Meeting Time --}}
                        <div class="col-md-4">
                            <label for="meeting_time" class="twwda-label">Meeting Time</label>
                            <input type="time" class="form-control twwda-input @error('meeting_time') is-invalid @enderror"
                                   id="meeting_time" name="meeting_time"
                                   value="{{ old('meeting_time', $notice->meeting_time ?? $meeting->meeting_time ?? '') }}">
                            @error('meeting_time')
                                <div class="invalid-feedback" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Body (Rich Text Editor) --}}
                        <div class="col-12">
                            <label for="body" class="twwda-label">
                                Body <span class="text-danger">*</span>
                            </label>
                            <div id="quill-wrapper">
                                <div id="quill-editor"></div>
                            </div>
                            <input type="hidden" name="body" id="body-input">
                            @error('body')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Agenda Items (Dynamic Repeater) --}}
                        <div class="col-12">
                            <label class="twwda-label">
                                Agenda Items <span class="text-danger">*</span>
                            </label>
                            <div id="agenda-container">
                                @if($notice && $notice->agenda_items)
                                    @foreach((is_array($notice->agenda_items) ? $notice->agenda_items : json_decode($notice->agenda_items, true)) ?? [] as $index => $item)
                                        <div class="agenda-item d-flex align-items-start gap-2 mb-2">
                                            <span class="agenda-number flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white"
                                                  style="width: 32px; height: 32px; font-size: 13px; font-weight: 700; margin-top: 6px; background: {{ setting('primary_color', '#1a1a1a') }};">
                                                {{ $index + 1 }}
                                            </span>
                                            <input type="text" class="form-control twwda-input" name="agenda_items[]"
                                                   value="{{ $item }}" placeholder="Enter agenda item">
                                            <button type="button" class="btn btn-sm btn-danger remove-agenda flex-shrink-0" style="margin-top: 6px; border-radius: 8px; padding: 6px 10px;">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="agenda-item d-flex align-items-start gap-2 mb-2">
                                        <span class="agenda-number flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white"
                                              style="width: 32px; height: 32px; font-size: 13px; font-weight: 700; margin-top: 6px; background: {{ setting('primary_color', '#1a1a1a') }};">
                                            1
                                        </span>
                                        <input type="text" class="form-control twwda-input" name="agenda_items[]"
                                               placeholder="Enter agenda item">
                                        <button type="button" class="btn btn-sm btn-danger remove-agenda flex-shrink-0" style="margin-top: 6px; border-radius: 8px; padding: 6px 10px;">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn twwda-btn-outline mt-2" id="add-agenda-btn" style="padding: 8px 16px; font-size: 13px;">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Add Agenda Item
                            </button>
                            @error('agenda_items')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                            @error('agenda_items.*')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <hr class="my-4" style="border-color: #e9ecef;">

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="btn twwda-btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Cancel
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="draft" class="btn twwda-btn-secondary">
                                <i class="mdi mdi-content-save-outline me-1"></i> Save Draft
                            </button>
                            @if($notice && $notice->status === 'rejected')
                                <button type="submit" name="action" value="resubmit" class="btn twwda-btn-primary"
                                        formaction="{{ route('workflow.internal-notice.resubmit', $notice->id) }}">
                                    <i class="mdi mdi-send-outline me-1"></i> Resubmit to CEO
                                </button>
                            @else
                                <button type="submit" name="action" value="submit" class="btn twwda-btn-primary"
                                        onclick="return confirm('Are you sure you want to submit this notice to the CEO for approval?');">
                                    <i class="mdi mdi-send-outline me-1"></i> Submit to CEO
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
/* ── TWWDA Internal Notice Form Styles ──────────────────────────────── */
.twwda-page-title {
    font-size: 28px;
    font-weight: 700;
    color: #0d0d0d;
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
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f0f2f5;
    color: #111111;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 22px;
}
.twwda-back-btn:hover { background: {{ setting('primary_color', '#1a1a1a') }}; color: #fff; }

/* ── Card ─────────────────────────────────────── */
.twwda-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    overflow: hidden;
}
.twwda-card-header {
    background: #fbfbfb;
    border-bottom: 1px solid #e9ecef;
    padding: 18px 28px;
}
.twwda-card-header h5 {
    font-size: 20px;
    font-weight: 700;
    color: #0d0d0d;
}
.twwda-card-body {
    padding: 28px;
}

/* ── Form Elements ────────────────────────────── */
.twwda-label {
    display: block;
    font-size: 16px;
    font-weight: 600;
    color: #111111;
    margin-bottom: 6px;
}
.twwda-input {
    font-size: 16px !important;
    padding: 11px 14px !important;
    border-radius: 8px !important;
    border: 1.5px solid #dee2e6 !important;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.twwda-input:focus {
    border-color: {{ setting('primary_color', '#1a1a1a') }} !important;
    box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1) !important;
}

/* ── Quill Editor ─────────────────────────────── */
#quill-wrapper { overflow: hidden; border-radius: 10px; border: 1.5px solid #dee2e6; }
#quill-wrapper .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #e9ecef !important; background: #fbfbfb; padding: 10px 12px !important; }
#quill-wrapper .ql-container.ql-snow { border: none !important; font-size: 16px !important; background: #fff; }
.ql-editor { min-height: 220px; font-size: 16px !important; line-height: 1.7; }
.ql-editor.ql-blank::before { font-style: normal !important; color: #9ca3af !important; font-size: 16px !important; }

/* ── Buttons ──────────────────────────────────── */
.twwda-btn-primary {
    background: {{ setting('primary_color', '#1a1a1a') }};
    color: #fff;
    border: none;
    padding: 11px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
.twwda-btn-primary:hover { background: #2b2b2b; color: #fff; }
.twwda-btn-secondary {
    background: #f0f2f5;
    color: #111111;
    border: 1px solid #dee2e6;
    padding: 11px 24px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
}
.twwda-btn-secondary:hover { background: #e2e6ea; color: #0d0d0d; }
.twwda-btn-outline {
    background: #fff;
    color: {{ setting('primary_color', '#1a1a1a') }};
    border: 1.5px solid {{ setting('primary_color', '#1a1a1a') }};
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
.twwda-btn-outline:hover { background: {{ setting('primary_color', '#1a1a1a') }}; color: #fff; }

/* ── Rejection Alert ──────────────────────────── */
.twwda-rejection-alert {
    background: #fff;
    border: 1px solid #f5c6cb;
    border-left: 4px solid #dc3545;
    border-radius: 10px;
    padding: 20px 24px;
}
.twwda-rejection-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #fef2f2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #dc3545;
    flex-shrink: 0;
}
.twwda-rejection-title {
    font-size: 18px;
    font-weight: 700;
    color: #b91c1c;
    margin-bottom: 4px;
}
.twwda-rejection-subtitle {
    font-size: 16px;
    color: #4b5563;
    margin-bottom: 12px;
}
.twwda-rejection-comments {
    background: #fef8f8;
    border-radius: 8px;
    padding: 16px;
    border: 1px solid #f5c6cb;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill Editor
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Enter the notice body content...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Pre-fill Quill with existing content
    @if($notice && $notice->body)
        quill.root.innerHTML = {!! json_encode($notice->body) !!};
    @endif

    // Sync Quill to hidden input on form submit
    document.getElementById('internalNoticeForm').addEventListener('submit', function() {
        document.getElementById('body-input').value = quill.root.innerHTML;
    });

    // Agenda Items Repeater
    var agendaContainer = document.getElementById('agenda-container');
    var addAgendaBtn = document.getElementById('add-agenda-btn');

    function renumberAgenda() {
        var items = agendaContainer.querySelectorAll('.agenda-item');
        items.forEach(function(item, idx) {
            item.querySelector('.agenda-number').textContent = idx + 1;
        });
    }

    addAgendaBtn.addEventListener('click', function() {
        var count = agendaContainer.querySelectorAll('.agenda-item').length + 1;
        var newItem = document.createElement('div');
        newItem.className = 'agenda-item d-flex align-items-start gap-2 mb-2';
        newItem.innerHTML = '<span class="agenda-number flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 32px; height: 32px; font-size: 13px; font-weight: 700; margin-top: 6px; background: {{ setting('primary_color', '#1a1a1a') }};">' + count + '</span>' +
            '<input type="text" class="form-control twwda-input" name="agenda_items[]" placeholder="Enter agenda item">' +
            '<button type="button" class="btn btn-sm btn-danger remove-agenda flex-shrink-0" style="margin-top: 6px; border-radius: 8px; padding: 6px 10px;"><i class="mdi mdi-close"></i></button>';
        agendaContainer.appendChild(newItem);
        newItem.querySelector('input').focus();
    });

    agendaContainer.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.remove-agenda');
        if (removeBtn) {
            var items = agendaContainer.querySelectorAll('.agenda-item');
            if (items.length > 1) {
                removeBtn.closest('.agenda-item').remove();
                renumberAgenda();
            }
        }
    });
});
</script>
@endpush
