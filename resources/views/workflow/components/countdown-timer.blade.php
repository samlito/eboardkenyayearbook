{{-- Countdown Timer Component --}}
{{-- Usage: @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date, 'label' => 'Meeting']) --}}

@php
    $daysLeft = now()->diffInDays($meetingDate, false);
    $label = $label ?? 'Meeting';
@endphp

<div class="twwda-countdown d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3
    @if($daysLeft < 0) bg-soft-danger
    @elseif($daysLeft <= 3) bg-soft-warning
    @elseif($daysLeft <= 7) bg-soft-info
    @else bg-soft-primary
    @endif" style="font-size: 13px;">
    <i class="mdi mdi-clock-outline" style="font-size: 18px;"></i>
    <div>
        @if($daysLeft < 0)
            <span class="fw-semibold text-danger">{{ abs($daysLeft) }} days overdue</span>
        @elseif($daysLeft == 0)
            <span class="fw-semibold text-warning">{{ $label }} is today!</span>
        @elseif($daysLeft == 1)
            <span class="fw-semibold text-warning">{{ $label }} is tomorrow</span>
        @else
            <span class="fw-semibold">{{ $daysLeft }} days</span> to {{ strtolower($label) }}
        @endif
    </div>
</div>
