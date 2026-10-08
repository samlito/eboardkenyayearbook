@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('workflow.ceo.papers') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Meetings
            </a>
            <h3 class="mb-0"><i class="mdi mdi-file-document-multiple-outline text-primary me-1"></i> {{ $meeting->title }}</h3>
            <p class="text-muted mb-0">
                <i class="mdi mdi-account-group-outline me-1"></i>{{ $meeting->boardCommittee->name ?? 'General' }}
                @if($meeting->meeting_date)
                    &mdash; <i class="mdi mdi-calendar me-1"></i>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d F Y') }}
                @endif
            </p>
        </div>
        <div>
            <span class="badge bg-soft-warning text-warning px-3 py-2" style="font-size: 13px;">
                <i class="mdi mdi-clock-outline me-1"></i> {{ $papers->count() }} pending review
            </span>
        </div>
    </div>

    {{-- Pending Papers --}}
    @if($papers->count())
        <div class="card mb-4">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="mdi mdi-clock-alert-outline text-warning me-1"></i>
                    Papers Pending {{ !empty($isAuditCommittee) && $isAuditCommittee ? 'DDIA' : 'CEO' }} Review
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-hover twwda-datatable w-100" data-export-title="CEO Paper Review">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th>Paper Number</th>
                            <th>Title</th>
                            <th>Prepared By</th>
                            <th>Director</th>
                            <th>Dir. Approved</th>
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
                                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; min-width: 28px;">
                                            <span class="fw-bold text-primary" style="font-size: 10px;">{{ strtoupper(substr($paper->author->name ?? 'N', 0, 2)) }}</span>
                                        </div>
                                        <span style="font-size: 13px;">{{ $paper->author->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td style="font-size: 12px;">
                                    @if($paper->directorApprover)
                                        {{ $paper->directorApprover->name }}
                                    @elseif($paper->assignment && $paper->assignment->director)
                                        {{ $paper->assignment->director->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($paper->director_approved_at)
                                        <span style="font-size: 12px;">{{ \Carbon\Carbon::parse($paper->director_approved_at)->format('d M Y') }}</span>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($paper->director_approved_at)->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('workflow.ceo.paper.review', $paper) }}" class="btn btn-sm btn-primary rounded-pill">
                                        <i class="mdi mdi-eye-check-outline me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <i class="mdi mdi-check-all text-success" style="font-size: 48px; opacity: 0.3;"></i>
                <p class="text-muted mb-0 mt-2">No papers pending {{ !empty($isAuditCommittee) && $isAuditCommittee ? 'DDIA' : 'CEO' }} review for this meeting.</p>
            </div>
        </div>
    @endif

    {{-- Already Reviewed Papers --}}
    @if($reviewedPapers->count())
        <div class="card">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="mdi mdi-check-circle-outline text-success me-1"></i> Reviewed Papers</h5>
                <span class="badge bg-soft-success text-success">{{ $reviewedPapers->count() }} reviewed</span>
            </div>
            <div class="card-body">
                <table class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th>Paper Number</th>
                            <th>Title</th>
                            <th>Prepared By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviewedPapers as $i => $paper)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-semibold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $paper->paper_number }}</span>
                                </td>
                                <td>{{ Str::limit($paper->title, 50) }}</td>
                                <td style="font-size: 13px;">{{ $paper->author->name ?? 'N/A' }}</td>
                                <td>
                                    @if(in_array($paper->status, ['ceo_approved', 'approved']) && $paper->ceo_signature)
                                        <div class="text-center">
                                            <img src="{{ $paper->ceo_signature }}" alt="Approver Signature" style="max-height: 40px;" class="mb-1">
                                            <br><small class="text-success fw-semibold" style="font-size: 10px;"><i class="mdi mdi-shield-check me-1"></i>Signed</small>
                                        </div>
                                    @elseif(in_array($paper->status, ['ceo_approved', 'approved']))
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('workflow.ceo.paper.review', $paper) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="mdi mdi-eye-outline me-1"></i> View
                                    </a>
                                    @if(in_array($paper->status, ['ceo_approved', 'approved']))
                                        <a href="{{ route('workflow.papers.pdf', $paper) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="mdi mdi-file-pdf-box me-1"></i> PDF
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
