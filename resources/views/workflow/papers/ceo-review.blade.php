@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('workflow.ceo.papers') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Papers
            </a>
            <h3 class="mb-0">
                <i class="mdi mdi-shield-check-outline text-primary me-1"></i>
                {{ !empty($isAuditCommittee) && $isAuditCommittee ? 'DDIA' : 'CEO' }} Board Paper Review
            </h3>
            <p class="text-muted mb-0">{{ $paper->paper_number }} &mdash; {{ $paper->title }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @php
                $statusColors = [
                    'director_approved' => 'info',
                    'ceo_approved' => 'success',
                    'approved' => 'success',
                    'rejected' => 'danger',
                ];
                $color = $statusColors[$paper->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }} px-3 py-2">{{ ucfirst(str_replace('_', ' ', $paper->status)) }}</span>
            @if(in_array($paper->status, ['ceo_approved', 'approved']))
                <a href="{{ route('workflow.papers.pdf', $paper) }}" class="btn btn-sm btn-success">
                    <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- Paper Content (PDF-like view) --}}
        <div class="col-lg-8">
            <div class="card" id="paperView">
                <div class="card-body p-4 p-md-5" style="background: #fff; min-height: 800px;">
                    @include('workflow.components.letterhead')

                    {{-- Paper Header --}}
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Paper Number</small>
                            <div class="fw-bold" style="font-size: 16px; color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->paper_number }}</div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Date</small>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($paper->created_at)->format('d F Y') }}</div>
                        </div>
                    </div>

                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h2 class="mb-1" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->title }}</h2>
                        @if($paper->agenda_item_number)
                            <span class="badge bg-soft-primary text-primary px-3 py-1">Agenda Item {{ $paper->agenda_item_number }}</span>
                        @endif
                    </div>

                    {{-- Paper Body (structured or legacy) --}}
                    <div class="paper-content">
                        @include('workflow.papers.partials.structured-content', ['paper' => $paper])
                    </div>

                    {{-- Annexures --}}
                    @include('workflow.papers.partials.annexures', ['paper' => $paper])

                    {{-- Embedded PDF viewer --}}
                    @if($paper->file_path)
                        <div class="mt-4 pt-3 border-top">
                            <h5><i class="mdi mdi-file-pdf-box text-danger me-1"></i> Original Document</h5>
                            <iframe src="{{ asset('storage/' . $paper->file_path) }}" style="width: 100%; height: 500px; border: 1px solid #dee2e6; border-radius: 6px;"></iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar: Sticky Notes & Actions --}}
        <div class="col-lg-4">

            {{-- Sticky Notes Panel --}}
            <div class="card">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="mdi mdi-note-multiple-outline me-1"></i> Sticky Notes</h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addNoteForm">
                        <i class="mdi mdi-plus me-1"></i> Add Note
                    </button>
                </div>
                <div class="card-body p-0">
                    {{-- Add Note Form --}}
                    <div class="collapse p-3 border-bottom bg-light" id="addNoteForm">
                        <form id="stickyNoteForm">
                            <div class="mb-2">
                                <label class="form-label" style="font-size: 12px;">Note Color</label>
                                <select name="color" id="noteColor" class="form-select form-select-sm">
                                    <option value="yellow" style="background: #fff9c4;">Yellow</option>
                                    <option value="blue" style="background: #f0f0f0;">Blue</option>
                                    <option value="pink" style="background: #fce4ec;">Pink</option>
                                    <option value="green" style="background: #e8f5e9;">Green</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" style="font-size: 12px;">Note Content</label>
                                <textarea name="content" id="noteContent" class="form-control form-control-sm" rows="3" placeholder="Write your note here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100" id="addNoteBtn">
                                <i class="mdi mdi-note-plus-outline me-1"></i> Add Sticky Note
                            </button>
                        </form>
                    </div>

                    {{-- Existing Notes --}}
                    <div id="stickyNotesList">
                        @if($paper->stickyNotes && $paper->stickyNotes->count())
                            @foreach($paper->stickyNotes as $note)
                                @php
                                    $noteColors = [
                                        'yellow' => ['bg' => '#fff9c4', 'border' => '#fdd835', 'icon' => 'mdi-note-outline'],
                                        'blue' => ['bg' => '#f0f0f0', 'border' => '#f31628', 'icon' => 'mdi-note-text-outline'],
                                        'pink' => ['bg' => '#fce4ec', 'border' => '#ef5350', 'icon' => 'mdi-note-alert-outline'],
                                        'green' => ['bg' => '#e8f5e9', 'border' => '#66bb6a', 'icon' => 'mdi-note-check-outline'],
                                    ];
                                    $nc = $noteColors[$note->color] ?? $noteColors['yellow'];
                                @endphp
                                <div class="sticky-note-item p-3 border-bottom position-relative" data-note-id="{{ $note->id }}" style="background: {{ $nc['bg'] }}; border-left: 4px solid {{ $nc['border'] }} !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <small class="fw-semibold" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">
                                            <i class="mdi {{ $nc['icon'] }} me-1"></i>{{ ucfirst($note->color) }} Note
                                        </small>
                                        <div class="d-flex align-items-center gap-1">
                                            <small class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($note->created_at)->format('d M H:i') }}</small>
                                            <button type="button" class="btn btn-sm p-0 text-muted delete-note-btn" data-note-id="{{ $note->id }}" title="Delete note">
                                                <i class="mdi mdi-close" style="font-size: 14px;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="font-size: 13px;">{{ $note->content }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="noNotesMsg" class="text-center text-muted py-4" style="{{ ($paper->stickyNotes && $paper->stickyNotes->count()) ? 'display:none;' : '' }}">
                        <i class="mdi mdi-note-off-outline" style="font-size: 36px; opacity: 0.3;"></i>
                        <p class="mb-0 mt-1" style="font-size: 12px;">No sticky notes yet.</p>
                    </div>
                </div>
            </div>

            {{-- Paper Info --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Paper Details</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                        <tr>
                            <td class="text-muted" width="40%">Author:</td>
                            <td class="fw-semibold">{{ $paper->author->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Director:</td>
                            <td>{{ $paper->assignment->director->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Meeting:</td>
                            <td>{{ $paper->meeting->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Submitted:</td>
                            <td>{{ $paper->submitted_at ? \Carbon\Carbon::parse($paper->submitted_at)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dir. Approved:</td>
                            <td>{{ $paper->director_approved_at ? \Carbon\Carbon::parse($paper->director_approved_at)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Action Bar --}}
            @if(in_array($paper->status, ['director_approved', 'under_review']))
                <div class="card border-primary">
                    <div class="card-header bg-soft-primary">
                        <h5 class="mb-0">
                            <i class="mdi mdi-gavel me-1"></i>
                            {{ !empty($isAuditCommittee) && $isAuditCommittee ? 'DDIA' : 'CEO' }} Decision
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            {{-- Approve Button --}}
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="mdi mdi-check-decagram me-1"></i> Approve Paper
                            </button>

                            {{-- Reject Button --}}
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Reject Paper
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Review History --}}
            @if($paper->comments && $paper->comments->count())
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="mdi mdi-history me-1"></i> Review History</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($paper->comments as $comment)
                            <div class="p-3 border-bottom" style="font-size: 12px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold">{{ $comment->user->name ?? 'System' }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-muted">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Approve Modal with Signature Pad --}}
@if(in_array($paper->status ?? '', ['director_approved', 'under_review']))
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-success">
                <h5 class="modal-title">
                    <i class="mdi mdi-check-decagram text-success me-1"></i> Approve Board Paper
                    @if(!empty($isAuditCommittee) && $isAuditCommittee)
                        <small class="text-muted">(as DD Internal Audit)</small>
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.ceo.paper.decision', $paper) }}" method="POST">
                @csrf
                <input type="hidden" name="decision" value="approve">
                <div class="modal-body">
                    <p class="text-muted mb-3">You are approving <strong>{{ $paper->paper_number }}</strong> &mdash; {{ $paper->title }}</p>

                    @if(!empty($isAuditCommittee) && $isAuditCommittee)
                        <div class="alert alert-info mb-3" style="font-size: 12px;">
                            <i class="mdi mdi-information-outline me-1"></i>
                            You are approving this paper as DD Internal Audit for the Audit Committee. Signature is optional.
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Comments (optional)</label>
                        <textarea name="comment" class="form-control" rows="2" placeholder="Optional approval comments..."></textarea>
                    </div>

                    @if(!empty($isAuditCommittee) && $isAuditCommittee)
                        {{-- Signature optional for DDIA --}}
                        @include('workflow.components.signature-pad', ['inputName' => 'ceo_signature', 'label' => 'Signature (Optional)'])
                    @else
                        @include('workflow.components.signature-pad', ['inputName' => 'ceo_signature', 'label' => 'CEO Signature'])
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check-decagram me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title"><i class="mdi mdi-close-circle text-danger me-1"></i> Reject Board Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.ceo.paper.decision', $paper) }}" method="POST">
                @csrf
                <input type="hidden" name="decision" value="reject">
                <div class="modal-body">
                    <p class="text-muted mb-3">You are rejecting <strong>{{ $paper->paper_number }}</strong> &mdash; {{ $paper->title }}</p>

                    <div class="alert alert-warning" style="font-size: 13px;">
                        <i class="mdi mdi-information-outline me-1"></i>
                        @if(!empty($isAuditCommittee) && $isAuditCommittee)
                            The paper will be sent back to the officer for revision.
                        @else
                            The paper will be sent back to the Director, then to the Deputy Director for revision.
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Provide detailed feedback on why this paper is being rejected and what changes are needed..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-close-circle-outline me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    .paper-content img { max-width: 100%; height: auto; }
    .paper-content table { width: 100%; border-collapse: collapse; }
    .paper-content table td, .paper-content table th { border: 1px solid #dee2e6; padding: 8px 12px; }
    .sticky-note-item { transition: background 0.3s ease; }
    .sticky-note-item.highlight { animation: noteHighlight 1s ease; }
    @keyframes noteHighlight { 0% { opacity: 0; transform: translateY(-10px); } 100% { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
<script>
    const noteColorMap = {
        yellow: { bg: '#fff9c4', border: '#fdd835', icon: 'mdi-note-outline' },
        blue:   { bg: '#f0f0f0', border: '#f31628', icon: 'mdi-note-text-outline' },
        pink:   { bg: '#fce4ec', border: '#ef5350', icon: 'mdi-note-alert-outline' },
        green:  { bg: '#e8f5e9', border: '#66bb6a', icon: 'mdi-note-check-outline' },
    };

    // Add sticky note via AJAX
    document.getElementById('stickyNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const content = document.getElementById('noteContent').value.trim();
        const color = document.getElementById('noteColor').value;
        const btn = document.getElementById('addNoteBtn');

        if (!content) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Saving...';

        fetch("{{ route('workflow.ceo.paper.sticky-note', $paper) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: content, color: color })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Hide "no notes" message
                document.getElementById('noNotesMsg').style.display = 'none';

                // Build note HTML
                const nc = noteColorMap[color] || noteColorMap.yellow;
                const now = new Date();
                const timeStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) + ' ' + now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                const noteId = data.note ? data.note.id : Date.now();

                const noteHtml = `
                    <div class="sticky-note-item p-3 border-bottom position-relative highlight" data-note-id="${noteId}" style="background: ${nc.bg}; border-left: 4px solid ${nc.border} !important;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <small class="fw-semibold" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">
                                <i class="mdi ${nc.icon} me-1"></i>${color.charAt(0).toUpperCase() + color.slice(1)} Note
                            </small>
                            <div class="d-flex align-items-center gap-1">
                                <small class="text-muted" style="font-size: 10px;">${timeStr}</small>
                                <button type="button" class="btn btn-sm p-0 text-muted delete-note-btn" data-note-id="${noteId}" title="Delete note">
                                    <i class="mdi mdi-close" style="font-size: 14px;"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mb-0" style="font-size: 13px;">${content.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                    </div>`;

                document.getElementById('stickyNotesList').insertAdjacentHTML('beforeend', noteHtml);

                // Reset form
                document.getElementById('noteContent').value = '';
                document.getElementById('noteColor').value = 'yellow';

                // Collapse form
                const formCollapse = bootstrap.Collapse.getInstance(document.getElementById('addNoteForm'));
                if (formCollapse) formCollapse.hide();
            } else {
                alert(data.message || 'Failed to add note.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while saving the note.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-note-plus-outline me-1"></i> Add Sticky Note';
        });
    });

    // Delete sticky note via AJAX
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-note-btn');
        if (!deleteBtn) return;

        if (!confirm('Remove this note?')) return;

        const noteId = deleteBtn.dataset.noteId;
        const noteEl = deleteBtn.closest('.sticky-note-item');

        fetch(`/workflow/ceo/sticky-notes/${noteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                noteEl.remove();
                // Show "no notes" if empty
                if (document.querySelectorAll('.sticky-note-item').length === 0) {
                    document.getElementById('noNotesMsg').style.display = '';
                }
            } else {
                alert(data.message || 'Failed to delete note.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while deleting the note.');
        });
    });
</script>
@endpush
@endsection
