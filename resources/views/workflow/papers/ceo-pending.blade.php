@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0">
                <i class="mdi mdi-shield-check-outline text-primary me-1"></i>
                {{ !empty($isDDIA) && $isDDIA ? 'Audit Committee Paper Review' : 'CEO Board Paper Review' }}
            </h3>
            <p class="text-muted mb-0">
                @if(!empty($isDDIA) && $isDDIA)
                    Review and approve board papers for Audit Committee meetings.
                @else
                    Review and approve board papers forwarded by directors for upcoming meetings.
                @endif
            </p>
        </div>
        <div>
            @php
                $totalPending = $meetings->sum('pending_papers_count');
            @endphp
            <span class="badge bg-soft-warning text-warning px-3 py-2" style="font-size: 13px;">
                <i class="mdi mdi-clock-outline me-1"></i> {{ $totalPending }} paper{{ $totalPending !== 1 ? 's' : '' }} pending review
            </span>
        </div>
    </div>

    @if($meetings->count())
        <div class="row g-3">
            @foreach($meetings as $meeting)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            {{-- Meeting Header --}}
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-soft-primary rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="mdi mdi-file-document-multiple-outline text-primary" style="font-size: 22px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1" style="font-size: 15px;">{{ $meeting->title }}</h5>
                                    <small class="text-muted">
                                        <i class="mdi mdi-account-group-outline me-1"></i>{{ $meeting->boardCommittee->name ?? 'General' }}
                                    </small>
                                </div>
                            </div>

                            {{-- Meeting Info --}}
                            <div class="bg-light rounded p-2 mb-3" style="font-size: 12px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted"><i class="mdi mdi-calendar me-1"></i> Meeting Date:</span>
                                    <span class="fw-semibold">{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') : 'TBD' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="mdi mdi-file-clock-outline me-1"></i> Pending Papers:</span>
                                    <span class="badge bg-warning text-dark">{{ $meeting->pending_papers_count }}</span>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <a href="{{ route('workflow.ceo.papers.show', $meeting) }}" class="btn btn-primary w-100">
                                <i class="mdi mdi-eye-check-outline me-1"></i> Review Papers
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $meetings->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-check-circle-outline text-success" style="font-size: 64px; opacity: 0.3;"></i>
                <h4 class="mt-3 text-muted">All caught up!</h4>
                <p class="text-muted mb-0">There are no board papers pending your review at the moment.{{ !empty($isDDIA) && $isDDIA ? ' (Audit Committee only)' : '' }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
