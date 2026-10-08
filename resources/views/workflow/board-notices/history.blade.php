@extends('admin_dashboard')

@push('styles')
<style>
    .history-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.04); overflow:hidden; }
    .history-card .card-header { background:#fff; border-bottom:2px solid {{ setting('primary_color', '#1a1a1a') }}; padding:.85rem 1.25rem; }
    .history-card .card-header h5 { margin:0; font-size:.95rem; font-weight:600; color:{{ setting('primary_color', '#1a1a1a') }}; }
    .timeline-item { position:relative; padding-left:36px; padding-bottom:24px; }
    .timeline-item:not(:last-child)::before {
        content:''; position:absolute; left:14px; top:28px; bottom:0;
        width:2px; background:#e9ecef;
    }
    .timeline-dot {
        position:absolute; left:4px; top:4px;
        width:22px; height:22px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-size:11px; color:#fff;
    }
    .dot-created { background:{{ setting('primary_color', '#1a1a1a') }}; }
    .dot-submitted { background:#0fee5d; }
    .dot-approved { background:#099139; }
    .dot-rejected { background:#dc3545; }
    .dot-sent { background:{{ setting('primary_color', '#1a1a1a') }}; }
    .dot-reminder { background:#fd7e14; }
    .dot-updated { background:#238b47; }
    .dot-rescheduled { background:#e83e8c; }
    .dot-default { background:#6c757d; }
</style>
@endpush

@section('admin')
<div class="content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <a href="{{ route('workflow.board-notice.preview', $notice) }}" class="btn btn-sm btn-light rounded-pill mb-1">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Notice
                </a>
                <h3 class="mb-0">
                    <i class="mdi mdi-history me-1" style="color:{{ setting('primary_color', '#1a1a1a') }};"></i> Notice Audit Trail
                </h3>
                <p class="text-muted mb-0">{{ $notice->reference_number }} &mdash; {{ $notice->subject }}</p>
            </div>
            <span class="badge bg-{{ $notice->status_color }} px-3 py-2" style="font-size:12px;">
                <i class="mdi {{ $notice->status_icon }} me-1"></i>{{ ucfirst($notice->status) }}
            </span>
        </div>

        <div class="row">
            {{-- Left: Timeline --}}
            <div class="col-lg-8">
                <div class="card history-card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-timeline-clock-outline me-1"></i> Activity Timeline</h5>
                    </div>
                    <div class="card-body">
                        @forelse($auditLogs as $log)
                            @php
                                $dotClass = 'dot-default';
                                $icon = 'mdi-circle-small';
                                if (str_contains($log->action, 'created')) { $dotClass = 'dot-created'; $icon = 'mdi-plus'; }
                                elseif (str_contains($log->action, 'submitted')) { $dotClass = 'dot-submitted'; $icon = 'mdi-send'; }
                                elseif (str_contains($log->action, 'approved')) { $dotClass = 'dot-approved'; $icon = 'mdi-check'; }
                                elseif (str_contains($log->action, 'rejected')) { $dotClass = 'dot-rejected'; $icon = 'mdi-close'; }
                                elseif (str_contains($log->action, 'sent')) { $dotClass = 'dot-sent'; $icon = 'mdi-email-outline'; }
                                elseif (str_contains($log->action, 'reminder')) { $dotClass = 'dot-reminder'; $icon = 'mdi-bell-outline'; }
                                elseif (str_contains($log->action, 'updated')) { $dotClass = 'dot-updated'; $icon = 'mdi-pencil'; }
                                elseif (str_contains($log->action, 'rescheduled')) { $dotClass = 'dot-rescheduled'; $icon = 'mdi-calendar-refresh'; }
                            @endphp
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $dotClass }}">
                                    <i class="mdi {{ $icon }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem;">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </div>
                                    <p class="text-muted mb-1" style="font-size:.85rem;">{{ $log->description }}</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="mdi mdi-account-outline me-1"></i>{{ $log->user->name ?? 'System' }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline me-1"></i>{{ $log->created_at->format('d M Y, h:i A') }}
                                        </small>
                                        @if($log->ip_address)
                                            <small class="text-muted">
                                                <i class="mdi mdi-ip-network-outline me-1"></i>{{ $log->ip_address }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-history d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                                <p class="mb-0">No audit logs found for this notice.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Notice Summary & Chain --}}
            <div class="col-lg-4">
                {{-- Notice Summary --}}
                <div class="card history-card mb-3">
                    <div class="card-header">
                        <h5><i class="mdi mdi-information-outline me-1"></i> Notice Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0" style="font-size:.85rem;">
                            <tr>
                                <td class="text-muted" style="width:110px;">Reference</td>
                                <td class="fw-semibold"><code>{{ $notice->reference_number }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type</td>
                                <td>{{ $notice->notice_type === '21_day' ? '21-Day' : '7-Day' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Version</td>
                                <td><span class="badge bg-light text-dark">v{{ $notice->version ?? 1 }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created By</td>
                                <td>{{ $notice->creator->name ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created</td>
                                <td>{{ $notice->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @if($notice->approved_by)
                                <tr>
                                    <td class="text-muted">Approved By</td>
                                    <td>{{ $notice->approver->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Approved</td>
                                    <td>{{ $notice->approved_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            @endif
                            @if($notice->sent_at)
                                <tr>
                                    <td class="text-muted">Sent</td>
                                    <td>{{ $notice->sent_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Reminders</td>
                                    <td>{{ $notice->reminder_count ?? 0 }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Notice Chain (parent / replacements) --}}
                @if($notice->parentNotice || ($notice->replacementNotices && $notice->replacementNotices->count()))
                    <div class="card history-card mb-3">
                        <div class="card-header">
                            <h5><i class="mdi mdi-link-variant me-1"></i> Notice Chain</h5>
                        </div>
                        <div class="card-body">
                            @if($notice->parentNotice)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="mdi mdi-arrow-up-bold-circle text-muted"></i>
                                    <div>
                                        <div style="font-size:.85rem;" class="fw-semibold">
                                            <a href="{{ route('workflow.board-notice.history', $notice->parentNotice) }}" class="text-decoration-none">
                                                {{ $notice->parentNotice->reference_number }}
                                            </a>
                                        </div>
                                        <small class="text-muted">Original Notice (v{{ $notice->parentNotice->version ?? 1 }})</small>
                                    </div>
                                </div>
                            @endif

                            @if($notice->replacementNotices && $notice->replacementNotices->count())
                                @foreach($notice->replacementNotices as $replacement)
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="mdi mdi-arrow-down-bold-circle text-primary"></i>
                                        <div>
                                            <div style="font-size:.85rem;" class="fw-semibold">
                                                <a href="{{ route('workflow.board-notice.history', $replacement) }}" class="text-decoration-none">
                                                    {{ $replacement->reference_number }}
                                                </a>
                                            </div>
                                            <small class="text-muted">Replacement (v{{ $replacement->version ?? 1 }}) — {{ ucfirst($replacement->status) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Comments --}}
                @if($notice->comments && $notice->comments->count())
                    <div class="card history-card">
                        <div class="card-header">
                            <h5><i class="mdi mdi-comment-text-multiple-outline me-1"></i> Comments</h5>
                        </div>
                        <div class="card-body">
                            @foreach($notice->comments as $comment)
                                <div class="{{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-semibold" style="font-size:.85rem;">{{ $comment->user->name ?? 'System' }}</span>
                                        <span class="badge bg-{{ $comment->action === 'rejected' ? 'danger' : 'secondary' }}" style="font-size:10px;">
                                            {{ ucfirst($comment->action) }}
                                        </span>
                                    </div>
                                    <p class="mb-1" style="font-size:.85rem;">{{ $comment->comment }}</p>
                                    <small class="text-muted">{{ $comment->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
