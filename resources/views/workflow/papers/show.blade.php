@extends('admin_dashboard')

@section('admin')
@php $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray(); @endphp
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
            <h3 class="mb-0">Board Paper</h3>
            <p class="text-muted mb-0">{{ $paper->paper_number }} &mdash; {{ $paper->meeting->title ?? '' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @php
                $statusColors = [
                    'draft' => 'warning',
                    'submitted' => 'info',
                    'under_review' => 'primary',
                    'director_approved' => 'primary',
                    'ceo_approved' => 'success',
                    'approved' => 'success',
                    'rejected_by_director' => 'danger',
                    'rejected_by_ceo' => 'danger',
                ];
                $color = $statusColors[$paper->status] ?? 'secondary';
            @endphp
            @if(!in_array($paper->status, ['ceo_approved', 'approved']))
                <span class="badge bg-{{ $color }} px-3 py-2" style="font-size: 13px;">
                    <i class="mdi mdi-circle-small"></i> {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                </span>
            @endif
            @if(in_array($paper->status, ['draft', 'rejected_by_director', 'rejected_by_ceo']) && (int) $paper->prepared_by === (int) auth()->id())
                @if(in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']))
                    <span class="badge bg-danger px-3 py-2" style="font-size: 13px;">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        @if($paper->status === 'rejected_by_ceo')
                            Rejected by {{ $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds) ? 'DD Internal Audit' : 'CEO' }}
                        @else
                            Rejected by Director
                        @endif
                        &mdash; Revision Required
                    </span>
                @endif
                <a href="{{ route('workflow.papers.edit', $paper) }}" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-pencil-outline me-1"></i> {{ in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']) ? 'Revise Paper' : 'Edit' }}
                </a>
                <form action="{{ route('workflow.papers.submit', $paper) }}" method="POST" class="d-inline" id="submitPaperForm">
                    @csrf
                    <button type="button" class="btn btn-sm text-white" style="background: #099139;" onclick="confirmSubmit()">
                        <i class="mdi mdi-send-check me-1"></i> {{ in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']) ? 'Resubmit for Review' : 'Submit for Review' }}
                    </button>
                </form>
            @endif
            @php
                $isAuthor = (int) $paper->prepared_by === (int) auth()->id();
                $canRecallFromDirector = $isAuthor && $paper->status === 'submitted';
                $canRecallFromCeo = $isAuthor && in_array($paper->status, ['director_approved', 'ceo_review']) && $paper->is_auto_approved;
            @endphp
            @if($canRecallFromDirector || $canRecallFromCeo)
                <form action="{{ route('workflow.papers.recall', $paper) }}" method="POST" class="d-inline" id="recallPaperForm">
                    @csrf
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirmRecall()">
                        <i class="mdi mdi-undo-variant me-1"></i> Recall Paper
                    </button>
                </form>
            @endif
            @if(in_array($paper->status, ['ceo_approved', 'approved']))
                <a href="{{ route('workflow.papers.pdf', $paper) }}" class="btn btn-sm btn-success">
                    <i class="mdi mdi-file-pdf-box me-1"></i> Download Approved PDF
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-printer-outline me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Rejection Feedback Banner (visible to the author when paper is rejected) --}}
    @if(in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']) && (int) $paper->prepared_by === (int) auth()->id())
        @php
            $latestRejection = $paper->comments
                ->whereIn('action', ['rejected', 'reject'])
                ->sortByDesc('created_at')
                ->first();
        @endphp
        @if($latestRejection)
            <div class="alert border-0 rounded-3 mb-3" style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border-left: 4px solid #dc2626 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 44px; height: 44px; background: #fee2e2;">
                        <i class="mdi mdi-alert-circle text-danger" style="font-size: 22px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-danger fw-bold" style="font-size: 14px;">
                            <i class="mdi mdi-message-alert-outline me-1"></i>
                            @if($paper->status === 'rejected_by_ceo')
                                {{ $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds) ? 'DD Internal Audit' : 'CEO' }}
                            @else
                                Director
                            @endif
                            Feedback &mdash; Revision Required
                        </h6>
                        <p class="mb-1" style="font-size: 13px; color: #991b1b;">
                            <strong>{{ $latestRejection->user->name ?? 'Reviewer' }}:</strong>
                            &ldquo;{{ $latestRejection->comment }}&rdquo;
                        </p>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($latestRejection->created_at)->format('d M Y, H:i') }}
                            &mdash; Please click <strong>"Revise Paper"</strong> to address this feedback and resubmit.
                        </small>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Recall Info Banner --}}
    @if(isset($canRecallFromDirector) && $canRecallFromDirector)
        <div class="alert border-0 rounded-3 mb-3" style="background: linear-gradient(135deg, #fefce8, #fef9c3); border-left: 4px solid #d97706 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-information-outline text-warning" style="font-size: 20px;"></i>
                <div style="font-size: 13px;">
                    <strong>Paper submitted for Director review.</strong> You can recall this paper if you need to make changes before the director reviews it.
                </div>
            </div>
        </div>
    @elseif(isset($canRecallFromCeo) && $canRecallFromCeo)
        <div class="alert border-0 rounded-3 mb-3" style="background: linear-gradient(135deg, #fefce8, #fef9c3); border-left: 4px solid #d97706 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-information-outline text-warning" style="font-size: 20px;"></i>
                <div style="font-size: 13px;">
                    <strong>Paper submitted directly for {{ $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds) ? 'DDIA' : 'CEO' }} review.</strong> You can recall this paper if you need to make changes before it is approved.
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        {{-- Main Paper View --}}
        <div class="col-lg-9">
            <div class="card" id="paperDocument">
                <div class="card-body p-4 p-md-5">
                    {{-- Letterhead --}}
                    @include('workflow.components.letterhead')

                    {{-- Paper Number & Date --}}
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

                    {{-- Title --}}
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h2 class="mb-1" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->title }}</h2>
                        @if($paper->agenda_item_number)
                            <span class="badge bg-soft-primary text-primary px-3 py-1">Agenda Item {{ $paper->agenda_item_number }}</span>
                        @endif
                    </div>

                    {{-- Content (structured or legacy) --}}
                    <div class="paper-content mb-4">
                        @include('workflow.papers.partials.structured-content', ['paper' => $paper])
                    </div>

                    {{-- Annexures --}}
                    @include('workflow.papers.partials.annexures', ['paper' => $paper])

                    {{-- Digital Signature Block --}}
                    @if(in_array($paper->status, ['ceo_approved', 'approved']))
                        @php
                            $isAuditPaper = $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds);
                            $approverRoleFull = $isAuditPaper ? 'Deputy Director Internal Audit' : 'Chief Executive Officer';
                            $approverUser = $isAuditPaper
                                ? \App\Models\User::role('DD Internal Audit')->first()
                                : \App\Models\User::role('CEO')->first();
                            $approverName = $approverUser->name ?? $approverRoleFull;
                        @endphp
                        <div class="mt-5 pt-4 border-top">
                            <div class="row">
                                <div class="col-md-7 offset-md-5">
                                    <div class="text-center p-3 rounded" style="border: 2px solid #099139; background: #f0faf3;">
                                        <div class="text-uppercase fw-bold mb-2" style="font-size: 11px; color: #099139; letter-spacing: 2px;">
                                            <i class="mdi mdi-shield-check me-1"></i> Digitally Signed & Approved
                                        </div>
                                        @if($paper->ceo_signature)
                                            <img src="{{ $paper->ceo_signature }}" alt="Approver Digital Signature" style="max-height: 80px;" class="mb-2">
                                        @else
                                            <div class="text-muted fst-italic mb-2" style="font-size: 12px;">Signature not available</div>
                                        @endif
                                        <hr style="border-top: 2px solid #333; width: 200px; margin: 0 auto;">
                                        <p class="fw-bold mb-0 mt-1">{{ $approverName }}</p>
                                        <p class="mb-0" style="font-size: 13px;">{{ $approverRoleFull }}</p>
                                        <small class="text-muted">{{ setting('institution_name', 'eBoard System') }}</small>
                                        <br>
                                        @if($paper->ceo_approved_at)
                                            <small class="text-muted">Date: {{ \Carbon\Carbon::parse($paper->ceo_approved_at)->format('d F Y') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-3">
            {{-- Sticky Notes --}}
            @if($paper->stickyNotes && $paper->stickyNotes->count())
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="mdi mdi-note-multiple-outline me-1"></i> Reviewer Notes</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($paper->stickyNotes as $note)
                            @php
                                $noteColors = [
                                    'yellow' => ['bg' => '#fff9c4', 'border' => '#fdd835'],
                                    'blue' => ['bg' => '#f0f0f0', 'border' => '#f31628'],
                                    'pink' => ['bg' => '#fce4ec', 'border' => '#ef5350'],
                                    'green' => ['bg' => '#e8f5e9', 'border' => '#66bb6a'],
                                ];
                                $nc = $noteColors[$note->color] ?? $noteColors['yellow'];
                            @endphp
                            <div class="p-3 border-bottom" style="background: {{ $nc['bg'] }}; border-left: 3px solid {{ $nc['border'] }} !important;">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-semibold text-uppercase" style="font-size: 10px;">{{ ucfirst($note->color) }} Note</small>
                                    <small class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($note->created_at)->format('d M H:i') }}</small>
                                </div>
                                <p class="mb-0" style="font-size: 13px;">{{ $note->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Comments / Review Timeline --}}
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="mdi mdi-comment-text-multiple-outline me-1"></i> Review History</h6>
                </div>
                <div class="card-body p-0">
                    @if($paper->comments && $paper->comments->count())
                        <div class="timeline-alt p-3">
                            @foreach($paper->comments as $comment)
                                <div class="d-flex gap-2 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        @php
                                            $commentIcons = [
                                                'approved' => ['icon' => 'mdi-check-circle', 'color' => 'success'],
                                                'rejected' => ['icon' => 'mdi-close-circle', 'color' => 'danger'],
                                                'comment' => ['icon' => 'mdi-comment-outline', 'color' => 'info'],
                                                'submitted' => ['icon' => 'mdi-send-check', 'color' => 'primary'],
                                            ];
                                            $ci = $commentIcons[$comment->type ?? 'comment'] ?? $commentIcons['comment'];
                                        @endphp
                                        <div class="bg-soft-{{ $ci['color'] }} rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="mdi {{ $ci['icon'] }} text-{{ $ci['color'] }}" style="font-size: 16px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-semibold" style="font-size: 12px;">{{ $comment->user->name ?? 'System' }}</span>
                                            <small class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-muted" style="font-size: 12px;">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-comment-off-outline" style="font-size: 32px; opacity: 0.3;"></i>
                            <p class="mb-0 mt-1" style="font-size: 12px;">No review comments yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Submission Routing Info --}}
            @if(in_array($paper->status, ['draft', 'rejected_by_director', 'rejected_by_ceo']))
                @php
                    $director = $paper->assignment->director ?? null;
                    $directorDirectorate = $director?->activeDirectorateMapping?->directorate ?? null;
                    $authorIsOwnDirector = $director && ((int) $director->id === (int) $paper->prepared_by);
                    $isAuditPaperRoute = $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds);
                    $finalApproverLabel = $isAuditPaperRoute ? 'DDIA Approval' : 'CEO Approval';
                    $finalApproverDesc = $isAuditPaperRoute ? 'DD Internal Audit approval' : 'Final approval & digital signature';
                @endphp
                <div class="card border-start border-3" style="border-color: {{ ($director && !$authorIsOwnDirector) ? '#b45309' : setting('primary_color', '#1a1a1a') }} !important;">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">
                            <i class="mdi mdi-arrow-decision-outline me-1"></i> Submission Route
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            {{-- Step 1: Director or direct to CEO --}}
                            @if($director && !$authorIsOwnDirector)
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 28px; height: 28px; background: #fffbeb; border: 2px solid #d97706;">
                                        <span class="fw-bold" style="font-size: 11px; color: #b45309;">1</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size: 13px; color: #b45309;">
                                            <i class="mdi mdi-account-tie me-1"></i> Director Review
                                        </div>
                                        <div class="fw-bold" style="font-size: 13px;">{{ $director->name }}</div>
                                        @if($directorDirectorate)
                                            <small class="text-muted">{{ $directorDirectorate->name }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center" style="margin-left: 13px;">
                                    <i class="mdi mdi-arrow-down text-muted" style="font-size: 18px;"></i>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 28px; height: 28px; background: #ecfdf5; border: 2px solid #099139;">
                                        <span class="fw-bold" style="font-size: 11px; color: #099139;">2</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size: 13px; color: #099139;">
                                            <i class="mdi mdi-shield-check me-1"></i> {{ $finalApproverLabel }}
                                        </div>
                                        <small class="text-muted">{{ $finalApproverDesc }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 28px; height: 28px; background: #f2f2f2; border: 2px solid {{ setting('primary_color', '#1a1a1a') }};">
                                        <span class="fw-bold" style="font-size: 11px; color: {{ setting('primary_color', '#1a1a1a') }};">1</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size: 13px; color: {{ setting('primary_color', '#1a1a1a') }};">
                                            <i class="mdi mdi-shield-check me-1"></i> {{ $finalApproverLabel }}
                                        </div>
                                        @if($authorIsOwnDirector)
                                            <small class="text-success">
                                                <i class="mdi mdi-arrow-right-bold-circle-outline me-1"></i>Director-authored — auto-approved to {{ $isAuditPaperRoute ? 'DDIA' : 'CEO' }}
                                            </small>
                                        @else
                                            <small class="text-muted">Direct to {{ $isAuditPaperRoute ? 'DD Internal Audit' : 'CEO' }} for review</small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Paper Info --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Paper Information</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                        <tr>
                            <td class="text-muted">Author:</td>
                            <td class="fw-semibold">{{ $paper->author->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Meeting:</td>
                            <td>{{ $paper->meeting->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td>{{ \Carbon\Carbon::parse($paper->created_at)->format('d M Y') }}</td>
                        </tr>
                        @if($paper->submitted_at)
                        <tr>
                            <td class="text-muted">Submitted:</td>
                            <td>{{ \Carbon\Carbon::parse($paper->submitted_at)->format('d M Y') }}</td>
                        </tr>
                        @endif
                        @if($paper->approved_at)
                        <tr>
                            <td class="text-muted">Approved:</td>
                            <td>{{ \Carbon\Carbon::parse($paper->approved_at)->format('d M Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .left-side-menu, .navbar-custom, .content > .d-flex:first-child, .col-lg-3, .btn { display: none !important; }
        .col-lg-9 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
        .card { box-shadow: none !important; border: none !important; }
    }
    .paper-content img { max-width: 100%; height: auto; }
    .paper-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
    .paper-content table td, .paper-content table th { border: 1px solid #dee2e6; padding: 8px 12px; }
</style>
@endpush

@push('scripts')
<script>
    function confirmSubmit() {
        @php
            $isResubmission = in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo']);
        @endphp
        var msg = @json($isResubmission
            ? 'Are you sure you want to resubmit this revised board paper for review?'
            : 'Are you sure you want to submit this board paper for review? Once submitted, you will not be able to edit it unless it is returned for revision.');
        if (confirm(msg)) {
            document.getElementById('submitPaperForm').submit();
        }
    }
    function confirmRecall() {
        @php
            $recallTarget = ($paper->status === 'submitted') ? 'Director' : 'CEO/DDIA';
        @endphp
        var msg = @json("Are you sure you want to recall this board paper from {$recallTarget} review? The paper will be returned to draft status and you can edit and resubmit it.");
        if (confirm(msg)) {
            document.getElementById('recallPaperForm').submit();
        }
    }
</script>
@endpush
@endsection
