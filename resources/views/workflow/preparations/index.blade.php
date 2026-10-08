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
            <h3 class="mb-0"><i class="mdi mdi-format-list-checks text-primary me-1"></i> Meeting Preparation</h3>
            <p class="text-muted mb-0">{{ $meeting->title }} &mdash; {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date, 'label' => 'Meeting'])
        </div>
    </div>

    {{-- Stage Indicator --}}
    @include('workflow.components.stage-indicator', ['currentStage' => 8, 'stageLabel' => 'Meeting Preparations', 'meeting' => $meeting])

    <div class="row">
        <div class="col-lg-8">

            {{-- ═══ OFFICIAL AGENDA ITEMS (from Board Notice + Approved New Items) ═══ --}}
            <div class="card">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="mdi mdi-sort-variant me-1 text-primary"></i> Meeting Agenda</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-soft-primary text-primary">{{ $agendaItems->count() }} items</span>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAgendaModal">
                            <i class="mdi mdi-plus me-1"></i> New Agenda Item
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3" style="font-size: 13px;">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Drag and drop to reorder agenda items. Official notice items are shown alongside newly added items.
                    </p>

                    <div id="sortableAgenda" class="sortable-container">
                        @forelse($agendaItems as $item)
                            <div class="sortable-item border rounded p-3 mb-2 bg-white d-flex align-items-start gap-3"
                                 data-id="{{ $item->id }}"
                                 data-status="{{ $item->status }}">

                                {{-- Drag Handle --}}
                                <div class="drag-handle cursor-grab text-muted pt-1" style="cursor: grab;">
                                    <i class="mdi mdi-drag-vertical" style="font-size: 24px;"></i>
                                </div>

                                {{-- Order Badge --}}
                                <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 36px; height: 36px; min-width: 36px;">
                                    <span class="fw-bold text-primary order-number">{{ $item->display_order }}</span>
                                </div>

                                {{-- Item Content --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        @if($item->item_number)
                                            <span class="badge bg-soft-secondary text-secondary" style="font-size: 10px;">Item {{ $item->item_number }}</span>
                                        @endif
                                        {{-- Source badge --}}
                                        @if($item->source === 'notice')
                                            <span class="badge bg-soft-info text-info" style="font-size: 10px;">
                                                <i class="mdi mdi-file-document-outline me-1"></i>Official Notice
                                            </span>
                                        @elseif($item->source === 'cs_added')
                                            <span class="badge bg-soft-warning text-warning" style="font-size: 10px;">
                                                <i class="mdi mdi-account-plus-outline me-1"></i>Added by CS
                                            </span>
                                        @elseif($item->source === 'ddia_added')
                                            <span class="badge bg-soft-success text-success" style="font-size: 10px;">
                                                <i class="mdi mdi-shield-check-outline me-1"></i>Added by DDIA
                                            </span>
                                        @endif
                                        {{-- Status badge --}}
                                        <span class="badge bg-soft-{{ $item->status_color }} text-{{ $item->status_color }}" style="font-size: 10px;">
                                            <i class="mdi {{ $item->status_icon }} me-1"></i>{{ $item->status_label }}
                                        </span>
                                    </div>

                                    <h6 class="mb-1" style="font-size: 14px;">{{ $item->title }}</h6>

                                    @if($item->description)
                                        <p class="text-muted mb-1" style="font-size: 12px;">{{ Str::limit($item->description, 120) }}</p>
                                    @endif

                                    {{-- Board Paper Link --}}
                                    @if($item->boardPaper)
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-soft-primary text-primary" style="font-size: 11px;">
                                                <i class="mdi mdi-file-document-outline me-1"></i>
                                                {{ $item->boardPaper->paper_number ?? $item->boardPaper->title }}
                                            </span>
                                            <a href="{{ route('workflow.papers.show', $item->boardPaper) }}" class="text-primary" style="font-size: 11px;" target="_blank">
                                                <i class="mdi mdi-open-in-new"></i> View Paper
                                            </a>
                                        </div>
                                    @elseif($unassignedPapers->count() > 0)
                                        {{-- Allow assigning a paper to any item without one --}}
                                        <div class="mt-1">
                                            <button class="btn btn-sm btn-outline-secondary assign-paper-btn"
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-title="{{ $item->title }}"
                                                    style="font-size: 11px; padding: 2px 8px;">
                                                <i class="mdi mdi-link-plus me-1"></i> Assign Board Paper
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Added by info for new items --}}
                                    @if($item->source !== 'notice' && $item->addedByUser)
                                        <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                            Added by {{ $item->addedByUser->name }} {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex align-items-start gap-1 flex-shrink-0">
                                    @if($item->boardPaper)
                                        <a href="{{ route('workflow.papers.show', $item->boardPaper) }}" class="btn btn-sm btn-light" title="View Paper" target="_blank">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    @endif
                                    @if($item->canBePulledDown())
                                        <button type="button" class="btn btn-sm btn-outline-warning pulldown-btn"
                                                data-item-id="{{ $item->id }}"
                                                data-item-title="{{ $item->title }}"
                                                title="Pull down (hide from board members)">
                                            <i class="mdi mdi-eye-off-outline"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5" id="emptyState">
                                <i class="mdi mdi-file-document-off-outline" style="font-size: 48px; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">No agenda items available yet.</p>
                                <small>Agenda items are seeded from the official board notice once it has been sent to board members.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ═══ PENDING CEO APPROVAL (only visible to CS/DDIA) ═══ --}}
            @if($pendingItems->count() > 0)
                <div class="card border-warning">
                    <div class="card-header bg-soft-warning d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-warning">
                            <i class="mdi mdi-clock-outline me-1"></i> Pending CEO Approval
                        </h5>
                        <span class="badge bg-warning text-white">{{ $pendingItems->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($pendingItems as $pItem)
                            <div class="d-flex align-items-center gap-3 px-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="bg-soft-warning rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 36px; height: 36px; min-width: 36px;">
                                    <i class="mdi {{ $pItem->status === 'pending_pulldown' ? 'mdi-eye-remove-outline' : 'mdi-clock-outline' }} text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-soft-{{ $pItem->status === 'pending_pulldown' ? 'info' : 'warning' }} text-{{ $pItem->status === 'pending_pulldown' ? 'info' : 'warning' }}" style="font-size: 10px;">
                                            {{ $pItem->status === 'pending_pulldown' ? 'Pulldown Request' : 'New Item' }}
                                        </span>
                                    </div>
                                    <h6 class="mb-0" style="font-size: 14px;">{{ $pItem->title }}</h6>
                                    @if($pItem->status === 'pending_pulldown' && $pItem->pulldown_justification)
                                        <small class="text-muted d-block mt-1">Justification: {{ Str::limit($pItem->pulldown_justification, 100) }}</small>
                                    @endif
                                    <small class="text-muted">
                                        {{ $pItem->status === 'pending_pulldown' ? 'Requested' : 'Added' }} by {{ $pItem->addedByUser->name ?? 'Unknown' }}
                                        {{ $pItem->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ═══ PULLED-DOWN ITEMS (visible only to CS/DDIA) ═══ --}}
            @if($pulledDownItems->count() > 0)
                <div class="card border-dashed">
                    <div class="card-header bg-white" data-bs-toggle="collapse" data-bs-target="#pulledDownSection" style="cursor: pointer;">
                        <h6 class="mb-0 text-muted">
                            <i class="mdi mdi-chevron-down me-1"></i>
                            <i class="mdi mdi-eye-off-outline me-1"></i>
                            Pulled-Down Items ({{ $pulledDownItems->count() }})
                            <small class="text-danger ms-1">Hidden from board members</small>
                        </h6>
                    </div>
                    <div class="collapse" id="pulledDownSection">
                        <div class="card-body p-0">
                            @foreach($pulledDownItems as $pdItem)
                                <div class="d-flex align-items-center p-3 border-bottom bg-light">
                                    <div class="flex-grow-1">
                                        <span class="badge bg-soft-secondary text-secondary me-1" style="font-size: 10px;">Pulled Down</span>
                                        <span class="fw-semibold" style="font-size: 13px;">{{ $pdItem->title }}</span>
                                        <br>
                                        <small class="text-muted">
                                            Justification: {{ $pdItem->pulldown_justification }}
                                        </small>
                                        <br>
                                        <small class="text-muted" style="font-size: 11px;">
                                            Pulled by {{ $pdItem->pulldownRequestedByUser->name ?? 'Unknown' }}
                                            @if($pdItem->pulldown_approved_at)
                                                | Approved {{ $pdItem->pulldown_approved_at->diffForHumans() }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══ REJECTED ITEMS (collapsed) ═══ --}}
            @if($rejectedItems->count() > 0)
                <div class="card border-dashed">
                    <div class="card-header bg-white" data-bs-toggle="collapse" data-bs-target="#rejectedSection" style="cursor: pointer;">
                        <h6 class="mb-0 text-muted">
                            <i class="mdi mdi-chevron-down me-1"></i>
                            <i class="mdi mdi-close-circle-outline me-1 text-danger"></i>
                            Rejected Items ({{ $rejectedItems->count() }})
                        </h6>
                    </div>
                    <div class="collapse" id="rejectedSection">
                        <div class="card-body p-0">
                            @foreach($rejectedItems as $rItem)
                                <div class="d-flex align-items-center p-3 border-bottom bg-light">
                                    <div class="flex-grow-1">
                                        <span class="badge bg-soft-danger text-danger me-1" style="font-size: 10px;">Rejected</span>
                                        <span class="fw-semibold" style="font-size: 13px;">{{ $rItem->title }}</span>
                                        @if($rItem->rejected_reason)
                                            <br><small class="text-danger">Reason: {{ $rItem->rejected_reason }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <div class="col-lg-4">

            {{-- Preparation Status --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: 0.05em;">Preparation Status</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="mdi mdi-format-list-checks text-primary" style="font-size: 28px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $agendaItems->count() }}</h3>
                            <small class="text-muted">Active Agenda Items</small>
                        </div>
                    </div>

                    {{-- Quick stats --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-soft-success rounded p-2 text-center">
                                <h5 class="mb-0 text-success">{{ $agendaItems->where('source', 'notice')->count() }}</h5>
                                <small class="text-muted" style="font-size: 11px;">From Notice</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-soft-warning rounded p-2 text-center">
                                <h5 class="mb-0 text-warning">{{ $pendingItems->count() }}</h5>
                                <small class="text-muted" style="font-size: 11px;">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-soft-secondary rounded p-2 text-center">
                                <h5 class="mb-0 text-secondary">{{ $pulledDownItems->count() }}</h5>
                                <small class="text-muted" style="font-size: 11px;">Pulled Down</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-soft-info rounded p-2 text-center">
                                <h5 class="mb-0 text-info">{{ $agendaItems->where('board_paper_id', '!=', null)->count() }}</h5>
                                <small class="text-muted" style="font-size: 11px;">With Papers</small>
                            </div>
                        </div>
                    </div>

                    @if($preparation && $preparation->finalized_at)
                        <div class="alert alert-success mb-0" style="font-size: 13px;">
                            <i class="mdi mdi-check-decagram me-1"></i>
                            <strong>Finalized</strong> on {{ $preparation->finalized_at->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Meeting Details --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">Meeting Details</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                        <tr>
                            <td class="text-muted" style="width: 40%;"><i class="mdi mdi-calendar me-1"></i>Date:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>Time:</td>
                            <td>{{ $meeting->meeting_time ?? 'TBD' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="mdi mdi-map-marker-outline me-1"></i>Venue:</td>
                            <td>{{ $meeting->venue ?? 'TBD' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="mdi mdi-account-group me-1"></i>Committee:</td>
                            <td>{{ $meeting->boardCommittee->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Unassigned Papers --}}
            @if($unassignedPapers->count() > 0)
                <div class="card border-info">
                    <div class="card-header bg-soft-info">
                        <h6 class="mb-0 text-info"><i class="mdi mdi-file-document-alert-outline me-1"></i> Unassigned Papers</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($unassignedPapers as $uPaper)
                            <div class="d-flex align-items-center gap-2 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <i class="mdi mdi-file-document-outline text-info"></i>
                                <div class="flex-grow-1">
                                    <span style="font-size: 12px; font-weight: 600;">{{ $uPaper->paper_number ?? $uPaper->title }}</span>
                                    <br><small class="text-muted" style="font-size: 11px;">{{ $uPaper->author->name ?? '' }}</small>
                                </div>
                                <a href="{{ route('workflow.papers.show', $uPaper) }}" class="btn btn-sm btn-light" target="_blank" title="View">
                                    <i class="mdi mdi-eye-outline" style="font-size: 12px;"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Finalize --}}
            @if($agendaItems->count() > 0 && (!$preparation || !$preparation->finalized_at))
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="mdi mdi-check-decagram text-success" style="font-size: 42px;"></i>
                        <h5 class="mt-2 mb-1">Ready to Finalize?</h5>
                        <p class="text-muted mb-2" style="font-size: 13px;">
                            Finalizing will lock the agenda and prepare the meeting for launch.
                        </p>
                        @if($pendingItems->count() > 0)
                            <div class="alert alert-warning mb-3" style="font-size: 12px;">
                                <i class="mdi mdi-alert-outline me-1"></i>
                                {{ $pendingItems->count() }} item(s) are still pending CEO approval. You must resolve these before finalizing.
                            </div>
                        @endif
                        <button type="button" class="btn btn-success w-100" id="finalizeBtn"
                                {{ $pendingItems->count() > 0 ? 'disabled' : '' }}>
                            <i class="mdi mdi-lock-check-outline me-1"></i> Finalize Preparation
                        </button>
                    </div>
                </div>
            @endif

            {{-- Legend --}}
            <div class="card bg-soft-primary border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="mdi mdi-lightbulb-on-outline text-primary me-1"></i> Guide</h6>
                    <ul class="mb-0" style="font-size: 12px; padding-left: 16px;">
                        <li class="mb-1">Drag items to reorder the agenda</li>
                        <li class="mb-1"><span class="badge bg-soft-info text-info" style="font-size: 9px;">Official Notice</span> items are seeded from the board notice</li>
                        <li class="mb-1">Use <strong>"New Agenda Item"</strong> to introduce new items with a board paper</li>
                        <li class="mb-1"><i class="mdi mdi-eye-off-outline text-warning"></i> pulls down an item (hides from board members)</li>
                        @if(!$isAudit)
                            <li class="mb-1">New items and pulldowns require CEO approval</li>
                        @else
                            <li class="mb-1">DDIA has full powers — no CEO approval needed</li>
                        @endif
                        <li>Finalizing locks the agenda and cannot be undone</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ ADD AGENDA ITEM MODAL ═══ --}}
<div class="modal fade" id="addAgendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline text-primary me-1"></i> Add New Agenda Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.prepare.add-item', $meeting) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if(!$isAudit)
                        <div class="alert alert-info" style="font-size: 13px;">
                            <i class="mdi mdi-information-outline me-1"></i>
                            New agenda items introduced by Corporation Secretary require CEO approval. The board paper will also need CEO signature.
                        </div>
                    @else
                        <div class="alert alert-success" style="font-size: 13px;">
                            <i class="mdi mdi-shield-check-outline me-1"></i>
                            As DDIA, your new agenda items are auto-approved. No CEO approval is required.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Item Number</label>
                            <input type="text" name="item_number" class="form-control" placeholder="e.g. 5.0">
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Agenda item title" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the agenda item..."></textarea>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="mdi mdi-file-upload-outline me-1"></i> Board Paper (Optional)</h6>
                    <p class="text-muted mb-2" style="font-size: 12px;">
                        Upload a board paper document to accompany this agenda item.
                        @if(!$isAudit) The CEO will review and sign the board paper. @endif
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Board Paper Document</label>
                        <input type="file" name="board_paper" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">PDF, DOC, or DOCX. Max 20MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Annexures</label>
                        <input type="file" name="annexures[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                        <small class="text-muted">Supporting documents. Multiple files allowed. Max 20MB each.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Add Agenda Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ PULLDOWN MODAL ═══ --}}
<div class="modal fade" id="pulldownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-warning">
                <h5 class="modal-title"><i class="mdi mdi-eye-off-outline text-warning me-1"></i> Pull Down Agenda Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.prepare.pulldown', $meeting) }}" method="POST" id="pulldownForm">
                @csrf
                <input type="hidden" name="item_id" id="pulldownItemId">
                <div class="modal-body">
                    <p class="mb-3" style="font-size: 14px;">
                        Pull down <strong id="pulldownItemTitle"></strong> from the agenda?
                    </p>
                    <p class="text-muted mb-3" style="font-size: 13px;">
                        This will hide the item from board members.
                        @if(!$isAudit)
                            A justification is required and will be sent to the CEO for approval.
                        @else
                            As DDIA, pulldowns are immediate.
                        @endif
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Justification <span class="text-danger">*</span></label>
                        <textarea name="justification" class="form-control" rows="3" placeholder="Explain why this item should be pulled down..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="mdi mdi-eye-off-outline me-1"></i> Pull Down
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ ASSIGN PAPER MODAL ═══ --}}
<div class="modal fade" id="assignPaperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-link-plus text-primary me-1"></i> Assign Board Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('workflow.prepare.assign-paper', $meeting) }}" method="POST" id="assignPaperForm">
                @csrf
                <input type="hidden" name="item_id" id="assignPaperItemId">
                <div class="modal-body">
                    <p class="mb-3" style="font-size: 14px;">
                        Assign a board paper to <strong id="assignPaperItemTitle"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Board Paper</label>
                        <select name="paper_id" class="form-select" required>
                            <option value="">-- Select a paper --</option>
                            @foreach($unassignedPapers as $uPaper)
                                <option value="{{ $uPaper->id }}">
                                    {{ $uPaper->paper_number ?? '' }} &mdash; {{ $uPaper->title }}
                                    ({{ $uPaper->author->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-link me-1"></i> Assign Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .sortable-item { transition: box-shadow 0.2s ease, transform 0.15s ease; }
    .sortable-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .sortable-ghost { opacity: 0.4; background: #ececec !important; }
    .sortable-chosen { box-shadow: 0 8px 24px rgba(200, 16, 46,0.15); transform: scale(1.01); }
    .sortable-drag { box-shadow: 0 12px 32px rgba(0,0,0,0.12); }
    .border-dashed { border-style: dashed !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
$(document).ready(function() {

    // ── Sortable Drag & Drop ──────────────────────────────
    var el = document.getElementById('sortableAgenda');
    if (el && el.children.length > 0) {
        Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            filter: '[data-status="pending_approval"]', // can't drag pending items
            onEnd: function(evt) {
                updateOrderNumbers();
                saveOrder();
            }
        });
    }

    function updateOrderNumbers() {
        $('#sortableAgenda .sortable-item').each(function(i) {
            $(this).find('.order-number').text(i + 1);
        });
    }

    function saveOrder() {
        var order = [];
        $('#sortableAgenda .sortable-item').each(function() {
            order.push(parseInt($(this).data('id')));
        });

        $.ajax({
            url: '{{ route("workflow.prepare.reorder", $meeting) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { order: order },
            success: function() {
                toastr.success('Agenda order updated.');
            },
            error: function() {
                toastr.error('Failed to save order.');
            }
        });
    }

    // ── Pulldown ──────────────────────────────────────────
    $(document).on('click', '.pulldown-btn', function() {
        var itemId = $(this).data('item-id');
        var itemTitle = $(this).data('item-title');
        $('#pulldownItemId').val(itemId);
        $('#pulldownItemTitle').text(itemTitle);
        $('#pulldownModal').modal('show');
    });

    // ── Assign Paper ──────────────────────────────────────
    $(document).on('click', '.assign-paper-btn', function() {
        var itemId = $(this).data('item-id');
        var itemTitle = $(this).data('item-title');
        $('#assignPaperItemId').val(itemId);
        $('#assignPaperItemTitle').text(itemTitle);
        $('#assignPaperModal').modal('show');
    });

    // ── Finalize ──────────────────────────────────────────
    $('#finalizeBtn').on('click', function() {
        Swal.fire({
            title: 'Finalize Meeting Preparation?',
            html: '<p>This will lock the agenda order and mark the meeting as ready for launch.</p><p class="text-danger fw-semibold">This action cannot be undone.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#099139',
            confirmButtonText: '<i class="mdi mdi-lock-check-outline me-1"></i> Yes, Finalize',
            cancelButtonText: 'Not Yet'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("workflow.prepare.finalize", $meeting) }}',
                    method: 'POST',
                    dataType: 'json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        toastr.success(response.message || 'Meeting preparation finalized!');
                        if (response.redirect) {
                            setTimeout(function() { window.location.href = response.redirect; }, 1000);
                        } else {
                            setTimeout(function() { location.reload(); }, 1000);
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Failed to finalize preparation.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            }
        });
    });
});
</script>
@endpush
