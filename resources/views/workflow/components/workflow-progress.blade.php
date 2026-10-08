{{-- Workflow Progress Sidebar Component --}}
{{-- Compact vertical stepper with connected dots --}}
{{-- Usage: @include('workflow.components.workflow-progress', ['currentStage' => $meeting->current_stage, 'status' => $meeting->status]) --}}

@php
    $stages = [
        1 => ['label' => 'Meeting Initiated', 'icon' => 'mdi-rocket-launch-outline'],
        2 => ['label' => 'Internal Notice', 'icon' => 'mdi-file-document-edit-outline'],
        3 => ['label' => 'CEO Notice Approval', 'icon' => 'mdi-check-decagram-outline'],
        4 => ['label' => 'Officers Assigned', 'icon' => 'mdi-account-group-outline'],
        5 => ['label' => 'Board Papers', 'icon' => 'mdi-file-multiple-outline'],
        6 => ['label' => 'Director Review', 'icon' => 'mdi-clipboard-check-outline'],
        7 => ['label' => 'CEO Paper Review', 'icon' => 'mdi-seal-variant'],
        8 => ['label' => 'Preparations', 'icon' => 'mdi-format-list-checks'],
        9 => ['label' => 'Meeting Day', 'icon' => 'mdi-video-outline'],
    ];
    $current = $currentStage ?? 1;
    $isRejected = str_contains($status ?? '', 'rejected');
    $totalStages = count($stages);
    $completedStages = max(0, $current - 1);
    $progressPercent = round(($completedStages / ($totalStages - 1)) * 100);
@endphp

<div class="card border-0 shadow-sm mb-3 twwda-progress-card">
    <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between twwda-progress-header">
        <h6 class="mb-0 text-white fw-semibold" style="font-size: 12.5px;">
            <i class="mdi mdi-progress-check me-1"></i> WORKFLOW PROGRESS
        </h6>
        <span class="twwda-progress-counter">{{ $completedStages }}/{{ $totalStages }}</span>
    </div>

    {{-- Progress bar --}}
    <div style="height: 4px; background: #e5e7eb;">
        <div class="twwda-progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
    </div>

    <div class="card-body px-3 py-2">
        <div class="twwda-stepper">
            @foreach($stages as $num => $stageInfo)
                @php
                    $isComplete = $num < $current;
                    $isCurrent = $num == $current;
                    $isFuture = $num > $current;
                    $stepRejected = $isCurrent && $isRejected;
                @endphp
                <a href="#step-{{ $num }}"
                   class="d-flex align-items-center text-decoration-none twwda-stepper-item py-1 {{ $isCurrent ? 'twwda-stepper-active' : '' }}"
                   onclick="document.getElementById('step-{{ $num }}')?.scrollIntoView({behavior:'smooth', block:'center'});"
                   style="min-height: 32px;">

                    {{-- Vertical line + dot --}}
                    <div class="twwda-stepper-track flex-shrink-0 position-relative" style="width: 24px;">
                        @if($num > 1)
                            <div class="twwda-stepper-line-top" style="background: {{ $isComplete || $isCurrent ? '#099139' : '#e5e7eb' }};"></div>
                        @endif
                        @if($num < $totalStages)
                            <div class="twwda-stepper-line-bottom" style="background: {{ $isComplete ? '#099139' : '#e5e7eb' }};"></div>
                        @endif

                        {{-- Dot --}}
                        @if($isComplete)
                            <div class="twwda-stepper-dot twwda-dot-complete">
                                <i class="mdi mdi-check" style="font-size: 10px;"></i>
                            </div>
                        @elseif($stepRejected)
                            <div class="twwda-stepper-dot twwda-dot-rejected">
                                <i class="mdi mdi-exclamation" style="font-size: 10px;"></i>
                            </div>
                        @elseif($isCurrent)
                            <div class="twwda-stepper-dot twwda-dot-current"></div>
                        @else
                            <div class="twwda-stepper-dot twwda-dot-future"></div>
                        @endif
                    </div>

                    {{-- Icon + Label --}}
                    <div class="d-flex align-items-center gap-2 flex-grow-1 ms-1 overflow-hidden">
                        <i class="mdi {{ $stageInfo['icon'] }} flex-shrink-0"
                           style="font-size: 14px; color: {{ $isComplete ? '#099139' : ($isCurrent ? setting('primary_color', '#1a1a1a') : ($stepRejected ? '#dc3545' : '#dd3c49')) }};"></i>
                        <span class="text-truncate"
                              style="font-size: 12px; line-height: 1.3;
                                     font-weight: {{ $isCurrent ? '700' : ($isComplete ? '500' : '400') }};
                                     color: {{ $isComplete ? '#099139' : ($isCurrent ? setting('primary_color', '#1a1a1a') : ($stepRejected ? '#dc3545' : '#94a3b8')) }};">
                            {{ $stageInfo['label'] }}
                        </span>
                    </div>

                    {{-- Current indicator arrow --}}
                    @if($isCurrent)
                        <i class="mdi mdi-chevron-left flex-shrink-0" style="font-size: 14px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* ── Progress Header ─────────────────────────────────────────── */
    .twwda-progress-header {
        background: linear-gradient(135deg, #212121, {{ setting('primary_color', '#1a1a1a') }}) !important;
        border: none !important;
    }
    .twwda-progress-counter {
        font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.7);
        background: rgba(255,255,255,0.12); padding: 2px 10px; border-radius: 12px;
    }
    .twwda-progress-bar-fill {
        height: 100%; transition: width 0.8s ease;
        background: linear-gradient(90deg, #099139, #22c55e);
        border-radius: 0 4px 4px 0;
    }

    /* ── Stepper Track ─────────────────────────────────────────── */
    .twwda-stepper-item {
        transition: background 0.15s; border-radius: 6px;
        padding-left: 4px; padding-right: 4px;
    }
    .twwda-stepper-item:hover { background: #fafafa; }
    .twwda-stepper-active { background: #f4f4f4 !important; }

    .twwda-stepper-track { display: flex; align-items: center; justify-content: center; }

    .twwda-stepper-dot {
        width: 16px; height: 16px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 2; flex-shrink: 0;
    }
    .twwda-dot-complete { background: #099139; color: #fff; }
    .twwda-dot-current  { background: {{ setting('primary_color', '#1a1a1a') }}; box-shadow: 0 0 0 3px rgba(200, 16, 46,0.2); }
    .twwda-dot-rejected { background: #dc3545; color: #fff; box-shadow: 0 0 0 3px rgba(220,53,69,0.2); }
    .twwda-dot-future   { background: #e5e7eb; border: 2px solid #d1d5db; width: 12px; height: 12px; }

    /* Vertical connector lines */
    .twwda-stepper-line-top,
    .twwda-stepper-line-bottom {
        position: absolute; left: 50%; width: 2px; transform: translateX(-50%); z-index: 1;
    }
    .twwda-stepper-line-top    { top: 0; height: 50%; }
    .twwda-stepper-line-bottom { bottom: 0; height: 50%; }
</style>
