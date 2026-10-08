@extends('admin_dashboard')

@section('admin')
@php
    $iconMap = [
        'meeting_initiated'     => ['mdi-rocket-launch-outline', 'red'],
        'internal_notice'       => ['mdi-file-send-outline', 'dark'],
        'notice_submitted'      => ['mdi-file-clock-outline', 'amber'],
        'notice_approved'       => ['mdi-check-circle-outline', 'green'],
        'notice_rejected'       => ['mdi-close-circle-outline', 'red'],
        'assignment_created'    => ['mdi-account-plus-outline', 'dark'],
        'paper_submitted'       => ['mdi-file-document-outline', 'dark'],
        'paper_approved'        => ['mdi-file-check-outline', 'green'],
        'paper_rejected'        => ['mdi-file-remove-outline', 'red'],
        'paper_removed'         => ['mdi-file-hidden', 'dark'],
        'meeting_finalized'     => ['mdi-check-decagram', 'green'],
        'board_notice_sent'     => ['mdi-email-check-outline', 'green'],
        'agenda_item_pending'   => ['mdi-format-list-checks', 'amber'],
        'agenda_item_approved'  => ['mdi-format-list-checks', 'green'],
        'agenda_item_rejected'  => ['mdi-format-list-checks', 'red'],
        'pulldown_pending'      => ['mdi-eye-off-outline', 'amber'],
        'pulldown_approved'     => ['mdi-eye-off-outline', 'green'],
        'pulldown_rejected'     => ['mdi-eye-off-outline', 'red'],
    ];
    $hasFilters = $filterType || $filterMeeting || $filterRead;
    $tabs = [
        ''       => ['All', $totalCount],
        'unread' => ['Unread', $unreadCount],
        'read'   => ['Read', $totalCount - $unreadCount],
    ];
@endphp

