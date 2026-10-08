@extends('admin_dashboard')

@section('admin')
<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,{{ setting('primary_color', '#1a1a1a') }},#b8000f);display:flex;align-items:center;justify-content:center;">
                            <i class="mdi mdi-file-check-outline" style="font-size:24px;color:#fff;"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">Board Notices — CEO Approval</h4>
                            <p class="text-muted mb-0" style="font-size:13px;">Review and approve official board meeting notices</p>
                        </div>
                    </div>
                    <a href="{{ route('workflow.dashboard') }}" class="btn btn-sm btn-light rounded-pill">
                        <i class="mdi mdi-arrow-left me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Notices Table --}}
        <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <div class="card-header bg-white py-3" style="border-bottom:1px solid #f0f2f5;">
                <h5 class="mb-0 fw-semibold">
                    <i class="mdi mdi-clock-alert-outline me-1 text-warning"></i> Pending Notices
                    <span class="badge bg-warning text-dark ms-2">{{ $notices->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr style="background:#fafafa;">
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">#</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">Meeting</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">Type</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">Reference</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">Submitted By</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;">Submitted</th>
                                <th style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;padding:12px 16px;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notices as $index => $notice)
                                <tr>
                                    <td class="text-muted fw-semibold" style="padding:14px 16px;">{{ $notices->firstItem() + $index }}</td>
                                    <td style="padding:14px 16px;">
                                        <div class="fw-semibold" style="font-size:13px;">{{ Str::limit($notice->meeting->title ?? '', 50) }}</div>
                                        @if($notice->meeting && $notice->meeting->boardCommittee)
                                            <small class="text-muted">{{ $notice->meeting->boardCommittee->name }}</small>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <span class="badge" style="background:{{ $notice->notice_type === '21_day' ? 'rgba(34,34,34,.1)' : 'rgba(9,145,57,.1)' }};color:{{ $notice->notice_type === '21_day' ? setting('primary_color', '#1a1a1a') : '#099139' }};font-size:11px;padding:5px 10px;">
                                            {{ $notice->notice_type === '21_day' ? '21-Day' : '7-Day' }}
                                        </span>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <code style="font-size:12px;">{{ $notice->reference_number }}</code>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <span style="font-size:13px;">{{ $notice->creator->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <span style="font-size:13px;">{{ $notice->submitted_at ? $notice->submitted_at->format('d M Y') : '—' }}</span>
                                        @if($notice->submitted_at)
                                            <br><small class="text-muted">{{ $notice->submitted_at->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end" style="padding:14px 16px;">
                                        <a href="{{ route('workflow.ceo.board-notice.review', $notice) }}" class="btn btn-sm btn-primary rounded-pill">
                                            <i class="mdi mdi-eye-outline me-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div style="width:72px;height:72px;border-radius:50%;background:#f8f8f8;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                            <i class="mdi mdi-check-all" style="font-size:36px;color:#099139;"></i>
                                        </div>
                                        <h6 class="text-muted mb-1">All Clear</h6>
                                        <p class="text-muted mb-0" style="font-size:13px;">No board notices awaiting your approval.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notices->hasPages())
                    <div class="px-3 py-3 border-top">
                        {{ $notices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
