@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        $sentDate       = $notice->sent_at ? Carbon::parse($notice->sent_at) : null;
        $noticeDate     = $notice->notice_date ? Carbon::parse($notice->notice_date) : null;
        $referenceDate  = $notice->reference_date ? Carbon::parse($notice->reference_date) : null;
        $committeeName  = optional(optional($notice->meeting)->boardCommittee)->name;
        $isRescheduled  = !empty($notice->parent_notice_id);
        $hasReplacements = $notice->replacementNotices && $notice->replacementNotices->count() > 0;
        $noticeTypeLabel = $notice->notice_type === '21_day' ? '21 Day' : ($notice->notice_type === '7_day' ? '7 Day' : ucfirst(str_replace('_', ' ', $notice->notice_type ?? '')));
    @endphp

    @push('styles')
    <style>
        :root {
            --twwda-navy: {{ setting('primary_color', '#1a1a1a') }};
            --twwda-navy-soft: #fdecee;
            --twwda-green: #099139;
            --twwda-green-soft: #e6f5ec;
        }

        /* Brand gradient header */
        .twwda-notice-show-gradient {
            background: linear-gradient(135deg, var(--twwda-navy) 0%, #3a3a3a 100%);
        }

        /* Card styling */
        .twwda-notice-show-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        /* Back button pill */
        .twwda-btn-back-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--twwda-navy-soft);
            color: var(--twwda-navy);
            border: 1px solid transparent;
            border-radius: 999px;
            padding: .5rem 1.25rem;
            font-weight: 600;
            font-size: .92rem;
            text-decoration: none;
            min-height: 44px;
            transition: all .2s ease;
        }
        .twwda-btn-back-pill:hover {
            background: var(--twwda-navy);
            color: #fff;
            text-decoration: none;
        }

        /* Detail rows */
        .twwda-detail-list .twwda-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .55rem 0;
            border-bottom: 1px dashed #f4f4f4;
            font-size: .93rem;
        }
        .twwda-detail-list .twwda-detail-row:last-child {
            border-bottom: 0;
        }
        .twwda-detail-label {
            color: #6c757d;
            font-size: .88rem;
            flex-shrink: 0;
            margin-right: 1rem;
        }
        .twwda-detail-value {
            font-weight: 600;
            color: #333;
            text-align: right;
        }

        /* Badges */
        .twwda-badge-type {
            border-radius: 999px;
            padding: .3rem .85rem;
            font-weight: 600;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .twwda-badge-type-21 {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .twwda-badge-type-7 {
            background: #fff3e0;
            color: #e65100;
        }
        .twwda-badge-committee {
            background: var(--twwda-navy-soft);
            color: var(--twwda-navy);
            font-weight: 600;
            border-radius: 999px;
            padding: .3rem .75rem;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .twwda-badge-version {
            background: var(--twwda-green-soft);
            color: var(--twwda-green);
            font-weight: 600;
            border-radius: 999px;
            padding: .25rem .65rem;
            font-size: .78rem;
        }

        /* Notice body reading panel */
        .twwda-notice-body {
            background: #fcfcfd;
            border: 1px solid #f2f2f2;
            border-radius: 12px;
            padding: 1.75rem 2rem;
            line-height: 1.85;
            font-size: 1rem;
            color: #333;
        }

        /* Agenda items */
        .twwda-agenda-list {
            counter-reset: agenda-counter;
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .twwda-agenda-list li {
            counter-increment: agenda-counter;
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .65rem 0;
            border-bottom: 1px solid #f0f2f5;
            font-size: .95rem;
            line-height: 1.6;
        }
        .twwda-agenda-list li:last-child {
            border-bottom: 0;
        }
        .twwda-agenda-list li::before {
            content: counter(agenda-counter);
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            background: var(--twwda-navy);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .8rem;
            margin-top: 2px;
        }

        /* Signatory section */
        .twwda-signatory-block {
            border-top: 2px solid var(--twwda-navy);
            padding-top: 1.25rem;
            margin-top: 1.5rem;
        }
        .twwda-signature-img {
            max-height: 80px;
            max-width: 240px;
            object-fit: contain;
        }

        /* Addressee / CC list */
        .twwda-addressee-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem 0;
            border-bottom: 1px solid #f4f5f7;
            font-size: .92rem;
        }
        .twwda-addressee-item:last-child {
            border-bottom: 0;
        }
        .twwda-addressee-icon {
            width: 32px;
            height: 32px;
            background: var(--twwda-navy-soft);
            color: var(--twwda-navy);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }

        /* Alert banners */
        .twwda-alert-rescheduled {
            background: #f0f0f0;
            border-left: 4px solid var(--twwda-navy);
            border-radius: 0 10px 10px 0;
            padding: .85rem 1.25rem;
            font-size: .9rem;
            color: #181818;
        }
        .twwda-alert-superseded {
            background: #fff8e1;
            border-left: 4px solid #ffa000;
            border-radius: 0 10px 10px 0;
            padding: .85rem 1.25rem;
            font-size: .9rem;
            color: #5d4200;
        }

        /* Download button */
        .twwda-btn-download {
            background: var(--twwda-green);
            border-color: var(--twwda-green);
            color: #fff;
            border-radius: 12px;
            min-height: 52px;
            font-weight: 700;
            font-size: 1.05rem;
            padding: .75rem 2rem;
            transition: all .2s ease;
        }
        .twwda-btn-download:hover {
            background: #077a2e;
            border-color: #077a2e;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(9, 145, 57, .3);
        }

        /* Section heading */
        .twwda-section-heading {
            color: var(--twwda-navy);
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        /* iPad-optimized touch targets */
        @media (max-width: 1024px) {
            .twwda-btn-back-pill {
                min-height: 44px;
                padding: .55rem 1.1rem;
            }
            .twwda-btn-download {
                min-height: 48px;
                width: 100%;
                font-size: 1rem;
            }
            .twwda-notice-body {
                padding: 1.25rem 1rem;
                font-size: .98rem;
            }
        }
        @media (max-width: 576px) {
            .twwda-detail-list .twwda-detail-row {
                flex-direction: column;
                gap: .15rem;
            }
            .twwda-detail-value {
                text-align: left;
            }
        }
    </style>
    @endpush

    <div class="content">
        <div class="container-fluid">

            {{-- Back button --}}
            <div class="mb-3">
                <a href="{{ route('board.portal.notices') }}" class="twwda-btn-back-pill">
                    <i class="mdi mdi-arrow-left"></i> Back to Notices
                </a>
            </div>

            {{-- Navy gradient header --}}
            <div class="card twwda-notice-show-card twwda-notice-show-gradient text-white mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        @if($committeeName)
                            <span class="badge bg-white bg-opacity-25 px-3 py-1" style="font-size:.82rem; border-radius:999px;">
                                <i class="mdi mdi-account-group me-1"></i>{{ $committeeName }}
                            </span>
                        @endif
                        @if($notice->version && $notice->version > 1)
                            <span class="badge bg-white bg-opacity-25 px-3 py-1" style="font-size:.78rem; border-radius:999px;">
                                <i class="mdi mdi-tag-outline me-1"></i>Version {{ $notice->version }}
                            </span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-2">
                        <i class="mdi mdi-file-document-outline me-2"></i>{{ $notice->subject }}
                    </h3>
                    <div class="d-flex flex-wrap gap-3 opacity-85" style="font-size:.9rem;">
                        @if($notice->reference_number)
                            <span>
                                <i class="mdi mdi-pound me-1"></i>{{ $notice->reference_number }}
                            </span>
                        @endif
                        @if($sentDate)
                            <span>
                                <i class="mdi mdi-send-check me-1"></i>Sent {{ $sentDate->format('jS F Y, g:i A') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rescheduled notice alert --}}
            @if($isRescheduled && $notice->parentNotice)
                <div class="twwda-alert-rescheduled mb-3 d-flex flex-wrap align-items-center gap-2">
                    <i class="mdi mdi-calendar-refresh" style="font-size:1.2rem;"></i>
                    <span>
                        This notice is a rescheduled version. Original notice:
                        <a href="{{ route('board.portal.notice.show', $notice->parentNotice) }}"
                           style="color: var(--twwda-navy); font-weight:600; text-decoration:underline;">
                            {{ $notice->parentNotice->subject }}
                            @if($notice->parentNotice->reference_number)
                                ({{ $notice->parentNotice->reference_number }})
                            @endif
                        </a>
                    </span>
                </div>
            @endif

            {{-- Superseded notice alert --}}
            @if($hasReplacements)
                @php $latestReplacement = $notice->replacementNotices->last(); @endphp
                <div class="twwda-alert-superseded mb-3 d-flex flex-wrap align-items-center gap-2">
                    <i class="mdi mdi-alert-outline" style="font-size:1.2rem;"></i>
                    <span>
                        This notice has been superseded by a newer version.
                        @if($latestReplacement)
                            <a href="{{ route('board.portal.notice.show', $latestReplacement) }}"
                               style="color: #5d4200; font-weight:600; text-decoration:underline;">
                                View latest notice <i class="mdi mdi-arrow-right"></i>
                            </a>
                        @endif
                    </span>
                </div>
            @endif

            <div class="row g-4">

                {{-- Main content column --}}
                <div class="col-lg-8">

                    {{-- Notice details card --}}
                    <div class="card twwda-notice-show-card mb-4">
                        <div class="card-body p-4">

                            {{-- Detail rows --}}
                            <div class="twwda-detail-list mb-4">
                                <div class="twwda-detail-row">
                                    <span class="twwda-detail-label">
                                        <i class="mdi mdi-pound me-1"></i>Reference Number
                                    </span>
                                    <span class="twwda-detail-value" style="font-family:'Courier New',Courier,monospace;">
                                        {{ $notice->reference_number ?? '---' }}
                                    </span>
                                </div>
                                @if($referenceDate)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">
                                            <i class="mdi mdi-calendar-outline me-1"></i>Reference Date
                                        </span>
                                        <span class="twwda-detail-value">
                                            {{ $referenceDate->format('jS F Y') }}
                                        </span>
                                    </div>
                                @endif
                                @if($noticeDate)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">
                                            <i class="mdi mdi-calendar me-1"></i>Notice Date
                                        </span>
                                        <span class="twwda-detail-value">
                                            {{ $noticeDate->format('jS F Y') }}
                                        </span>
                                    </div>
                                @endif
                                @if($notice->notice_type)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">
                                            <i class="mdi mdi-clock-outline me-1"></i>Notice Type
                                        </span>
                                        <span class="twwda-detail-value">
                                            <span class="twwda-badge-type {{ $notice->notice_type === '21_day' ? 'twwda-badge-type-21' : 'twwda-badge-type-7' }}">
                                                <i class="mdi {{ $notice->notice_type === '21_day' ? 'mdi-calendar-month' : 'mdi-calendar-week' }}"></i>
                                                {{ $noticeTypeLabel }} Notice
                                            </span>
                                        </span>
                                    </div>
                                @endif
                                @if($committeeName)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">
                                            <i class="mdi mdi-account-group me-1"></i>Committee
                                        </span>
                                        <span class="twwda-detail-value">{{ $committeeName }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Notice body --}}
                            @if($notice->body)
                                <div class="mb-4">
                                    <h6 class="twwda-section-heading">
                                        <i class="mdi mdi-text-box-outline"></i> Notice Content
                                    </h6>
                                    <div class="twwda-notice-body">
                                        {!! nl2br(e($notice->body)) !!}
                                    </div>
                                </div>
                            @endif

                            {{-- Agenda items --}}
                            @php
                                $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : (json_decode($notice->agenda_items, true) ?? []);
                            @endphp
                            @if(count($agendaItems) > 0)
                                <div class="mb-4">
                                    <h6 class="twwda-section-heading">
                                        <i class="mdi mdi-format-list-numbered"></i> Agenda Items
                                    </h6>
                                    <ol class="twwda-agenda-list">
                                        @foreach($agendaItems as $agendaItem)
                                            <li>{{ is_array($agendaItem) ? ($agendaItem['title'] ?? $agendaItem['text'] ?? json_encode($agendaItem)) : $agendaItem }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif

                            {{-- Closing text --}}
                            @if($notice->closing_text)
                                <div class="mb-4">
                                    <div class="twwda-notice-body" style="background:#fcfcfc;">
                                        {!! nl2br(e($notice->closing_text)) !!}
                                    </div>
                                </div>
                            @endif

                            {{-- Signatory section --}}
                            @if($notice->signatory_name || $notice->ceo_signature)
                                <div class="twwda-signatory-block">
                                    @if($notice->ceo_signature)
                                        <div class="mb-2">
                                            <img src="{{ $notice->ceo_signature }}"
                                                 alt="Signature"
                                                 class="twwda-signature-img">
                                        </div>
                                    @endif
                                    @if($notice->signatory_name)
                                        <div class="fw-bold" style="font-size:1.02rem; color:#141414;">
                                            {{ $notice->signatory_name }}
                                        </div>
                                    @endif
                                    @if($notice->signatory_title)
                                        <div class="text-muted" style="font-size:.9rem;">
                                            {{ $notice->signatory_title }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- Download PDF button --}}
                    <div class="text-center mb-4">
                        <a href="{{ route('board.portal.notice.download', $notice) }}"
                           class="btn twwda-btn-download d-inline-flex align-items-center gap-2">
                            <i class="mdi mdi-file-pdf-box" style="font-size:1.3rem;"></i>
                            Download Notice as PDF
                        </a>
                    </div>

                </div>

                {{-- Sidebar column --}}
                <div class="col-lg-4">

                    {{-- Addressees card --}}
                    @php
                        $rawAddressees = is_array($notice->addressees) ? $notice->addressees : (json_decode($notice->addressees, true) ?? []);
                        $resolvedAddressees = [];
                        foreach ($rawAddressees as $entry) {
                            if (is_array($entry)) {
                                $resolvedAddressees[] = $entry;
                            } elseif (is_numeric($entry)) {
                                $bm = \App\Models\BoardMember::find($entry);
                                if ($bm) {
                                    $resolvedAddressees[] = [
                                        'name' => $bm->names,
                                        'designation' => $bm->designation,
                                        'email' => $bm->email,
                                    ];
                                }
                            } elseif (is_string($entry)) {
                                $resolvedAddressees[] = ['name' => $entry];
                            }
                        }
                    @endphp
                    @if(count($resolvedAddressees) > 0)
                        <div class="card twwda-notice-show-card mb-4">
                            <div class="card-body p-4">
                                <h6 class="twwda-section-heading">
                                    <i class="mdi mdi-account-multiple"></i>
                                    Addressees
                                    <span class="badge ms-1" style="background:var(--twwda-navy-soft); color:var(--twwda-navy); font-size:.75rem;">
                                        {{ count($resolvedAddressees) }}
                                    </span>
                                </h6>
                                <div>
                                    @foreach($resolvedAddressees as $addressee)
                                        <div class="twwda-addressee-item">
                                            <span class="twwda-addressee-icon">
                                                <i class="mdi mdi-account"></i>
                                            </span>
                                            <div>
                                                <strong>{{ $addressee['name'] ?? 'Unknown' }}</strong>
                                                @if(!empty($addressee['designation']))
                                                    <br><small class="text-muted">{{ $addressee['designation'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CC card --}}
                    @php
                        $rawCc = is_array($notice->cc) ? $notice->cc : (json_decode($notice->cc, true) ?? []);
                        $resolvedCc = [];
                        foreach ($rawCc as $entry) {
                            if (is_array($entry)) {
                                $resolvedCc[] = $entry;
                            } elseif (is_string($entry)) {
                                $resolvedCc[] = ['name' => $entry];
                            }
                        }
                    @endphp
                    @if(count($resolvedCc) > 0)
                        <div class="card twwda-notice-show-card mb-4">
                            <div class="card-body p-4">
                                <h6 class="twwda-section-heading">
                                    <i class="mdi mdi-email-multiple-outline"></i>
                                    CC Recipients
                                    <span class="badge ms-1" style="background:var(--twwda-green-soft); color:var(--twwda-green); font-size:.75rem;">
                                        {{ count($resolvedCc) }}
                                    </span>
                                </h6>
                                <div>
                                    @foreach($resolvedCc as $ccRecipient)
                                        <div class="twwda-addressee-item">
                                            <span class="twwda-addressee-icon" style="background:var(--twwda-green-soft); color:var(--twwda-green);">
                                                <i class="mdi mdi-account-outline"></i>
                                            </span>
                                            <div>
                                                <strong>{{ $ccRecipient['name'] ?? 'Unknown' }}</strong>
                                                @if(!empty($ccRecipient['designation']))
                                                    <br><small class="text-muted">{{ $ccRecipient['designation'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Notice metadata card --}}
                    <div class="card twwda-notice-show-card mb-4">
                        <div class="card-body p-4">
                            <h6 class="twwda-section-heading">
                                <i class="mdi mdi-information-outline"></i> Notice Information
                            </h6>
                            <div class="twwda-detail-list">
                                @if($notice->status)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">Status</span>
                                        <span class="twwda-detail-value">
                                            @switch($notice->status)
                                                @case('sent')
                                                    <span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Sent</span>
                                                    @break
                                                @case('draft')
                                                    <span class="badge bg-secondary"><i class="mdi mdi-pencil me-1"></i>Draft</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger"><i class="mdi mdi-close-circle me-1"></i>Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-info text-dark"><i class="mdi mdi-information me-1"></i>{{ ucfirst($notice->status) }}</span>
                                            @endswitch
                                        </span>
                                    </div>
                                @endif
                                @if(optional($notice->creator)->name)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">Created By</span>
                                        <span class="twwda-detail-value">{{ $notice->creator->name }}</span>
                                    </div>
                                @endif
                                @if($notice->version)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">Version</span>
                                        <span class="twwda-detail-value">
                                            <span class="twwda-badge-version">
                                                <i class="mdi mdi-tag-outline me-1"></i>v{{ $notice->version }}
                                            </span>
                                        </span>
                                    </div>
                                @endif
                                @if(optional($notice->meeting)->title)
                                    <div class="twwda-detail-row">
                                        <span class="twwda-detail-label">Meeting</span>
                                        <span class="twwda-detail-value">{{ $notice->meeting->title }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            // Print-friendly: Ctrl+P
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    // Allow default print behavior
                }
            });
        })();
    </script>
    @endpush
@endsection