<div class="content nt-page">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('workflow.dashboard') }}" class="nt-back">
                <i class="mdi mdi-arrow-left"></i> Dashboard
            </a>
            <h4 class="mb-1">{{ $isAdmin ? 'All System Notifications' : 'My Notifications' }}</h4>
            <p class="text-muted mb-0">
                {{ $isAdmin ? 'Notifications and activity across all meetings and users.' : 'Notifications and activity within your scope.' }}
            </p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('workflow.notifications.markread') }}" method="POST" id="markAllForm">
                @csrf
                <button type="button" class="btn btn-sm btn-success" onclick="confirmMarkAll()">
                    <i class="mdi mdi-check-all"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card nt-card">

                {{-- Toolbar: status tabs + filters --}}
                <div class="nt-toolbar">
                    <div class="nt-tabs">
                        @foreach($tabs as $value => [$label, $count])
                            <a href="{{ request()->fullUrlWithQuery(['read_status' => $value ?: null, 'page' => null]) }}"
                               class="nt-tab {{ (string) $filterRead === (string) $value ? 'active' : '' }}">
                                {{ $label }} <span>{{ $count }}</span>
                            </a>
                        @endforeach
                    </div>

                    <form method="GET" action="{{ route('workflow.notifications') }}" class="nt-filters">
                        @if($filterRead)
                            <input type="hidden" name="read_status" value="{{ $filterRead }}">
                        @endif
                        <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" aria-label="Type">
                            <option value="">All types</option>
                            @foreach($notificationTypes as $type)
                                <option value="{{ $type }}" {{ $filterType === $type ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($type)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="meeting_id" class="form-select form-select-sm" onchange="this.form.submit()" aria-label="Meeting">
                            <option value="">All meetings</option>
                            @foreach($meetings as $meeting)
                                <option value="{{ $meeting->id }}" {{ (string)$filterMeeting === (string)$meeting->id ? 'selected' : '' }}>
                                    {{ Str::limit($meeting->title, 35) }}
                                    ({{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') : '' }})
                                </option>
                            @endforeach
                        </select>
                        @if($hasFilters)
                            <a href="{{ route('workflow.notifications') }}" class="nt-clear" title="Clear filters">
                                <i class="mdi mdi-close"></i> Clear
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Notification List --}}
                @if($notifications->count() > 0)
                    <ul class="nt-list">
                        @foreach($notifications as $notification)
                            @php
                                $icon = $iconMap[$notification->type] ?? ['mdi-bell-outline', 'dark'];
                                $unread = is_null($notification->read_at);
                            @endphp
                            <li class="nt-item {{ $unread ? 'is-unread' : '' }}">
                                <div class="nt-icon nt-{{ $icon[1] }}">
                                    <i class="mdi {{ $icon[0] }}"></i>
                                </div>

                                <div class="nt-body">
                                    <div class="nt-title">{{ $notification->title }}</div>
                                    <div class="nt-msg">{{ Str::limit($notification->message, 160) }}</div>
                                    <div class="nt-meta">
                                        <span title="{{ $notification->created_at->format('d M Y, H:i') }}">{{ $notification->created_at->diffForHumans() }}</span>
                                        @if($notification->meeting)
                                            <span><i class="mdi mdi-calendar-blank-outline"></i>{{ Str::limit($notification->meeting->title, 30) }}</span>
                                        @endif
                                        @if($isAdmin && $notification->user)
                                            <span><i class="mdi mdi-account-outline"></i>{{ $notification->user->name }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="nt-actions">
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="nt-btn" title="Open">
                                            <i class="mdi mdi-arrow-top-right"></i>
                                        </a>
                                    @endif
                                    @if($unread)
                                        <form action="{{ route('workflow.notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="nt-btn" title="Mark as read">
                                                <i class="mdi mdi-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="nt-empty">
                        <div class="nt-empty-icon"><i class="mdi mdi-bell-check-outline"></i></div>
                        <h6>{{ $hasFilters ? 'No matching notifications' : 'You are all caught up' }}</h6>
                        <p>
                            @if($hasFilters)
                                Try different filters or <a href="{{ route('workflow.notifications') }}">clear them</a>.
                            @else
                                New notifications will appear here.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

        {{-- Activity Timeline --}}
        <div class="col-xl-4">
            <div class="card nt-card">
                <div class="nt-side-head">
                    <span>{{ $isAdmin ? 'System activity' : 'Your activity' }}</span>
                    <small>{{ $isAdmin ? 'Full access' : 'Your scope' }}</small>
                </div>
                <div class="nt-timeline">
                    @forelse($recentActivity as $log)
                        @php
                            $dot = match(true) {
                                str_contains($log->action, 'approved') || str_contains($log->action, 'finalized') => 'green',
                                str_contains($log->action, 'rejected') => 'red',
                                str_contains($log->action, 'submitted') || str_contains($log->action, 'pending') => 'amber',
                                default => 'dark',
                            };
                        @endphp
                        <div class="nt-tl-item">
                            <span class="nt-dot nt-{{ $dot }}"></span>
                            <div class="nt-tl-text">{{ Str::limit($log->description, 100) }}</div>
                            <div class="nt-tl-meta">
                                {{ $log->created_at->diffForHumans() }}@if($log->user) &middot; {{ $log->user->name }}@endif
                            </div>
                        </div>
                    @empty
                        <div class="nt-empty py-4">
                            <p class="mb-0">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nt-page { --nt-red: #c8102e; --nt-green: #099139; --nt-ink: #111827; --nt-muted: #6b7280; --nt-line: #eef0f2; }
    .nt-back { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 600; color: var(--nt-muted); text-decoration: none; margin-bottom: 6px; }
    .nt-back:hover { color: var(--nt-ink); }

    .nt-card { border-radius: 14px !important; overflow: hidden; }

    /* Toolbar */
    .nt-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--nt-line); background: #fff; }
    .nt-tabs { display: inline-flex; padding: 4px; background: #f3f4f6; border-radius: 10px; gap: 2px; }
    .nt-tab { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: var(--nt-muted); text-decoration: none; transition: background .15s, color .15s; }
    .nt-tab span { font-size: 11.5px; font-weight: 700; padding: 1px 7px; border-radius: 999px; background: #e5e7eb; color: #374151; }
    .nt-tab:hover { color: var(--nt-ink); }
    .nt-tab.active { background: #fff; color: var(--nt-ink); box-shadow: 0 1px 3px rgba(17, 17, 17, .08); }
    .nt-tab.active span { background: var(--nt-red); color: #fff; }
    .nt-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
    .nt-filters .form-select { width: auto; min-width: 150px; max-width: 240px; border-radius: 9px; font-size: 13.5px; }
    .nt-clear { font-size: 13px; font-weight: 600; color: var(--nt-red); text-decoration: none; }

    /* List */
    .nt-list { list-style: none; margin: 0; padding: 0; }
    .nt-item { position: relative; display: flex; align-items: flex-start; gap: 14px; padding: 16px 18px; border-bottom: 1px solid var(--nt-line); background: #fff; transition: background .15s; }
    .nt-item:last-child { border-bottom: 0; }
    .nt-item:hover { background: #fafafa; }
    .nt-item.is-unread::before { content: ""; position: absolute; left: 0; top: 14px; bottom: 14px; width: 3px; border-radius: 0 3px 3px 0; background: var(--nt-red); }

    .nt-icon { flex-shrink: 0; width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 19px; }
    .nt-red   { background: #fdecee; color: var(--nt-red); }
    .nt-green { background: #e6f6ec; color: var(--nt-green); }
    .nt-amber { background: #fef6e4; color: #b45309; }
    .nt-dark  { background: #f1f1f1; color: #111; }

    .nt-body { flex: 1; min-width: 0; }
    .nt-title { font-size: 14.5px; font-weight: 600; color: var(--nt-ink); line-height: 1.4; }
    .nt-item.is-unread .nt-title { font-weight: 700; }
    .nt-msg { font-size: 13.5px; color: #4b5563; line-height: 1.55; margin-top: 2px; }
    .nt-meta { display: flex; flex-wrap: wrap; gap: 4px 14px; margin-top: 6px; font-size: 12.5px; color: var(--nt-muted); }
    .nt-meta i { margin-right: 3px; }

    .nt-actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; opacity: .55; transition: opacity .15s; }
    .nt-item:hover .nt-actions, .nt-item.is-unread .nt-actions { opacity: 1; }
    .nt-actions form { margin: 0; }
    .nt-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--nt-line); background: #fff; color: #374151; font-size: 16px; text-decoration: none; transition: all .15s; }
    .nt-btn:hover { border-color: var(--nt-green); color: var(--nt-green); background: #e6f6ec; }

    /* Empty state */
    .nt-empty { text-align: center; padding: 56px 20px; color: var(--nt-muted); }
    .nt-empty-icon { width: 64px; height: 64px; margin: 0 auto 14px; border-radius: 50%; background: #e6f6ec; color: var(--nt-green); display: flex; align-items: center; justify-content: center; font-size: 30px; }
    .nt-empty h6 { color: var(--nt-ink); margin-bottom: 4px; }
    .nt-empty p { font-size: 13.5px; margin: 0; }

    /* Activity timeline */
    .nt-side-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--nt-line); }
    .nt-side-head span { font-weight: 700; font-size: 15px; color: var(--nt-ink); }
    .nt-side-head small { font-size: 12px; color: var(--nt-muted); }
    .nt-timeline { max-height: 640px; overflow-y: auto; padding: 8px 18px 12px; }
    .nt-tl-item { position: relative; padding: 10px 0 10px 22px; }
    .nt-tl-item::before { content: ""; position: absolute; left: 4px; top: 0; bottom: 0; width: 2px; background: var(--nt-line); }
    .nt-tl-item:first-child::before { top: 16px; }
    .nt-tl-item:last-child::before { bottom: calc(100% - 16px); }
    .nt-dot { position: absolute; left: 0; top: 13px; width: 10px; height: 10px; border-radius: 50%; box-shadow: 0 0 0 3px #fff; }
    .nt-dot.nt-red { background: var(--nt-red); }
    .nt-dot.nt-green { background: var(--nt-green); }
    .nt-dot.nt-amber { background: #f59e0b; }
    .nt-dot.nt-dark { background: #9ca3af; }
    .nt-tl-text { font-size: 13px; color: #374151; line-height: 1.5; }
    .nt-tl-meta { font-size: 12px; color: var(--nt-muted); margin-top: 2px; }

    @media (max-width: 575.98px) {
        .nt-toolbar { padding: 12px; }
        .nt-filters, .nt-filters .form-select { width: 100%; max-width: none; }
        .nt-item { padding: 14px 12px; gap: 10px; }
    }
</style>
@endpush

@push('scripts')
<script>
function confirmMarkAll() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark {{ $unreadCount }} notification(s) as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
        confirmButtonText: 'Yes, mark all'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('markAllForm').submit();
        }
    });
}
</script>
@endpush
