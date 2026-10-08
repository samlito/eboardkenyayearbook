@extends('admin_dashboard')

@section('admin')
<div class="content">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0"><i class="mdi mdi-format-list-checks text-primary me-1"></i> Agenda Items Pending Approval</h3>
            <p class="text-muted mb-0">Review new agenda items and pulldown requests requiring your decision.</p>
        </div>
    </div>

    @if($pendingItems->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="font-size: 12px;">Type</th>
                                <th style="font-size: 12px;">Agenda Item</th>
                                <th style="font-size: 12px;">Meeting</th>
                                <th style="font-size: 12px;">Committee</th>
                                <th style="font-size: 12px;">Requested By</th>
                                <th style="font-size: 12px;">Date</th>
                                <th style="font-size: 12px; width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingItems as $item)
                                <tr>
                                    <td>
                                        @if($item->status === 'pending_pulldown')
                                            <span class="badge bg-soft-info text-info">
                                                <i class="mdi mdi-eye-off-outline me-1"></i>Pulldown
                                            </span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">
                                                <i class="mdi mdi-plus-circle-outline me-1"></i>New Item
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="font-size: 13px;">{{ $item->title }}</span>
                                        @if($item->boardPaper)
                                            <br><small class="text-primary"><i class="mdi mdi-file-document-outline me-1"></i>Has board paper</small>
                                        @endif
                                        @if($item->status === 'pending_pulldown' && $item->pulldown_justification)
                                            <br><small class="text-muted">Justification: {{ Str::limit($item->pulldown_justification, 80) }}</small>
                                        @endif
                                    </td>
                                    <td style="font-size: 13px;">{{ $item->meeting->title ?? 'N/A' }}</td>
                                    <td style="font-size: 13px;">{{ $item->meeting->boardCommittee->name ?? 'N/A' }}</td>
                                    <td style="font-size: 13px;">
                                        @if($item->status === 'pending_pulldown')
                                            {{ $item->pulldownRequestedByUser->name ?? 'Unknown' }}
                                        @else
                                            {{ $item->addedByUser->name ?? 'Unknown' }}
                                        @endif
                                    </td>
                                    <td style="font-size: 12px;">{{ $item->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('workflow.ceo.agenda-item.review', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-eye-outline me-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $pendingItems->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-check-circle-outline text-success" style="font-size: 64px; opacity: 0.4;"></i>
                <h5 class="mt-3 text-muted">No Pending Items</h5>
                <p class="text-muted">All agenda items and pulldown requests have been resolved.</p>
            </div>
        </div>
    @endif
</div>
@endsection
