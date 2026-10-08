@extends('admin_dashboard')
@section('admin')

@php
    $meeting = $assignment->meeting;
    $notice = $meeting->internalNotice ?? null;
    $hasApprovedNotice = $notice && $notice->isApproved();
@endphp

<div class="content">
    <div class="container-fluid">

        {{-- Compact Page Header --}}
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-start gap-3">
                        <a href="{{ route('workflow.officer.assignments') }}" class="btn btn-sm btn-light rounded-circle mt-1" style="width: 32px; height: 32px; padding: 0; line-height: 32px;">
                            <i class="mdi mdi-arrow-left"></i>
                        </a>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="mb-0">Item {{ $assignment->agenda_item_number }}: {{ $assignment->agenda_item_title }}</h5>
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap" style="font-size: 12px; color: #6c757d;">
                                <i class="mdi mdi-account-group-outline"></i>
                                <span>{{ $meeting->boardCommittee->name ?? '-' }}</span>
                                <span class="mx-1">&middot;</span>
                                <i class="mdi mdi-calendar-outline"></i>
                                <span>{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') : '-' }}</span>
                                <span class="mx-1">&middot;</span>
                                <span>{{ $meeting->reference_number ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($hasApprovedNotice)
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#noticeModal">
                                <i class="mdi mdi-file-sign me-1"></i> View Notice
                            </button>
                        @elseif($notice)
                            <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 11px;">
                                <i class="mdi mdi-clock-outline me-1"></i> Notice Pending
                            </span>
                        @endif
                        @if($assignment->isRead())
                            <span class="badge bg-success py-1 px-2" style="font-size: 11px;">
                                <i class="mdi mdi-check-circle me-1"></i> Read {{ ($assignment->system_read_at ?? $assignment->email_read_at)?->format('d M') }}
                            </span>
                        @else
                            <span class="badge bg-info py-1 px-2" style="font-size: 11px;">
                                <i class="mdi mdi-eye-check-outline me-1"></i> Just Read
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column --}}
            <div class="col-xl-8 col-lg-7 mb-3">

                {{-- Assignment Info --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header border-0 py-2" style="background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #212121 100%);">
                        <h6 class="mb-0 text-white" style="font-size: 13px;">
                            <i class="mdi mdi-clipboard-text-outline me-1"></i> Assignment Details
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 32px; height: 32px; background: rgba(200, 16, 46,0.1); font-size: 11px; font-weight: 700; color: {{ setting('primary_color', '#1a1a1a') }};">
                                        {{ strtoupper(substr($assignment->assigner->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em;">Assigned By</div>
                                        <div style="font-size: 13px; font-weight: 500;">{{ $assignment->assigner->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 32px; height: 32px; background: rgba(200, 16, 46,0.1);">
                                        <i class="mdi mdi-calendar-outline" style="font-size: 14px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em;">Assigned On</div>
                                        <div style="font-size: 13px; font-weight: 500;">{{ $assignment->created_at->format('d M Y, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @if($assignment->director)
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 32px; height: 32px; background: rgba(9,145,57,0.1); font-size: 11px; font-weight: 700; color: #099139;">
                                            {{ strtoupper(substr($assignment->director->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em;">Supervising Director</div>
                                            <div style="font-size: 13px; font-weight: 500;">{{ $assignment->director->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 32px; height: 32px; background: rgba(200, 16, 46,0.1);">
                                        <i class="mdi mdi-text-box-outline" style="font-size: 14px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em;">Meeting</div>
                                        <div style="font-size: 13px; font-weight: 500;">{{ $meeting->title }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Board Papers --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between border-0 py-2">
                        <h6 class="mb-0" style="font-size: 13px;">
                            <i class="mdi mdi-file-document-outline me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i> Board Papers
                            @if($assignment->boardPapers->count())
                                <span class="badge bg-success ms-1" style="font-size: 10px;">{{ $assignment->boardPapers->count() }}</span>
                            @endif
                        </h6>
                        @if($assignment->boardPapers->isEmpty())
                            <a href="{{ route('workflow.papers.create', $meeting->id) }}?assignment_id={{ $assignment->id }}"
                               class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }}; font-size: 12px;">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Create Board Paper
                            </a>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        @if($assignment->boardPapers->isEmpty())
                            <div class="text-center py-3">
                                <i class="mdi mdi-file-document-edit-outline" style="font-size: 40px; color: #d1d5db;"></i>
                                <p class="text-muted mb-0 mt-2" style="font-size: 13px;">
                                    No board paper yet. Click <strong>Create Board Paper</strong> to start.
                                </p>
                            </div>
                        @else
                            @foreach($assignment->boardPapers as $paper)
                                <div class="d-flex align-items-center justify-content-between p-2 rounded mb-2" style="background: #f8f9fa;">
                                    <div class="d-flex align-items-center gap-2 min-width-0">
                                        <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 32px; height: 32px; background: rgba(200, 16, 46,0.1);">
                                            <i class="mdi mdi-file-document" style="font-size: 16px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                        </div>
                                        <div class="min-width-0">
                                            <div class="text-truncate fw-medium" style="font-size: 13px;">{{ Str::limit($paper->title, 55) }}</div>
                                            <div style="font-size: 11px; color: #6c757d;">{{ $paper->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'submitted' => 'info',
                                                'director_review' => 'warning',
                                                'director_approved' => 'primary',
                                                'ceo_review' => 'warning',
                                                'ceo_approved' => 'success',
                                                'rejected_by_director' => 'danger',
                                                'rejected_by_ceo' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Draft',
                                                'submitted' => 'Submitted',
                                                'director_review' => 'Director Review',
                                                'director_approved' => 'Director Approved',
                                                'ceo_review' => 'CEO Review',
                                                'ceo_approved' => 'Approved',
                                                'rejected_by_director' => 'Rejected',
                                                'rejected_by_ceo' => 'Rejected',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$paper->status] ?? 'secondary' }}" style="font-size: 10px;">
                                            {{ $statusLabels[$paper->status] ?? ucfirst($paper->status) }}
                                        </span>
                                        <a href="{{ route('workflow.papers.show', $paper->id) }}" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px;">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-xl-4 col-lg-5 mb-3">

                {{-- Meeting Snapshot --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header border-0 py-2">
                        <h6 class="mb-0" style="font-size: 13px;">
                            <i class="mdi mdi-calendar-clock me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i> Meeting Info
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-calendar-outline text-muted" style="font-size: 16px; width: 20px;"></i>
                            <span style="font-size: 13px;">{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d F Y') : '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-clock-outline text-muted" style="font-size: 16px; width: 20px;"></i>
                            <span style="font-size: 13px;">{{ $meeting->meeting_time ?? '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-map-marker-outline text-muted" style="font-size: 16px; width: 20px;"></i>
                            <span style="font-size: 13px;">{{ $meeting->venue ?? '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-identifier text-muted" style="font-size: 16px; width: 20px;"></i>
                            <span style="font-size: 13px;">{{ $meeting->reference_number ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Activity Timeline (User-relevant only) --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header border-0 py-2">
                        <h6 class="mb-0" style="font-size: 13px;">
                            <i class="mdi mdi-timeline-clock-outline me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i> Your Activity
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        @php
                            $timelineEvents = collect();

                            $timelineEvents->push([
                                'icon' => 'clipboard-arrow-down',
                                'color' => setting('primary_color', '#1a1a1a'),
                                'label' => 'Assignment Received',
                                'detail' => 'from ' . ($assignment->assigner->name ?? 'System'),
                                'date' => $assignment->created_at,
                            ]);

                            if ($assignment->isRead()) {
                                $readAt = $assignment->system_read_at ?? $assignment->email_read_at;
                                $timelineEvents->push([
                                    'icon' => 'eye-check-outline',
                                    'color' => '#099139',
                                    'label' => 'Viewed Assignment',
                                    'detail' => null,
                                    'date' => $readAt,
                                ]);
                            }

                            foreach ($assignment->boardPapers as $paper) {
                                $timelineEvents->push([
                                    'icon' => 'file-plus-outline',
                                    'color' => setting('primary_color', '#1a1a1a'),
                                    'label' => 'Paper Created',
                                    'detail' => Str::limit($paper->title, 30),
                                    'date' => $paper->created_at,
                                ]);

                                if ($paper->submitted_at) {
                                    $timelineEvents->push([
                                        'icon' => 'send-check-outline',
                                        'color' => '#e00214',
                                        'label' => 'Paper Submitted',
                                        'detail' => null,
                                        'date' => $paper->submitted_at,
                                    ]);
                                }

                                if ($paper->director_approved_at) {
                                    $timelineEvents->push([
                                        'icon' => 'check-decagram',
                                        'color' => '#099139',
                                        'label' => 'Director Approved',
                                        'detail' => $paper->directorApprover->name ?? null,
                                        'date' => $paper->director_approved_at,
                                    ]);
                                }

                                if (in_array($paper->status, ['rejected_by_director'])) {
                                    $timelineEvents->push([
                                        'icon' => 'close-circle-outline',
                                        'color' => '#dc3545',
                                        'label' => 'Rejected by Director',
                                        'detail' => null,
                                        'date' => $paper->updated_at,
                                    ]);
                                }

                                if ($paper->status === 'ceo_approved') {
                                    $timelineEvents->push([
                                        'icon' => 'shield-check-outline',
                                        'color' => '#099139',
                                        'label' => 'CEO Approved',
                                        'detail' => null,
                                        'date' => $paper->ceo_approved_at ?? $paper->updated_at,
                                    ]);
                                }

                                if (in_array($paper->status, ['rejected_by_ceo'])) {
                                    $timelineEvents->push([
                                        'icon' => 'close-circle-outline',
                                        'color' => '#dc3545',
                                        'label' => 'Rejected by CEO',
                                        'detail' => null,
                                        'date' => $paper->updated_at,
                                    ]);
                                }
                            }

                            $timelineEvents = $timelineEvents->sortBy('date')->values();
                        @endphp

                        @foreach($timelineEvents as $idx => $event)
                            <div class="d-flex align-items-start gap-2 {{ $idx < $timelineEvents->count() - 1 ? 'mb-3' : '' }}">
                                <div class="position-relative flex-shrink-0" style="width: 24px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 24px; height: 24px; background: {{ $event['color'] }}10;">
                                        <i class="mdi mdi-{{ $event['icon'] }}" style="font-size: 13px; color: {{ $event['color'] }};"></i>
                                    </div>
                                    @if($idx < $timelineEvents->count() - 1)
                                        <div style="position: absolute; left: 50%; top: 26px; bottom: -12px; width: 1px; background: #e9ecef; transform: translateX(-50%);"></div>
                                    @endif
                                </div>
                                <div class="min-width-0">
                                    <div style="font-size: 12px; font-weight: 600; color: #343a40;">{{ $event['label'] }}</div>
                                    @if($event['detail'])
                                        <div class="text-muted text-truncate" style="font-size: 11px;">{{ $event['detail'] }}</div>
                                    @endif
                                    <div class="text-muted" style="font-size: 10px;">
                                        {{ $event['date'] instanceof \Carbon\Carbon ? $event['date']->format('d M Y, h:i A') : \Carbon\Carbon::parse($event['date'])->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($timelineEvents->isEmpty())
                            <div class="text-center py-2">
                                <small class="text-muted">No activity yet.</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="d-grid gap-2">
                    @if($assignment->boardPapers->isEmpty())
                        <a href="{{ route('workflow.papers.create', $meeting->id) }}?assignment_id={{ $assignment->id }}"
                           class="btn btn-sm text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};">
                            <i class="mdi mdi-file-plus-outline me-1"></i> Create Board Paper
                        </a>
                    @endif
                    <a href="{{ route('workflow.officer.assignments') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to My Assignments
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Internal Notice Modal --}}
@if($hasApprovedNotice)
<div class="modal fade" id="noticeModal" tabindex="-1" aria-labelledby="noticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2" style="background: linear-gradient(135deg, #099139 0%, #0a7a30 100%);">
                <h6 class="modal-title text-white" id="noticeModalLabel">
                    <i class="mdi mdi-file-sign me-1"></i> Internal Notice
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="font-family: 'Times New Roman', Times, serif;">

                {{-- Letterhead --}}
                @include('workflow.components.letterhead')

                {{-- Reference and Date --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="mb-0" style="font-size: 13px;">
                            <strong>Our Ref:</strong> {{ $notice->reference_number ?? '-' }}
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="mb-0" style="font-size: 13px;">
                            <strong>Date:</strong> {{ $notice->reference_date ? \Carbon\Carbon::parse($notice->reference_date)->format('d F Y') : now()->format('d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- Addressee --}}
                <div class="mb-3" style="font-size: 13px; line-height: 1.7;">
                    <p class="mb-0">All Members,</p>
                    <p class="mb-0">{{ $meeting->boardCommittee->name ?? 'Board Committee' }}</p>
                    <p class="mb-0">{{ setting('institution_name', 'eBoard System') }}</p>
                </div>

                {{-- Subject --}}
                <div class="text-center mb-3">
                    <p class="mb-0" style="font-size: 14px; font-weight: bold; text-decoration: underline;">
                        RE: {{ strtoupper($notice->subject ?? '-') }}
                    </p>
                </div>

                {{-- Body --}}
                @if($notice->body)
                    <div class="mb-3" style="font-size: 13px; line-height: 1.7; text-align: justify;">
                        {!! $notice->body !!}
                    </div>
                @endif

                {{-- Agenda Items --}}
                @php
                    $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : json_decode($notice->agenda_items, true);
                @endphp
                @if($agendaItems && count($agendaItems) > 0)
                    <div class="mb-3">
                        <p class="mb-2" style="font-size: 13px; font-weight: bold;">AGENDA:</p>
                        <ol style="font-size: 13px; line-height: 1.7; padding-left: 20px;">
                            @foreach($agendaItems as $idx => $item)
                                @php
                                    $itemTitle = is_array($item) ? ($item['title'] ?? $item['name'] ?? '') : $item;
                                    $itemNumber = is_array($item) ? ($item['number'] ?? ($idx + 1)) : ($idx + 1);
                                    $isMyItem = $itemNumber == $assignment->agenda_item_number;
                                @endphp
                                <li class="mb-1 {{ $isMyItem ? 'p-1 rounded' : '' }}"
                                    style="{{ $isMyItem ? 'background: rgba(200, 16, 46,0.08); border-left: 3px solid ' . setting('primary_color', '#1a1a1a') . '; font-weight: 600; color: ' . setting('primary_color', '#1a1a1a') . ';' : '' }}">
                                    {{ $itemTitle }}
                                    @if($isMyItem)
                                        <span class="badge text-white ms-1" style="font-size: 9px; background: {{ setting('primary_color', '#1a1a1a') }};">Your Item</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                {{-- Meeting Details --}}
                <div class="mb-3 p-3 rounded" style="background: #f8f9fa; font-size: 13px; line-height: 1.7;">
                    <p class="mb-1"><strong>Date:</strong> {{ $notice->meeting_date ? \Carbon\Carbon::parse($notice->meeting_date)->format('l, d F Y') : '-' }}</p>
                    <p class="mb-1"><strong>Time:</strong> {{ $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('h:i A') : '-' }}</p>
                    <p class="mb-0"><strong>Venue:</strong> {{ $notice->meeting_venue ?? $meeting->venue ?? '-' }}</p>
                </div>

                {{-- Closing --}}
                <div class="mb-3" style="font-size: 13px; line-height: 1.7;">
                    <p>Your attendance is highly appreciated.</p>
                </div>

                {{-- Approver Signature --}}
                @php
                    $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
                    $isAuditCommittee = in_array($meeting->board_committee_id, $auditCommitteeIds);
                    $approverRoleFull = $isAuditCommittee ? 'Deputy Director Internal Audit' : setting('signatory_title_ceo', 'Chief Executive Officer');
                @endphp
                <div class="mt-4 mb-3">
                    @if(in_array($notice->status, ['approved', 'special_approved']) && $notice->ceo_signature)
                        <div class="mb-2">
                            <img src="{{ $notice->ceo_signature }}" alt="Signature" style="max-height: 60px; max-width: 200px;">
                        </div>
                    @else
                        <div style="height: 50px; border-bottom: 1px solid #ccc; width: 200px;"></div>
                    @endif
                    <p class="mb-0 mt-2" style="font-size: 13px; font-weight: bold;">
                        {{ $notice->approver->name ?? $approverRoleFull }}
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                        {{ $notice->status === 'special_approved' ? 'Special Approval' : $approverRoleFull }}
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                        {{ setting('institution_name', 'eBoard System') }}
                    </p>
                </div>
            </div>
            <div class="modal-footer py-2 justify-content-between">
                <div>
                    <span class="badge bg-success" style="font-size: 10px;">
                        <i class="mdi mdi-check-circle me-1"></i>
                        {{ $notice->status === 'special_approved' ? 'Self Approved' : 'Approved' }}
                        @if($notice->approved_at)
                            &middot; {{ $notice->approved_at->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('workflow.internal-notice.preview', $notice->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="mdi mdi-open-in-new me-1"></i> Open Full Page
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .min-width-0 { min-width: 0; }
</style>
@endpush
