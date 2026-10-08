@extends('admin_dashboard')

@section('admin')
<div class="content">
    <div class="container-fluid">

        {{-- ═══════════════════════════════════════════════════════════
             PAGE HEADER
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bn-header-icon">
                            <i class="mdi mdi-bulletin-board"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">Board Notices</h4>
                            <p class="text-muted mb-0" style="font-size: 13px;">Create and manage official notices for board meetings</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('workflow.dashboard') }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="mdi mdi-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SUMMARY STATS — Glassmorphism cards
             ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="bn-stat-card">
                    <div class="bn-stat-icon" style="background: rgba(200, 16, 46, 0.1);">
                        <i class="mdi mdi-file-document-multiple-outline" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Total Notices</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="bn-stat-card">
                    <div class="bn-stat-icon" style="background: rgba(255, 193, 7, 0.12);">
                        <i class="mdi mdi-clock-alert-outline" style="color: #b57d00;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold" style="color: #b57d00;">{{ $stats['pending'] }}</h3>
                        <small class="text-muted">Pending Approval</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="bn-stat-card">
                    <div class="bn-stat-icon" style="background: rgba(9, 145, 57, 0.1);">
                        <i class="mdi mdi-check-decagram-outline" style="color: #099139;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold" style="color: #099139;">{{ $stats['approved'] }}</h3>
                        <small class="text-muted">Approved &amp; Ready</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="bn-stat-card">
                    <div class="bn-stat-icon" style="background: rgba(200, 16, 46, 0.08);">
                        <i class="mdi mdi-email-check-outline" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $stats['sent'] }}</h3>
                        <small class="text-muted">Sent to Board</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             INITIATE NEW NOTICE — Available Meetings
             ═══════════════════════════════════════════════════════════ --}}
        @if(isset($availableMeetings) && $availableMeetings->count())
            <div class="card bn-table-card mb-4">
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="mdi mdi-plus-circle-outline me-1" style="color: #099139;"></i> Initiate a Board Notice
                    </h5>
                    <small class="text-muted">Select a meeting to create an official board notice</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="bn-table-head">
                                    <th style="padding: 10px 16px;">Meeting</th>
                                    <th style="padding: 10px 16px; width: 160px;">Committee</th>
                                    <th style="padding: 10px 16px; width: 130px;">Meeting Date</th>
                                    <th style="padding: 10px 16px; width: 100px;">Stage</th>
                                    <th style="padding: 10px 16px; width: 120px;">Notice</th>
                                    <th style="padding: 10px 16px; width: 140px;" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableMeetings as $meeting)
                                    <tr>
                                        <td style="padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bn-meeting-icon flex-shrink-0">
                                                    <i class="mdi mdi-calendar-clock"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('workflow.meetings.show', $meeting) }}" class="fw-semibold text-dark text-decoration-none bn-meeting-link" style="font-size: 13px;">
                                                        {{ Str::limit($meeting->title, 55) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if($meeting->boardCommittee)
                                                <span class="badge bg-soft-primary text-primary" style="font-size: 10.5px;">
                                                    {{ $meeting->boardCommittee->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if($meeting->meeting_date)
                                                <span style="font-size: 13px;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</span>
                                            @else
                                                <span class="text-muted">TBD</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <span class="badge bg-light text-dark" style="font-size: 11px;">
                                                Stage {{ $meeting->current_stage }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if($meeting->has_notice)
                                                <span class="badge bg-soft-success text-success" style="font-size: 11px;">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i>Created
                                                </span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning" style="font-size: 11px;">
                                                    <i class="mdi mdi-alert-circle-outline me-1"></i>None
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end" style="padding: 12px 16px;">
                                            <a href="{{ route('workflow.board-notice.create', $meeting) }}"
                                               class="btn btn-sm {{ $meeting->has_notice ? 'btn-outline-primary' : 'text-white' }} rounded-pill"
                                               style="{{ $meeting->has_notice ? '' : 'background:' . setting('primary_color', '#1a1a1a') . ';' }}">
                                                @if($meeting->has_notice)
                                                    <i class="mdi mdi-pencil-outline me-1"></i> Edit Notice
                                                @else
                                                    <i class="mdi mdi-plus me-1"></i> Create Notice
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             NOTICES TABLE — Existing notices
             ═══════════════════════════════════════════════════════════ --}}
        <div class="card bn-table-card">
            <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="mdi mdi-format-list-bulleted-square me-1 text-primary"></i> All Notices
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-filter-variant me-1"></i> <span id="filterLabel">All Statuses</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="statusFilterBtn">
                            <li><a class="dropdown-item bn-filter-item active" href="#" data-status="all"><i class="mdi mdi-format-list-bulleted me-2 text-muted"></i>All Statuses</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item bn-filter-item" href="#" data-status="draft"><i class="mdi mdi-file-edit-outline me-2 text-warning"></i>Draft</a></li>
                            <li><a class="dropdown-item bn-filter-item" href="#" data-status="submitted"><i class="mdi mdi-clock-outline me-2 text-info"></i>Submitted</a></li>
                            <li><a class="dropdown-item bn-filter-item" href="#" data-status="approved"><i class="mdi mdi-check-circle-outline me-2 text-success"></i>Approved</a></li>
                            <li><a class="dropdown-item bn-filter-item" href="#" data-status="sent"><i class="mdi mdi-email-check-outline me-2 text-primary"></i>Sent</a></li>
                            <li><a class="dropdown-item bn-filter-item" href="#" data-status="rejected"><i class="mdi mdi-close-circle-outline me-2 text-danger"></i>Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 twwda-datatable" id="noticesTable">
                        <thead>
                            <tr class="bn-table-head">
                                <th style="width: 40px;">#</th>
                                <th>Meeting</th>
                                <th style="width: 110px;">Notice Type</th>
                                <th>Reference No.</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 140px;">Created</th>
                                <th style="width: 110px;">Sent</th>
                                <th style="width: 140px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notices as $index => $notice)
                                <tr class="bn-table-row" data-status="{{ $notice->status }}">
                                    <td>
                                        <span class="text-muted fw-semibold">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="bn-meeting-icon flex-shrink-0">
                                                <i class="mdi mdi-calendar-clock"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('workflow.meetings.show', $notice->meeting) }}" class="fw-semibold text-dark text-decoration-none bn-meeting-link" style="font-size: 13px;">
                                                    {{ Str::limit($notice->meeting->title, 45) }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    @if($notice->meeting->boardCommittee)
                                                        <span class="badge bg-soft-primary text-primary" style="font-size: 10px;">
                                                            {{ $notice->meeting->boardCommittee->name }}
                                                        </span>
                                                    @endif
                                                    <small class="text-muted" style="font-size: 11px;">
                                                        <i class="mdi mdi-calendar-outline me-1"></i>{{ \Carbon\Carbon::parse($notice->meeting->meeting_date)->format('d M Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($notice->notice_type === '21_day')
                                            <span class="badge bn-type-badge bn-type-21">
                                                <i class="mdi mdi-calendar-range me-1"></i>21-Day
                                            </span>
                                        @else
                                            <span class="badge bn-type-badge bn-type-7">
                                                <i class="mdi mdi-calendar-week me-1"></i>7-Day
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="font-size: 13px; font-family: 'SFMono-Regular', Consolas, monospace;">
                                            {{ $notice->reference_number ?? '---' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-{{ $notice->status_color }} text-{{ $notice->status_color }} bn-status-badge">
                                            <i class="mdi {{ $notice->status_icon }} me-1"></i>{{ ucfirst($notice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px;">{{ $notice->created_at->format('d M Y') }}</div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $notice->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($notice->sent_at)
                                            <div style="font-size: 13px;">{{ $notice->sent_at->format('d M Y') }}</div>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="{{ route('workflow.board-notice.preview', $notice) }}" class="btn btn-sm btn-light bn-action-btn" title="View Notice">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                            @if($notice->isEditable())
                                                <a href="{{ route('workflow.board-notice.create', $notice->meeting) }}" class="btn btn-sm btn-soft-primary bn-action-btn" title="Edit Notice">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('workflow.board-notice.history', $notice) }}" class="btn btn-sm btn-light bn-action-btn" title="Audit Trail">
                                                <i class="mdi mdi-history"></i>
                                            </a>
                                            @if($notice->canSendReminder())
                                                <button type="button" class="btn btn-sm btn-soft-info bn-action-btn btn-send-reminder"
                                                        data-notice-id="{{ $notice->id }}"
                                                        data-reference="{{ $notice->reference_number }}"
                                                        data-reminder-count="{{ $notice->reminder_count ?? 0 }}"
                                                        title="Send Reminder">
                                                    <i class="mdi mdi-bell-ring-outline"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-5">
                                            <div class="bn-empty-icon mb-3">
                                                <i class="mdi mdi-bulletin-board"></i>
                                            </div>
                                            <h6 class="text-muted mb-1">No Board Notices Yet</h6>
                                            <p class="text-muted mb-3" style="font-size: 13px;">
                                                Select a meeting from the table above to create your first official board notice.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    /* ── Header Icon ─────────────────────────────────────────── */
    .bn-header-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #b8000f 100%);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .bn-header-icon i { font-size: 24px; color: #fff; }

    /* ── Glassmorphism Stat Cards ────────────────────────────── */
    .bn-stat-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.5);
        border-radius: 14px; padding: 20px;
        display: flex; align-items: center; gap: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .bn-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.04);
    }
    .bn-stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .bn-stat-icon i { font-size: 24px; }
    .bn-stat-card h3 { font-size: 1.5rem; line-height: 1; }
    .bn-stat-card small { font-size: 11.5px; letter-spacing: 0.02em; }

    /* ── Table Card ──────────────────────────────────────────── */
    .bn-table-card {
        border: none; border-radius: 14px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .bn-table-card .card-header { border-bottom: 1px solid #f0f2f5; }
    .bn-table-head th {
        background: #fafafa; border-bottom: 2px solid #e9ecef;
        font-size: 11.5px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.04em; color: #6c757d; padding: 12px 16px; white-space: nowrap;
    }
    .bn-table-row td {
        padding: 14px 16px; border-bottom: 1px solid #f5f6f8; vertical-align: middle;
    }
    .bn-table-row:last-child td { border-bottom: none; }
    .bn-table-row:hover { background: #fbfbfb; }

    /* ── Meeting Cell ────────────────────────────────────────── */
    .bn-meeting-icon {
        width: 34px; height: 34px; border-radius: 8px;
        background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #b8000f 100%);
        display: flex; align-items: center; justify-content: center; margin-top: 2px;
    }
    .bn-meeting-icon i { font-size: 16px; color: #fff; }
    .bn-meeting-link:hover { color: {{ setting('primary_color', '#1a1a1a') }} !important; }

    /* ── Notice Type Badges ──────────────────────────────────── */
    .bn-type-badge { font-size: 11px; font-weight: 600; padding: 5px 10px; border-radius: 6px; letter-spacing: 0.02em; }
    .bn-type-21 { background: rgba(200, 16, 46,0.1); color: {{ setting('primary_color', '#1a1a1a') }}; }
    .bn-type-7 { background: rgba(9,145,57,0.1); color: #099139; }

    /* ── Status Badge ────────────────────────────────────────── */
    .bn-status-badge { font-size: 11.5px; font-weight: 600; padding: 5px 10px; border-radius: 6px; }

    /* ── Action Buttons ──────────────────────────────────────── */
    .bn-action-btn {
        width: 32px; height: 32px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; font-size: 16px; transition: all 0.15s ease;
    }
    .bn-action-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn-soft-primary { background: rgba(200, 16, 46,0.1); color: {{ setting('primary_color', '#1a1a1a') }}; border: none; }
    .btn-soft-primary:hover { background: rgba(200, 16, 46,0.2); color: {{ setting('primary_color', '#1a1a1a') }}; }
    .btn-soft-info { background: rgba(15,238,93,0.1); color: #0fee5d; border: none; }
    .btn-soft-info:hover { background: rgba(15,238,93,0.2); color: #0ed353; }

    /* ── Empty State ─────────────────────────────────────────── */
    .bn-empty-icon {
        width: 72px; height: 72px; border-radius: 50%; background: #f8f8f8;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .bn-empty-icon i { font-size: 36px; color: #c4cdd5; }

    /* ── Filter dropdown active state ────────────────────────── */
    .bn-filter-item.active { background: rgba(200, 16, 46,0.08); color: {{ setting('primary_color', '#1a1a1a') }}; font-weight: 600; }

    /* ── Soft background utilities ───────────────────────────── */
    .bg-soft-primary   { background: rgba(200, 16, 46,0.1) !important; }
    .bg-soft-success   { background: rgba(9,145,57,0.1) !important; }
    .bg-soft-warning   { background: rgba(255,193,7,0.12) !important; }
    .bg-soft-danger    { background: rgba(220,53,69,0.1) !important; }
    .bg-soft-info      { background: rgba(15,238,93,0.1) !important; }
    .bg-soft-secondary { background: rgba(108,117,125,0.1) !important; }

    /* ── DataTable overrides ─────────────────────────────────── */
    .bn-table-card .dataTables_wrapper .dataTables_filter,
    .bn-table-card .dataTables_wrapper .dataTables_length { padding: 12px 16px; }
    .bn-table-card .dataTables_wrapper .dataTables_info,
    .bn-table-card .dataTables_wrapper .dataTables_paginate { padding: 12px 16px; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {

    // ── Status Filter ───────────────────────────────────────
    $(document).on('click', '.bn-filter-item', function(e) {
        e.preventDefault();
        var status = $(this).data('status');
        var label  = $(this).text().trim();

        $('.bn-filter-item').removeClass('active');
        $(this).addClass('active');
        $('#filterLabel').text(label);

        if (status === 'all') {
            $('.bn-table-row').show();
        } else {
            $('.bn-table-row').each(function() {
                $(this).toggle($(this).data('status') === status);
            });
        }
    });

    // ── Send Reminder ───────────────────────────────────────
    $(document).on('click', '.btn-send-reminder', function() {
        var noticeId      = $(this).data('notice-id');
        var reference     = $(this).data('reference');
        var reminderCount = $(this).data('reminder-count');

        var reminderText = reminderCount > 0
            ? '<p class="text-muted mb-0" style="font-size:13px;"><i class="mdi mdi-information-outline me-1"></i>' + reminderCount + ' reminder(s) already sent.</p>'
            : '';

        Swal.fire({
            title: 'Send Reminder?',
            html: '<p>Send a follow-up reminder for notice <strong>' + reference + '</strong> to all board members?</p>' + reminderText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
            confirmButtonText: '<i class="mdi mdi-bell-ring-outline me-1"></i> Send Reminder',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/workflow/board-notices/' + noticeId + '/reminder';
                form.innerHTML = '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

});
</script>
@endpush
