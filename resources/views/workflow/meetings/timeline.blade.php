@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="btn btn-sm btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Meeting Timeline</h4>
                        </div>
                        <p class="text-muted mb-0 ms-5">
                            {{ $meeting->title }} &middot; {{ $meeting->reference_number }}
                        </p>
                    </div>
                    <div>
                        @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
                    </div>
                </div>
            </div>
        </div>

        {{-- Stage Indicator --}}
        @include('workflow.components.stage-indicator', ['currentStage' => $meeting->current_stage, 'stageLabel' => 'Meeting Timeline', 'meeting' => $meeting])

        @php
            $stageLabels = [
                1 => 'Initiation',
                2 => 'Internal Notice',
                3 => 'CEO Approval',
                4 => 'Notify Officers',
                5 => 'Board Papers',
                6 => 'Director Review',
                7 => 'CEO Review',
                8 => 'Preparation',
                9 => 'Meeting Day',
            ];

            $stageColors = [
                1 => '#6c757d',
                2 => '#c50010',
                3 => '#b57d00',
                4 => '#099139',
                5 => setting('primary_color', '#1a1a1a'),
                6 => '#238b47',
                7 => '#e83e8c',
                8 => '#fd7e14',
                9 => '#20c997',
            ];

            $actionColors = [
                'created'    => ['bg' => '#e6f6ec', 'text' => '#099139', 'icon' => 'mdi-plus-circle-outline'],
                'initiated'  => ['bg' => '#e6f6ec', 'text' => '#099139', 'icon' => 'mdi-rocket-launch-outline'],
                'submitted'  => ['bg' => '#f8f8f8', 'text' => '#c50010', 'icon' => 'mdi-send-outline'],
                'approved'   => ['bg' => '#e6f6ec', 'text' => '#099139', 'icon' => 'mdi-check-decagram-outline'],
                'rejected'   => ['bg' => '#fff5f8', 'text' => '#f1416c', 'icon' => 'mdi-close-circle-outline'],
                'updated'    => ['bg' => '#f8f8f8', 'text' => '#c50010', 'icon' => 'mdi-pencil-outline'],
                'assigned'   => ['bg' => '#ececec', 'text' => setting('primary_color', '#1a1a1a'), 'icon' => 'mdi-account-check-outline'],
                'uploaded'   => ['bg' => '#fff8dd', 'text' => '#b57d00', 'icon' => 'mdi-upload-outline'],
                'completed'  => ['bg' => '#e6f6ec', 'text' => '#099139', 'icon' => 'mdi-flag-checkered'],
            ];
        @endphp

        {{-- Timeline Content --}}
        <div class="row">
            <div class="col-lg-8 mx-auto">

                @if($auditLogs->isEmpty())
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="mdi mdi-timeline-clock-outline text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 mb-2">No Activity Yet</h5>
                            <p class="text-muted">No audit logs have been recorded for this meeting.</p>
                        </div>
                    </div>
                @else
                    <div class="twwda-vertical-timeline position-relative" style="padding-left: 40px;">
                        {{-- Vertical line --}}
                        <div style="position: absolute; left: 18px; top: 0; bottom: 0; width: 2px; background: linear-gradient(180deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #099139 50%, #e9ecef 100%);"></div>

                        @php $currentDate = null; @endphp

                        @foreach($auditLogs->sortByDesc('created_at') as $log)
                            @php
                                $logDate = $log->created_at->format('Y-m-d');
                                $action = strtolower($log->action ?? 'default');
                                $ac = $actionColors[$action] ?? ['bg' => '#f8f8f8', 'text' => '#7e8299', 'icon' => 'mdi-circle-medium'];
                                $stage = $log->stage ?? null;
                                $stageColor = $stage ? ($stageColors[$stage] ?? '#7e8299') : '#7e8299';
                            @endphp

                            {{-- Date separator --}}
                            @if($currentDate !== $logDate)
                                @php $currentDate = $logDate; @endphp
                                <div class="position-relative mb-3" style="margin-left: -40px;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-twwda-navy d-flex align-items-center justify-content-center"
                                             style="width: 38px; height: 38px; z-index: 1;">
                                            <i class="mdi mdi-calendar text-white" style="font-size: 16px;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <span class="fw-semibold" style="font-size: 14px; color: var(--text-dark);">
                                                {{ $log->created_at->format('l, d F Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Timeline entry --}}
                            <div class="position-relative mb-3" style="margin-left: -40px;">
                                <div class="d-flex align-items-start">
                                    {{-- Timeline dot --}}
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                         style="width: 38px; height: 38px; z-index: 1; background: {{ $ac['bg'] }};">
                                        <i class="mdi {{ $ac['icon'] }}" style="font-size: 16px; color: {{ $ac['text'] }};"></i>
                                    </div>

                                    {{-- Content card --}}
                                    <div class="flex-grow-1 ms-3">
                                        <div class="card mb-0">
                                            <div class="card-body py-3 px-4">
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1 fw-semibold" style="font-size: 13px; color: var(--text-dark);">
                                                            {{ $log->description }}
                                                        </p>
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-account-outline me-1"></i>
                                                                {{ $log->user->name ?? 'System' }}
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                                {{ $log->created_at->format('H:i') }}
                                                            </small>
                                                            @if($stage && isset($stageLabels[$stage]))
                                                                <span class="badge" style="background: {{ $stageColor }}15; color: {{ $stageColor }}; border: 1px solid {{ $stageColor }}33; font-size: 10px;">
                                                                    Stage {{ $stage }}: {{ $stageLabels[$stage] }}
                                                                </span>
                                                            @endif
                                                            @if($log->module)
                                                                <span class="badge bg-secondary" style="font-size: 10px;">{{ $log->module }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <small class="text-muted flex-shrink-0 ms-3" style="font-size: 11px;">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Timeline end marker --}}
                        <div class="position-relative" style="margin-left: -40px;">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                     style="width: 38px; height: 38px; z-index: 1;">
                                    <i class="mdi mdi-flag-variant-outline text-muted" style="font-size: 16px;"></i>
                                </div>
                                <div class="ms-3">
                                    <span class="text-muted" style="font-size: 12px;">Meeting initiated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection
