@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        $committeeNames = $notices->pluck('meeting.boardCommittee.name')->filter()->unique()->values();
    @endphp

    <style>
        :root {
            --bp-navy: {{ setting('primary_color', '#1a1a1a') }};
            --bp-navy-dark: #9b0c23;
            --bp-navy-light: #c52330;
            --bp-navy-soft: #fdecee;
            --bp-green: #099139;
            --bp-green-dark: #077a2e;
            --bp-green-soft: #e6f5ec;
            --bp-amber: #f59e0b;
            --bp-amber-soft: #fff8e1;
            --bp-blue-soft: #f0f0f0;
            --bp-radius: 16px;
            --bp-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            --bp-shadow-hover: 0 12px 32px rgba(200, 16, 46, .12);
        }

        /* ── Hero Banner ─────────────────────────────── */
        .bp-hero {
            position: relative;
            background: linear-gradient(135deg, var(--bp-navy) 0%, var(--bp-navy-light) 60%, #d30f1f 100%);
            border-radius: var(--bp-radius);
            overflow: hidden;
            color: #fff;
            padding: 2.5rem 2rem;
        }
        .bp-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: .08;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .bp-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .bp-hero-content {
            position: relative;
            z-index: 2;
        }
        .bp-hero-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -.3px;
            margin-bottom: .25rem;
        }
        .bp-hero-subtitle {
            opacity: .85;
            font-size: .95rem;
        }
        .bp-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: .5rem 1.25rem;
            font-size: .9rem;
            font-weight: 600;
            color: #fff;
        }
        .bp-stat-chip i {
            font-size: 1.2rem;
        }

        /* ── Search & Filter Bar ─────────────────────── */
        .bp-search-wrap {
            position: relative;
            max-width: 480px;
        }
        .bp-search-wrap .bp-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            pointer-events: none;
        }
        .bp-search-input {
            width: 100%;
            border: 2px solid #e9e9e9;
            border-radius: 999px;
            padding: .7rem 1rem .7rem 2.75rem;
            font-size: .95rem;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
            min-height: 48px;
        }
        .bp-search-input:focus {
            outline: none;
            border-color: var(--bp-navy);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, .12);
        }
        .bp-search-input::placeholder {
            color: #94a3b8;
        }
        .bp-filter-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
        }
        .bp-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem .9rem;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
            border: 1.5px solid #e9e9e9;
            background: #fff;
            color: #64748b;
            cursor: pointer;
            transition: all .2s ease;
            min-height: 44px;
            user-select: none;
        }
        .bp-filter-chip:hover {
            border-color: var(--bp-navy);
            color: var(--bp-navy);
            background: var(--bp-navy-soft);
        }
        .bp-filter-chip.active {
            border-color: var(--bp-navy);
            background: var(--bp-navy);
            color: #fff;
        }
        .bp-filter-chip i {
            font-size: 1rem;
        }

        /* ── Notice Cards ────────────────────────────── */
        .bp-notice-card {
            position: relative;
            background: #fff;
            border: none;
            border-radius: var(--bp-radius);
            box-shadow: var(--bp-shadow);
            transition: transform .25s ease, box-shadow .25s ease;
            overflow: hidden;
        }
        .bp-notice-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--bp-shadow-hover);
        }
        .bp-notice-card .bp-accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
        }
        .bp-accent-navy { background: var(--bp-navy); }
        .bp-accent-amber { background: var(--bp-amber); }
        .bp-accent-blue { background: #f31628; }

        .bp-card-body {
            padding: 1.25rem 1.25rem 1.25rem 1.75rem;
        }

        /* Badge row */
        .bp-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-bottom: .65rem;
            align-items: center;
        }
        .bp-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-weight: 600;
            border-radius: 999px;
            font-size: .78rem;
            line-height: 1;
        }
        .bp-badge-committee {
            background: var(--bp-navy-soft);
            color: var(--bp-navy);
            padding: .35rem .75rem;
        }
        .bp-badge-version {
            background: var(--bp-green-soft);
            color: var(--bp-green);
            padding: .3rem .6rem;
            font-size: .72rem;
        }
        .bp-badge-rescheduled {
            background: #fff3e0;
            color: #e65100;
            padding: .3rem .65rem;
        }
        .bp-badge-superseded {
            background: var(--bp-blue-soft);
            color: #2b2b2b;
            padding: .3rem .65rem;
        }

        /* Subject */
        .bp-subject {
            font-size: 1.1rem;
            font-weight: 700;
            color: #121212;
            margin-bottom: .5rem;
            line-height: 1.4;
        }

        /* Meta row */
        .bp-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem .9rem;
            font-size: .84rem;
            color: #64748b;
        }
        .bp-meta span {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            white-space: nowrap;
        }
        .bp-meta .bp-ref {
            font-family: 'Courier New', Courier, monospace;
            color: #94a3b8;
            font-size: .82rem;
        }

        /* Rescheduled link */
        .bp-replaces-link {
            font-size: .84rem;
            margin-top: .5rem;
            color: #64748b;
        }
        .bp-replaces-link a {
            color: var(--bp-navy);
            font-weight: 600;
            text-decoration: none;
        }
        .bp-replaces-link a:hover {
            text-decoration: underline;
        }

        /* Superseded banner */
        .bp-superseded-banner {
            background: var(--bp-amber-soft);
            border-left: 4px solid var(--bp-amber);
            padding: .6rem 1rem;
            border-radius: 0 10px 10px 0;
            margin-top: .75rem;
            font-size: .84rem;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }
        .bp-superseded-banner a {
            color: var(--bp-navy);
            font-weight: 700;
            text-decoration: none;
        }
        .bp-superseded-banner a:hover {
            text-decoration: underline;
        }

        /* Status footer */
        .bp-card-footer {
            background: #fafafa;
            border-top: 1px solid #f5f5f5;
            padding: .6rem 1.25rem .6rem 1.75rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .bp-status-badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .bp-status-sent { background: var(--bp-green-soft); color: var(--bp-green); }
        .bp-status-approved { background: var(--bp-blue-soft); color: #2b2b2b; }
        .bp-status-draft { background: #f5f5f5; color: #64748b; }
        .bp-status-cancelled { background: #fee2e2; color: #dc2626; }
        .bp-status-default { background: var(--bp-blue-soft); color: #2b2b2b; }

        .bp-meeting-title {
            font-size: .82rem;
            color: #94a3b8;
        }

        /* Action buttons */
        .bp-actions {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            flex-shrink: 0;
            align-items: stretch;
        }
        .bp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .5rem 1.1rem;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            transition: all .2s ease;
            white-space: nowrap;
            min-height: 44px;
        }
        .bp-btn-navy {
            background: var(--bp-navy);
            color: #fff;
        }
        .bp-btn-navy:hover {
            background: var(--bp-navy-dark);
            color: #fff;
        }
        .bp-btn-green {
            background: var(--bp-green);
            color: #fff;
        }
        .bp-btn-green:hover {
            background: var(--bp-green-dark);
            color: #fff;
        }

        /* ── Pagination ──────────────────────────────── */
        .bp-pagination .page-link {
            color: var(--bp-navy);
            border-radius: 10px;
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 1.5px solid #e9e9e9;
            margin: 0 .15rem;
            transition: all .2s ease;
        }
        .bp-pagination .page-link:hover {
            background: var(--bp-navy-soft);
            border-color: var(--bp-navy);
        }
        .bp-pagination .page-item.active .page-link {
            background: var(--bp-navy);
            border-color: var(--bp-navy);
            color: #fff;
        }
        .bp-pagination .page-item.disabled .page-link {
            color: #dd3c49;
            border-color: #e9e9e9;
        }

        /* ── Empty State ─────────────────────────────── */
        .bp-empty {
            text-align: center;
            padding: 5rem 2rem;
        }
        .bp-empty-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #f5f5f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .bp-empty-icon i {
            font-size: 40px;
            color: #dd3c49;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 767.98px) {
            .bp-hero {
                padding: 1.75rem 1.25rem;
            }
            .bp-hero-title {
                font-size: 1.35rem;
            }
            .bp-card-body {
                padding: 1rem 1rem 1rem 1.5rem;
            }
            .bp-actions {
                flex-direction: row;
            }
            .bp-actions .bp-btn {
                flex: 1;
            }
            .bp-meta {
                gap: .25rem .65rem;
            }
            .bp-card-footer {
                padding: .5rem 1rem .5rem 1.5rem;
            }
        }
        @media (min-width: 768px) {
            .bp-card-inner {
                display: flex;
                gap: 1.25rem;
            }
            .bp-card-main {
                flex: 1;
                min-width: 0;
            }
        }

        /* ── Utility ─────────────────────────────────── */
        .bp-back-link {
            color: var(--bp-navy);
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            min-height: 44px;
            transition: color .2s ease;
        }
        .bp-back-link:hover {
            color: var(--bp-navy-dark);
        }
        .bp-no-results {
            display: none;
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }
        .bp-no-results i {
            font-size: 48px;
            display: block;
            margin-bottom: .75rem;
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Back link --}}
            <div class="mb-3">
                <a href="{{ route('board.portal.dashboard') }}" class="bp-back-link">
                    <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            {{-- Hero Banner --}}
            <div class="bp-hero mb-4">
                <div class="bp-hero-content d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h3 class="bp-hero-title">
                            <i class="mdi mdi-bulletin-board me-2"></i>Official Board Notices
                        </h3>
                        <div class="bp-hero-subtitle">
                            Notices &amp; communications for <strong>{{ $member->names }}</strong>
                            &mdash; {{ $member->designation ?? 'Board Member' }}
                        </div>
                    </div>
                    <div>
                        <span class="bp-stat-chip">
                            <i class="mdi mdi-file-document-multiple-outline"></i>
                            {{ $notices->total() }} {{ Str::plural('Notice', $notices->total()) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Search & Filter Bar --}}
            @if($notices->count())
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 mb-4">
                    <div class="bp-search-wrap flex-grow-1">
                        <i class="mdi mdi-magnify bp-search-icon"></i>
                        <input type="text"
                               id="bpNoticeSearch"
                               class="bp-search-input"
                               placeholder="Search by subject, reference, or committee...">
                    </div>

                    @if($committeeNames->count() > 1)
                        <div class="bp-filter-chips">
                            <span class="text-muted" style="font-size:.82rem;font-weight:600;">Filter:</span>
                            @foreach($committeeNames as $cName)
                                <button type="button"
                                        class="bp-filter-chip"
                                        data-committee="{{ Str::slug($cName) }}">
                                    <i class="mdi mdi-account-group"></i> {{ $cName }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- No Results Message (JS driven) --}}
            <div class="bp-no-results" id="bpNoResults">
                <i class="mdi mdi-text-search-variant"></i>
                <div class="fw-semibold mb-1">No notices match your search</div>
                <div>Try adjusting your search term or clearing filters.</div>
            </div>

            {{-- Notices List --}}
            @if($notices->count())
                <div class="d-flex flex-column gap-3" id="bpNoticesList">
                    @foreach($notices as $notice)
                        @php
                            $noticeDate  = $notice->notice_date ? Carbon::parse($notice->notice_date) : null;
                            $sentDate    = $notice->sent_at ? Carbon::parse($notice->sent_at) : null;
                            $meetingDate = optional($notice->meeting)->meeting_date
                                ? Carbon::parse($notice->meeting->meeting_date) : null;
                            $committeeName = optional(optional($notice->meeting)->boardCommittee)->name;
                            $isRescheduled  = !empty($notice->parent_notice_id);
                            $hasReplacements = $notice->replacementNotices && $notice->replacementNotices->count() > 0;

                            // Determine accent color
                            $accentClass = 'bp-accent-navy';
                            if ($isRescheduled) $accentClass = 'bp-accent-amber';
                            if ($hasReplacements) $accentClass = 'bp-accent-blue';
                        @endphp

                        <div class="bp-notice-card bp-notice-item"
                             data-subject="{{ Str::lower($notice->subject) }}"
                             data-ref="{{ Str::lower($notice->reference_number ?? '') }}"
                             data-committee="{{ $committeeName ? Str::slug($committeeName) : '' }}">

                            <div class="bp-accent {{ $accentClass }}"></div>

                            <div class="bp-card-body">
                                <div class="bp-card-inner">
                                    {{-- Main content --}}
                                    <div class="bp-card-main">
                                        {{-- Badges --}}
                                        <div class="bp-badges">
                                            @if($committeeName)
                                                <span class="bp-badge bp-badge-committee">
                                                    <i class="mdi mdi-account-group"></i> {{ $committeeName }}
                                                </span>
                                            @endif
                                            @if($notice->version && $notice->version > 1)
                                                <span class="bp-badge bp-badge-version">
                                                    <i class="mdi mdi-tag-outline"></i> v{{ $notice->version }}
                                                </span>
                                            @endif
                                            @if($isRescheduled)
                                                <span class="bp-badge bp-badge-rescheduled">
                                                    <i class="mdi mdi-calendar-refresh"></i> Rescheduled
                                                </span>
                                            @endif
                                            @if($hasReplacements)
                                                <span class="bp-badge bp-badge-superseded">
                                                    <i class="mdi mdi-swap-horizontal"></i> Superseded
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Subject --}}
                                        <div class="bp-subject">{{ $notice->subject }}</div>

                                        {{-- Meta row --}}
                                        <div class="bp-meta">
                                            @if($notice->reference_number)
                                                <span class="bp-ref">
                                                    <i class="mdi mdi-pound"></i>{{ $notice->reference_number }}
                                                </span>
                                            @endif
                                            @if($noticeDate)
                                                <span>
                                                    <i class="mdi mdi-calendar"></i>
                                                    {{ $noticeDate->format('jS F Y') }}
                                                </span>
                                            @endif
                                            @if($sentDate)
                                                <span>
                                                    <i class="mdi mdi-send-check"></i>
                                                    Sent {{ $sentDate->format('jS M Y, g:i A') }}
                                                </span>
                                            @elseif($notice->approved_at)
                                                <span>
                                                    <i class="mdi mdi-check-decagram"></i>
                                                    Approved {{ Carbon::parse($notice->approved_at)->format('jS M Y, g:i A') }}
                                                </span>
                                            @endif
                                            @if($meetingDate)
                                                <span>
                                                    <i class="mdi mdi-calendar-star"></i>
                                                    Meeting: {{ $meetingDate->format('jS F Y') }}
                                                </span>
                                            @endif
                                            @if(optional($notice->creator)->name)
                                                <span>
                                                    <i class="mdi mdi-account-edit"></i>
                                                    {{ $notice->creator->name }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Rescheduled: link to original --}}
                                        @if($isRescheduled && $notice->parentNotice)
                                            <div class="bp-replaces-link">
                                                <i class="mdi mdi-link-variant" style="color:var(--bp-amber)"></i>
                                                Replaces:
                                                <a href="{{ route('board.portal.notice.show', $notice->parentNotice->id) }}">
                                                    {{ $notice->parentNotice->subject }}
                                                    @if($notice->parentNotice->reference_number)
                                                        ({{ $notice->parentNotice->reference_number }})
                                                    @endif
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Superseded banner --}}
                                        @if($hasReplacements)
                                            <div class="bp-superseded-banner">
                                                <i class="mdi mdi-alert-circle-outline"></i>
                                                <span>This notice has been superseded by a newer version.</span>
                                                @php $replacement = $notice->replacementNotices->first(); @endphp
                                                @if($replacement)
                                                    <a href="{{ route('board.portal.notice.show', $replacement->id) }}">
                                                        View latest notice <i class="mdi mdi-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action buttons --}}
                                    <div class="bp-actions">
                                        <a href="{{ route('board.portal.notice.show', $notice->id) }}"
                                           class="bp-btn bp-btn-navy">
                                            <i class="mdi mdi-eye-outline"></i> View
                                        </a>
                                        <a href="{{ route('board.portal.notice.download', $notice->id) }}"
                                           class="bp-btn bp-btn-green">
                                            <i class="mdi mdi-file-pdf-box"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Status footer --}}
                            @if($notice->status)
                                <div class="bp-card-footer">
                                    @switch($notice->status)
                                        @case('sent')
                                            <span class="bp-status-badge bp-status-sent">
                                                <i class="mdi mdi-check-circle"></i> Sent
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="bp-status-badge bp-status-approved">
                                                <i class="mdi mdi-check-decagram"></i> Approved
                                            </span>
                                            @break
                                        @case('draft')
                                            <span class="bp-status-badge bp-status-draft">
                                                <i class="mdi mdi-pencil"></i> Draft
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="bp-status-badge bp-status-cancelled">
                                                <i class="mdi mdi-close-circle"></i> Cancelled
                                            </span>
                                            @break
                                        @default
                                            <span class="bp-status-badge bp-status-default">
                                                <i class="mdi mdi-information"></i> {{ ucfirst($notice->status) }}
                                            </span>
                                    @endswitch

                                    @if(optional($notice->meeting)->title)
                                        <span class="bp-meeting-title ms-auto d-none d-md-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-calendar-text"></i> {{ $notice->meeting->title }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($notices->hasPages())
                    <div class="d-flex justify-content-center mt-4 bp-pagination">
                        {{ $notices->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="bp-notice-card">
                    <div class="bp-empty">
                        <div class="bp-empty-icon">
                            <i class="mdi mdi-bell-off-outline"></i>
                        </div>
                        <h5 class="fw-bold text-muted mb-2">No Notices Available</h5>
                        <p class="text-muted mb-4" style="max-width:440px;margin:0 auto;">
                            There are currently no official board notices to display.
                            New notices will appear here when published.
                        </p>
                        <a href="{{ route('board.portal.dashboard') }}" class="bp-btn bp-btn-navy">
                            <i class="mdi mdi-arrow-left"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Client-side search & filter --}}
    <script>
        $(function () {
            var $search   = $('#bpNoticeSearch');
            var $items    = $('.bp-notice-item');
            var $chips    = $('.bp-filter-chip');
            var $noResult = $('#bpNoResults');
            var activeCommittee = null;

            function applyFilters() {
                var query = ($search.val() || '').toLowerCase().trim();
                var visible = 0;

                $items.each(function () {
                    var $el = $(this);
                    var subject   = $el.data('subject') || '';
                    var ref       = $el.data('ref') || '';
                    var committee = $el.data('committee') || '';

                    var matchesSearch = !query
                        || subject.indexOf(query) !== -1
                        || ref.indexOf(query) !== -1
                        || committee.indexOf(query) !== -1;

                    var matchesFilter = !activeCommittee
                        || committee === activeCommittee;

                    if (matchesSearch && matchesFilter) {
                        $el.show();
                        visible++;
                    } else {
                        $el.hide();
                    }
                });

                $noResult.toggle(visible === 0 && $items.length > 0);
            }

            $search.on('input', function () {
                applyFilters();
            });

            $chips.on('click', function () {
                var $chip = $(this);
                var committee = $chip.data('committee');

                if (activeCommittee === committee) {
                    activeCommittee = null;
                    $chip.removeClass('active');
                } else {
                    activeCommittee = committee;
                    $chips.removeClass('active');
                    $chip.addClass('active');
                }

                applyFilters();
            });
        });
    </script>
@endsection
