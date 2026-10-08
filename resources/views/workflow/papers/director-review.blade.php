@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0"><i class="mdi mdi-eye-check-outline text-primary me-1"></i> Board Papers &mdash; Director Review</h3>
            <p class="text-muted mb-0">Review and approve board papers submitted by officers under your directorate.</p>
        </div>
        <div>
            <span class="badge bg-soft-warning text-warning px-3 py-2" style="font-size: 13px;">
                <i class="mdi mdi-clock-outline me-1"></i> {{ $papers->where('status', 'submitted')->count() }} pending review
            </span>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="mdi mdi-file-multiple-outline me-1"></i> Papers for Review</h5>
            <div class="d-flex gap-2">
                <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="submitted" selected>Pending Review</option>
                    <option value="director_approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover twwda-datatable w-100" data-export-title="Director Paper Review">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Paper Number</th>
                        <th>Title</th>
                        <th>Prepared By</th>
                        <th>Meeting</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papers as $i => $paper)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <span class="fw-semibold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->paper_number }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ Str::limit($paper->title, 50) }}</span>
                                @if($paper->agenda_item_number)
                                    <br><small class="text-muted">Agenda Item {{ $paper->agenda_item_number }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; min-width: 28px;">
                                        <span class="fw-bold text-primary" style="font-size: 10px;">{{ strtoupper(substr($paper->author->name ?? 'N', 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <span style="font-size: 13px;">{{ $paper->author->name ?? 'N/A' }}</span>
                                        <br><small class="text-muted">{{ $paper->author->role ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 12px;">{{ $paper->meeting->title ?? 'N/A' }}</td>
                            <td>
                                @if($paper->submitted_at)
                                    <span style="font-size: 12px;">{{ \Carbon\Carbon::parse($paper->submitted_at)->format('d M Y') }}</span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($paper->submitted_at)->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = [
                                        'submitted' => 'bg-warning',
                                        'under_review' => 'bg-info',
                                        'director_approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBadge[$paper->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#reviewModal-{{ $paper->id }}">
                                    <i class="mdi mdi-eye-outline me-1"></i> Review
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recallable Papers Section --}}
@if(isset($recallablePapers) && $recallablePapers->count())
    <div class="card mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="mdi mdi-undo-variant text-warning me-1"></i> Forwarded to CEO &mdash; Can Be Recalled
            </h5>
            <span class="badge bg-soft-warning text-warning px-3 py-1" style="font-size: 12px;">
                {{ $recallablePapers->count() }} {{ Str::plural('paper', $recallablePapers->count()) }}
            </span>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-3" style="font-size: 12px;">
                <i class="mdi mdi-information-outline text-info me-1"></i>
                These papers have been approved by you and forwarded to the CEO. You can recall them if the CEO has not yet taken action.
            </div>
            <table class="table table-hover w-100">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Paper Number</th>
                        <th>Title</th>
                        <th>Prepared By</th>
                        <th>Meeting</th>
                        <th>Approved On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recallablePapers as $i => $rPaper)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="fw-semibold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $rPaper->paper_number }}</span></td>
                            <td>{{ Str::limit($rPaper->title, 50) }}</td>
                            <td style="font-size: 13px;">{{ $rPaper->author->name ?? 'N/A' }}</td>
                            <td style="font-size: 12px;">{{ $rPaper->meeting->title ?? 'N/A' }}</td>
                            <td>
                                @if($rPaper->director_approved_at)
                                    <span style="font-size: 12px;">{{ \Carbon\Carbon::parse($rPaper->director_approved_at)->format('d M Y') }}</span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($rPaper->director_approved_at)->diffForHumans() }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $rPaper->status)) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('workflow.director.paper.recall', $rPaper) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill"
                                            onclick="return confirm('Are you sure you want to recall this board paper from CEO review? It will be returned to your review queue.')">
                                        <i class="mdi mdi-undo-variant me-1"></i> Recall
                                    </button>
                                </form>
                                <a href="{{ route('workflow.papers.show', $rPaper) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="mdi mdi-eye-outline me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Review Modals --}}
