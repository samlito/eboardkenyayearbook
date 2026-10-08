{{-- Compact Stage Indicator Bar --}}
{{-- Lightweight breadcrumb-style progress for sub-pages (forms, reviews, etc.) --}}
{{-- Usage: @include('workflow.components.stage-indicator', ['currentStage' => 2, 'stageLabel' => 'Internal Notice', 'meeting' => $meeting]) --}}

@php
    $stage = $currentStage ?? 1;
    $label = $stageLabel ?? 'Stage ' . $stage;
    $totalStages = 9;
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-4">
        <div class="d-flex align-items-center justify-content-between">
            {{-- Left: stage info --}}
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                     style="width: 30px; height: 30px; font-size: 13px; background: {{ setting('primary_color', '#1a1a1a') }}; flex-shrink: 0;">
                    {{ $stage }}
                </div>
                <div>
                    <span class="fw-semibold d-block" style="font-size: 13px; color: #141414;">{{ $label }}</span>
                    <small class="text-muted" style="font-size: 11px;">Step {{ $stage }} of {{ $totalStages }}</small>
                </div>
            </div>

            {{-- Right: mini progress dots --}}
            <div class="d-none d-sm-flex align-items-center gap-1">
                @for($i = 1; $i <= $totalStages; $i++)
                    <div class="rounded-circle"
                         style="width: {{ $i == $stage ? '10px' : '6px' }};
                                height: {{ $i == $stage ? '10px' : '6px' }};
                                background: {{ $i < $stage ? '#099139' : ($i == $stage ? setting('primary_color', '#1a1a1a') : '#dee2e6') }};
                                transition: all 0.2s;"
                         title="Stage {{ $i }}"></div>
                @endfor
            </div>

            {{-- Back to meeting link --}}
            @if(isset($meeting))
                <a href="{{ route('workflow.meetings.show', $meeting->id) }}"
                   class="btn btn-sm btn-light border d-flex align-items-center gap-1"
                   style="font-size: 11.5px; border-radius: 6px;">
                    <i class="mdi mdi-arrow-left" style="font-size: 14px;"></i>
                    <span class="d-none d-md-inline">Meeting</span>
                </a>
            @endif
        </div>
    </div>
</div>
