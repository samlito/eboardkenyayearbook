@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        $meetingDate = optional($paper->meeting)->meeting_date
            ? Carbon::parse($paper->meeting->meeting_date)->isoFormat('dddd, MMMM D, YYYY')
            : null;
        $committeeName = optional(optional($paper->meeting)->boardCommittee)->name ?? 'Board Committee';
        $sectionLabels = \App\Models\Twwda\TwwdaBoardPaper::sectionLabels();
        $noteColors = [
            'yellow' => '#fff9c4',
            'blue'   => '#dbdbdb',
            'green'  => '#c8e6c9',
            'pink'   => '#f8bbd0',
        ];
    @endphp

    <style>
        :root {
            --brand: {{ setting('primary_color', '#1a1a1a') }};
            --brand-soft: #fdecee;
            --green: #099139;
            --green-soft: #e8f5ec;
        }
        .brand-gradient { background: linear-gradient(135deg, var(--brand) 0%, #3a3a3a 100%); }
        .text-brand { color: var(--brand) !important; }
        .bg-brand { background-color: var(--brand) !important; }
        .bg-brand-soft { background-color: var(--brand-soft) !important; }
        .text-green { color: var(--green) !important; }
        .bg-green-soft { background-color: var(--green-soft) !important; }
        .card-portal {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }
        .chip {
            background: var(--brand-soft);
            color: var(--brand);
            border-radius: 999px;
            padding: .3rem .75rem;
            font-weight: 600;
            font-size: .85rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .chip-green {
            background: var(--green-soft);
            color: var(--green);
        }
        .paper-number-badge {
            background: var(--brand);
            color: #fff;
            border-radius: 8px;
            padding: .35rem .85rem;
            font-weight: 700;
            font-size: .9rem;
            letter-spacing: .5px;
        }
        .ceo-badge {
            background: var(--green-soft);
            color: var(--green);
            border-radius: 999px;
            padding: .3rem .75rem;
            font-weight: 600;
            font-size: .85rem;
        }

        /* Reading panel */
        .reading-panel {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 2rem 2.5rem;
            line-height: 1.8;
            font-size: 1.02rem;
            color: #333;
        }
        .reading-panel h1, .reading-panel h2, .reading-panel h3,
        .reading-panel h4, .reading-panel h5, .reading-panel h6 {
            color: var(--brand);
            margin-top: 1.5rem;
            margin-bottom: .75rem;
        }
        .reading-panel table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .reading-panel table th,
        .reading-panel table td {
            border: 1px solid #dee2e6;
            padding: .5rem .75rem;
            font-size: .95rem;
        }
        .reading-panel table thead th {
            background: var(--brand-soft);
            color: var(--brand);
            font-weight: 600;
        }
        .reading-panel ul, .reading-panel ol {
            padding-left: 1.5rem;
        }

        /* Annexure list */
        .annexure-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: .75rem 1rem;
            transition: border-color .2s;
        }
        .annexure-item:hover {
            border-color: var(--brand);
        }

        /* Sticky notes sidebar */
        .sticky-sidebar {
            position: sticky;
            top: 80px;
        }
        .note-item {
            border-radius: 10px;
            padding: .75rem 1rem;
            margin-bottom: .5rem;
            position: relative;
            font-size: .92rem;
            line-height: 1.5;
            min-height: 44px;
        }
        .note-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid rgba(0,0,0,.1);
        }
        .note-delete-btn {
            position: absolute;
            top: 6px;
            right: 8px;
            background: none;
            border: none;
            color: #999;
            font-size: 1.1rem;
            padding: 4px;
            cursor: pointer;
            min-height: 36px;
            min-width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all .2s;
        }
        .note-delete-btn:hover {
            background: rgba(0,0,0,.06);
            color: #dc3545;
        }
        .color-selector .color-option {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .color-selector .color-option:hover {
            transform: scale(1.15);
        }
        .color-selector .color-option.active {
            border-color: var(--brand);
            box-shadow: 0 0 0 2px rgba(200, 16, 46,.25);
        }

        /* Info card */
        .info-list .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .45rem 0;
            border-bottom: 1px dashed #f4f4f4;
            font-size: .92rem;
        }
        .info-list .info-row:last-child { border-bottom: 0; }
        .info-list .info-label { color: #6c757d; }
        .info-list .info-value { font-weight: 600; color: #333; text-align: right; }

        /* Buttons */
        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            border-radius: 10px;
            min-height: 44px;
            font-weight: 600;
        }
        .btn-brand:hover { background: #2b2b2b; color: #fff; }
        .btn-green {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
            border-radius: 10px;
            min-height: 44px;
            font-weight: 600;
        }
        .btn-green:hover { background: #077a2e; color: #fff; }
        .btn-back {
            min-height: 44px;
            min-width: 44px;
            font-size: 1rem;
            border-radius: 10px;
        }

        /* iPad touch-friendly */
        @media (min-width: 768px) and (max-width: 1200px) {
            .reading-panel { padding: 1.5rem; font-size: 1.05rem; }
            .note-item { padding: 1rem; }
            .btn-brand, .btn-green { padding: .6rem 1.2rem; font-size: 1rem; }
        }
        @media (max-width: 991px) {
            .sticky-sidebar { position: relative; top: auto; }
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Back link --}}
            <div class="mb-3">
                <a href="{{ route('board.portal.papers') }}" class="btn btn-outline-secondary btn-back">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Papers
                </a>
            </div>

            <div class="row g-4">

                {{-- ════════════════════════════════════════════════════
                    LEFT COLUMN: Paper content
                ════════════════════════════════════════════════════ --}}
                <div class="col-lg-8">

                    {{-- Paper header --}}
                    <div class="card card-portal mb-3">
                        <div class="card-body py-4">
                            <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
                                <span class="paper-number-badge">{{ $paper->paper_number ?? 'N/A' }}</span>
                                @if($paper->agenda_item_number)
                                    <span class="chip">Agenda Item {{ $paper->agenda_item_number }}</span>
                                @endif
                                @if($paper->ceo_signature)
                                    <span class="ceo-badge">
                                        <i class="mdi mdi-check-decagram me-1"></i> CEO Signed
                                    </span>
                                @endif
                            </div>

                            <h3 class="fw-bold mb-2" style="font-size:1.45rem; color:#141414;">
                                {{ $paper->title }}
                            </h3>

                            <div class="d-flex flex-wrap gap-3 text-muted" style="font-size:.92rem;">
                                <span>
                                    <i class="mdi mdi-account-outline me-1"></i>{{ optional($paper->author)->name ?? 'Unknown Author' }}
                                </span>
                                <span>
                                    <i class="mdi mdi-account-group me-1"></i>{{ $committeeName }}
                                </span>
                                @if($meetingDate)
                                    <span>
                                        <i class="mdi mdi-calendar me-1"></i>{{ $meetingDate }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Paper content --}}
                    <div class="card card-portal mb-3">
                        <div class="card-body p-0">
                            <div class="reading-panel">
                                {!! $paper->rendered_content !!}
                            </div>
                        </div>
                    </div>

                    {{-- Download original document --}}
                    @if($paper->file_path)
                        <div class="card card-portal mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="mdi mdi-file-pdf-box text-danger me-2" style="font-size:1.5rem;"></i>
                                    <span class="fw-semibold" style="font-size:1rem;">Original Document</span>
                                </div>
                                <a href="{{ route('workflow.papers.pdf', $paper) }}" class="btn btn-green" target="_blank">
                                    <i class="mdi mdi-download me-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Annexures --}}
                    @if($paper->annexures->isNotEmpty())
                        <div class="card card-portal mb-3">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="mdi mdi-paperclip me-1 text-brand"></i>
                                    Annexures
                                    <span class="badge bg-brand-soft text-brand ms-2" style="font-size:.8rem;">
                                        {{ $paper->annexures->count() }}
                                    </span>
                                </h5>

                                <div class="d-flex flex-column gap-2">
                                    @foreach($paper->annexures as $annexure)
                                        <div class="annexure-item d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                @php
                                                    $fileIcon = 'mdi-file-outline';
                                                    $ext = strtolower(pathinfo($annexure->file_name ?? '', PATHINFO_EXTENSION));
                                                    if(in_array($ext, ['pdf'])) $fileIcon = 'mdi-file-pdf-box';
                                                    elseif(in_array($ext, ['doc','docx'])) $fileIcon = 'mdi-file-word-box';
                                                    elseif(in_array($ext, ['xls','xlsx'])) $fileIcon = 'mdi-file-excel-box';
                                                    elseif(in_array($ext, ['ppt','pptx'])) $fileIcon = 'mdi-file-powerpoint-box';
                                                    elseif(in_array($ext, ['png','jpg','jpeg','gif'])) $fileIcon = 'mdi-file-image';
                                                @endphp
                                                <i class="mdi {{ $fileIcon }} text-brand" style="font-size:1.5rem;"></i>
                                                <div>
                                                    <div class="fw-semibold" style="font-size:.95rem;">{{ $annexure->title }}</div>
                                                    <div class="text-muted" style="font-size:.8rem;">
                                                        {{ $annexure->file_name ?? 'Document' }}
                                                        @if($annexure->file_size)
                                                            <span class="ms-1">({{ number_format($annexure->file_size / 1024, 0) }} KB)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($annexure->file_path)
                                                <a href="{{ asset('storage/' . $annexure->file_path) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-primary" style="min-height:40px; min-width:40px; border-radius:8px;">
                                                    <i class="mdi mdi-download me-1"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ════════════════════════════════════════════════════
                    RIGHT COLUMN: Sidebar
                ════════════════════════════════════════════════════ --}}
                <div class="col-lg-4">
                    <div class="sticky-sidebar">

                        {{-- My Notes panel --}}
                        <div class="card card-portal mb-3">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="mdi mdi-note-multiple me-1 text-brand"></i> My Notes
                                </h5>

                                {{-- Existing notes list --}}
                                <div id="notes-list">
                                    @forelse($myNotes as $note)
                                        <div class="note-item" id="note-{{ $note->id }}"
                                             style="background: {{ $noteColors[$note->color] ?? $noteColors['yellow'] }};">
                                            <button class="note-delete-btn" onclick="deleteNote({{ $note->id }})" title="Delete note">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                            <div class="pe-4" style="white-space:pre-wrap;">{{ $note->content }}</div>
                                            <div class="d-flex align-items-center gap-2 mt-2" style="font-size:.75rem; color:#888;">
                                                <span class="note-color-dot" style="background:{{ $noteColors[$note->color] ?? $noteColors['yellow'] }};"></span>
                                                {{ $note->created_at ? Carbon::parse($note->created_at)->diffForHumans() : '' }}
                                            </div>
                                        </div>
                                    @empty
                                        <div id="no-notes-msg" class="text-muted text-center py-3" style="font-size:.92rem;">
                                            <i class="mdi mdi-note-off-outline d-block mb-1" style="font-size:1.5rem;"></i>
                                            No notes yet. Add one below.
                                        </div>
                                    @endforelse
                                </div>

                                <hr class="my-3">

                                {{-- Add Note form --}}
                                <h6 class="fw-semibold mb-2" style="font-size:.92rem;">Add a Note</h6>
                                <div id="note-form">
                                    <textarea id="note-content" class="form-control mb-2" rows="3"
                                              placeholder="Type your note here..." style="border-radius:10px; font-size:.95rem; min-height:80px;"></textarea>

                                    <div class="color-selector d-flex gap-2 mb-3">
                                        @foreach($noteColors as $colorName => $colorHex)
                                            <div class="color-option {{ $colorName === 'yellow' ? 'active' : '' }}"
                                                 data-color="{{ $colorName }}"
                                                 style="background:{{ $colorHex }};"
                                                 title="{{ ucfirst($colorName) }}">
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" id="save-note-btn" class="btn btn-brand w-100" onclick="saveNote()">
                                        <i class="mdi mdi-content-save me-1"></i> Save Note
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Paper info card --}}
                        <div class="card card-portal mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="mdi mdi-information-outline me-1 text-brand"></i> Paper Details
                                </h6>
                                <div class="info-list">
                                    @if($paper->paper_type)
                                        <div class="info-row">
                                            <span class="info-label">Paper Type</span>
                                            <span class="info-value">{{ $paper->paper_type }}</span>
                                        </div>
                                    @endif
                                    @if($paper->financial_year)
                                        <div class="info-row">
                                            <span class="info-label">Financial Year</span>
                                            <span class="info-value">{{ $paper->financial_year }}</span>
                                        </div>
                                    @endif
                                    @if($paper->reporting_period)
                                        <div class="info-row">
                                            <span class="info-label">Reporting Period</span>
                                            <span class="info-value">{{ $paper->reporting_period }}</span>
                                        </div>
                                    @endif
                                    @if($paper->author_department)
                                        <div class="info-row">
                                            <span class="info-label">Department</span>
                                            <span class="info-value">{{ $paper->author_department }}</span>
                                        </div>
                                    @endif
                                    @if($paper->committee_name)
                                        <div class="info-row">
                                            <span class="info-label">Committee</span>
                                            <span class="info-value">{{ $paper->committee_name }}</span>
                                        </div>
                                    @endif
                                    <div class="info-row">
                                        <span class="info-label">Author</span>
                                        <span class="info-value">{{ optional($paper->author)->name ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Meeting context card --}}
                        <div class="card card-portal mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="mdi mdi-calendar-text me-1 text-brand"></i> Meeting Context
                                </h6>
                                <div class="info-list">
                                    <div class="info-row">
                                        <span class="info-label">Committee</span>
                                        <span class="info-value">{{ $committeeName }}</span>
                                    </div>
                                    @if(optional($paper->meeting)->title)
                                        <div class="info-row">
                                            <span class="info-label">Meeting</span>
                                            <span class="info-value">{{ $paper->meeting->title }}</span>
                                        </div>
                                    @endif
                                    @if($meetingDate)
                                        <div class="info-row">
                                            <span class="info-label">Date</span>
                                            <span class="info-value">{{ $meetingDate }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            var paperId = {{ $paper->id }};
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var selectedColor = 'yellow';

            // Note color definitions for JS
            var noteColors = @json($noteColors);

            // Color selector
            $('.color-selector .color-option').on('click', function() {
                $('.color-selector .color-option').removeClass('active');
                $(this).addClass('active');
                selectedColor = $(this).data('color');
            });

            // Save note
            window.saveNote = function() {
                var content = $.trim($('#note-content').val());
                if (!content) {
                    toastr.warning('Please enter a note before saving.');
                    return;
                }

                var $btn = $('#save-note-btn');
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Saving...');

                $.ajax({
                    url: '{{ route("board.portal.paper.note.store", $paper->id) }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: {
                        content: content,
                        color: selectedColor
                    },
                    success: function(res) {
                        if (res.success && res.note) {
                            // Remove empty state message
                            $('#no-notes-msg').remove();

                            var bgColor = noteColors[res.note.color] || noteColors['yellow'];

                            var noteHtml = '<div class="note-item" id="note-' + res.note.id + '" style="background:' + bgColor + ';">' +
                                '<button class="note-delete-btn" onclick="deleteNote(' + res.note.id + ')" title="Delete note">' +
                                    '<i class="mdi mdi-close"></i>' +
                                '</button>' +
                                '<div class="pe-4" style="white-space:pre-wrap;">' + $('<div>').text(res.note.content).html() + '</div>' +
                                '<div class="d-flex align-items-center gap-2 mt-2" style="font-size:.75rem; color:#888;">' +
                                    '<span class="note-color-dot" style="background:' + bgColor + ';"></span>' +
                                    'just now' +
                                '</div>' +
                            '</div>';

                            $('#notes-list').append(noteHtml);
                            $('#note-content').val('');
                            toastr.success('Note saved successfully.');
                        } else {
                            toastr.error(res.message || 'Failed to save note.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to save note. Please try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i> Save Note');
                    }
                });
            };

            // Delete note
            window.deleteNote = function(noteId) {
                Swal.fire({
                    title: 'Delete Note?',
                    text: 'This note will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/board/portal/notes/' + noteId,
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            success: function(res) {
                                if (res.success) {
                                    $('#note-' + noteId).fadeOut(300, function() {
                                        $(this).remove();

                                        // Show empty state if no notes remain
                                        if ($('#notes-list .note-item').length === 0) {
                                            $('#notes-list').html(
                                                '<div id="no-notes-msg" class="text-muted text-center py-3" style="font-size:.92rem;">' +
                                                    '<i class="mdi mdi-note-off-outline d-block mb-1" style="font-size:1.5rem;"></i>' +
                                                    'No notes yet. Add one below.' +
                                                '</div>'
                                            );
                                        }
                                    });
                                    toastr.success('Note deleted.');
                                } else {
                                    toastr.error(res.message || 'Failed to delete note.');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 403) {
                                    toastr.error('You are not authorized to delete this note.');
                                } else {
                                    toastr.error('Failed to delete note. Please try again.');
                                }
                            }
                        });
                    }
                });
            };
        })();
    </script>
    @endpush
@endsection
