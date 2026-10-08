@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1">Almanac Approvals</h4>
                        <p class="text-muted mb-0">Review and approve board meeting almanacs submitted for CEO approval</p>
                    </div>
                    <a href="{{ route('workflow.dashboard') }}" class="btn btn-sm btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Strip --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card mb-0">
                    <div class="card-body py-3 px-3 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px; background: #fff8dd;">
                                <i class="mdi mdi-clock-alert-outline" style="font-size: 18px; color: #b57d00;"></i>
                            </div>
                            <div class="text-start">
                                <h4 class="mb-0 fw-bold" style="color: #b57d00;">{{ $stats['pending'] }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Pending</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card mb-0">
                    <div class="card-body py-3 px-3 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px; background: #e6f6ec;">
                                <i class="mdi mdi-check-decagram-outline" style="font-size: 18px; color: #099139;"></i>
                            </div>
                            <div class="text-start">
                                <h4 class="mb-0 fw-bold" style="color: #099139;">{{ $stats['approved'] }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Approved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card mb-0">
                    <div class="card-body py-3 px-3 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px; background: #fff5f8;">
                                <i class="mdi mdi-close-circle-outline" style="font-size: 18px; color: #f1416c;"></i>
                            </div>
                            <div class="text-start">
                                <h4 class="mb-0 fw-bold" style="color: #f1416c;">{{ $stats['rejected'] }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card mb-0">
                    <div class="card-body py-3 px-3 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px; background: #f8f8f8;">
                                <i class="mdi mdi-file-document-multiple-outline" style="font-size: 18px; color: #c50010;"></i>
                            </div>
                            <div class="text-start">
                                <h4 class="mb-0 fw-bold" style="color: #c50010;">{{ $stats['total'] }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Almanacs Section --}}
        @if($pendingAlmanacs->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-left: 3px solid #b57d00;">
                <h6 class="mb-0">
                    <i class="mdi mdi-clock-alert-outline me-1" style="color: #b57d00;"></i> Awaiting Your Approval
                    <span class="badge ms-1" style="background: #fff8dd; color: #b57d00;">{{ $pendingAlmanacs->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach($pendingAlmanacs as $almanac)
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #212121 100%); flex-shrink: 0;">
                            <i class="mdi mdi-book-open-page-variant text-white" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ $almanac->serialno }}</h6>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted" style="font-size: 12px;">
                                    <i class="mdi mdi-calendar-range me-1"></i>FY {{ $almanac->financialyear->year ?? '-' }}
                                </span>
                                <span class="text-muted" style="font-size: 12px;">
                                    <i class="mdi mdi-cash me-1"></i>KES {{ number_format($almanac->budget, 2) }}
                                </span>
                                <span class="text-muted" style="font-size: 12px;">
                                    <i class="mdi mdi-account-outline me-1"></i>{{ $almanac->created_by ?? 'Unknown' }}
                                </span>
                                <span class="badge" style="background: #fff8dd; color: #b57d00; font-size: 10px;">
                                    {{ $almanac->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('workflow.ceo.almanac.show', $almanac->id) }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-eye-outline me-1"></i> Review
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card mb-4">
            <div class="card-header" style="border-left: 3px solid #b57d00;">
                <h6 class="mb-0">
                    <i class="mdi mdi-clock-alert-outline me-1" style="color: #b57d00;"></i> Awaiting Your Approval
                </h6>
            </div>
            <div class="card-body text-center py-5">
                <i class="mdi mdi-check-all text-muted" style="font-size: 48px;"></i>
                <h6 class="mt-2 mb-1">All Clear</h6>
                <p class="text-muted mb-0" style="font-size: 13px;">No almanacs pending approval at this time.</p>
            </div>
        </div>
        @endif

        {{-- Approved Almanacs Section --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-left: 3px solid #099139;">
                <h6 class="mb-0">
                    <i class="mdi mdi-check-decagram-outline me-1" style="color: #099139;"></i> Approved Almanacs
                    <span class="badge ms-1" style="background: #e6f6ec; color: #099139;">{{ $approvedAlmanacs->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($approvedAlmanacs as $almanac)
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; background: #e6f6ec; flex-shrink: 0;">
                            <i class="mdi mdi-check-decagram" style="font-size: 20px; color: #099139;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold" style="font-size: 13px;">{{ $almanac->serialno }}</h6>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted" style="font-size: 11px;">
                                    FY {{ $almanac->financialyear->year ?? '-' }}
                                </span>
                                <span class="text-muted" style="font-size: 11px;">
                                    KES {{ number_format($almanac->budget, 2) }}
                                </span>
                                @if($almanac->ceo_approval_date)
                                <span class="text-muted" style="font-size: 11px;">
                                    <i class="mdi mdi-calendar-check me-1"></i>{{ $almanac->ceo_approval_date->format('d M Y') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('workflow.ceo.almanac.show', $almanac->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-eye-outline"></i> View
                    </a>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-muted mb-0" style="font-size: 13px;">No approved almanacs yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Rejected Almanacs Section --}}
        @if($rejectedAlmanacs->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-left: 3px solid #f1416c;">
                <h6 class="mb-0">
                    <i class="mdi mdi-close-circle-outline me-1" style="color: #f1416c;"></i> Rejected Almanacs
                    <span class="badge ms-1" style="background: #fff5f8; color: #f1416c;">{{ $rejectedAlmanacs->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach($rejectedAlmanacs as $almanac)
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; background: #fff5f8; flex-shrink: 0;">
                            <i class="mdi mdi-close-circle" style="font-size: 20px; color: #f1416c;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold" style="font-size: 13px;">{{ $almanac->serialno }}</h6>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted" style="font-size: 11px;">
                                    FY {{ $almanac->financialyear->year ?? '-' }}
                                </span>
                                <span class="text-muted" style="font-size: 11px;">
                                    KES {{ number_format($almanac->budget, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('workflow.ceo.almanac.show', $almanac->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-eye-outline"></i> View
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