@foreach($papers as $paper)
<div class="modal fade" id="reviewModal-{{ $paper->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">{{ $paper->paper_number }} &mdash; {{ $paper->title }}</h5>
                    <small class="text-muted">Prepared by {{ $paper->author->name ?? 'N/A' }} | {{ $paper->meeting->title ?? '' }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Paper Content --}}
                    <div class="col-md-8">
                        <div class="border rounded p-4 bg-white" style="max-height: 600px; overflow-y: auto;">
                            @include('workflow.components.letterhead')

                            <div class="text-center mb-3">
                                <span class="badge bg-soft-primary text-primary px-3">{{ $paper->paper_number }}</span>
                            </div>
                            <h4 class="text-center mb-4" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->title }}</h4>

                            <div class="paper-content">
                                @include('workflow.papers.partials.structured-content', ['paper' => $paper])
                            </div>

                            @include('workflow.papers.partials.annexures', ['paper' => $paper, 'compact' => true])

                            @if($paper->file_path)
                                <hr>
                                <a href="{{ asset('storage/' . $paper->file_path) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="mdi mdi-file-pdf-box me-1"></i> View Original PDF
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Review Panel --}}
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="mb-3"><i class="mdi mdi-gavel me-1"></i> Your Decision</h6>

                            {{-- Paper Info --}}
                            <div class="mb-3">
                                <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                    <tr>
                                        <td class="text-muted">Author:</td>
                                        <td>{{ $paper->author->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Submitted:</td>
                                        <td>{{ $paper->submitted_at ? \Carbon\Carbon::parse($paper->submitted_at)->format('d M Y H:i') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Agenda Item:</td>
                                        <td>{{ $paper->agenda_item_number ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            @if($paper->status === 'submitted' || $paper->status === 'under_review')
                                {{-- Approve Form --}}
                                <form action="{{ route('workflow.director.paper.decision', $paper) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="decision" value="approve">
                                    <div class="mb-2">
                                        <label class="form-label" style="font-size: 12px;">Comments (optional)</label>
                                        <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Add review comments..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this board paper?')">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Approve Paper
                                    </button>
                                </form>

                                <hr>

                                {{-- Reject Form --}}
                                <form action="{{ route('workflow.director.paper.decision', $paper) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="decision" value="reject">
                                    <div class="mb-2">
                                        <label class="form-label" style="font-size: 12px;">Reason for Rejection <span class="text-danger">*</span></label>
                                        <textarea name="comment" class="form-control form-control-sm" rows="3" placeholder="Explain why this paper is being rejected..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this board paper? The author will be notified.')">
                                        <i class="mdi mdi-close-circle-outline me-1"></i> Reject Paper
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info mb-0" style="font-size: 13px;">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    This paper has already been {{ str_replace('_', ' ', $paper->status) }}.
                                </div>
                            @endif
                        </div>

                        {{-- Previous Comments --}}
                        @if($paper->comments && $paper->comments->count())
                            <div class="mt-3">
                                <h6 class="text-muted mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Review History</h6>
                                @foreach($paper->comments as $comment)
                                    <div class="border rounded p-2 mb-2 bg-white" style="font-size: 12px;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-semibold">{{ $comment->user->name ?? 'System' }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">{{ $comment->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('workflow.papers.show', $paper) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="mdi mdi-open-in-new me-1"></i> Open Full View
                </a>
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('styles')
<style>
    .paper-content img { max-width: 100%; height: auto; }
    .paper-content table { width: 100%; border-collapse: collapse; }
    .paper-content table td, .paper-content table th { border: 1px solid #dee2e6; padding: 6px 10px; font-size: 13px; }
</style>
@endpush
@endsection
