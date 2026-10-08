<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaMeeting;
use App\Models\AlmanacSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingWorkflowService
{
    /**
     * Initiate a meeting from an almanac schedule.
     */
    public function initiate(AlmanacSchedule $schedule): TwwdaMeeting
    {
        return DB::transaction(function () use ($schedule) {
            // Generate reference number
            $committee = $schedule->boardCommittee;
            $count = TwwdaMeeting::where('board_committee_id', $committee->id)->count() + 1;
            $prefix = $this->getCommitteePrefix($committee->name);
            $refNumber = setting('reference_number_prefix', 'ORG') . "/{$prefix}/{$count}/" . now()->format('m/Y');

            $meeting = TwwdaMeeting::create([
                'almanac_schedule_id' => $schedule->id,
                'board_committee_id' => $schedule->board_committee_id,
                'title' => $this->generateTitle($schedule),
                'meeting_date' => $schedule->meeting_date,
                'meeting_time' => '10:00',
                'venue' => setting('default_venue', 'Board Room'),
                'reference_number' => $refNumber,
                'status' => 'initiated',
                'initiated_by' => Auth::id(),
                'initiated_at' => now(),
                'current_stage' => 1,
            ]);

            TwwdaAuditService::log(
                'meeting_initiated',
                'Stage 1: Meeting Initiation',
                TwwdaMeeting::class,
                $meeting->id,
                "Meeting initiated: {$meeting->title}",
                null,
                $meeting->toArray(),
                $meeting->id
            );

            return $meeting;
        });
    }

    /**
     * Advance the meeting to the next stage.
     */
    public function advanceStage(TwwdaMeeting $meeting, string $newStatus, int $newStage): void
    {
        $oldStatus = $meeting->status;
        $oldStage = $meeting->current_stage;

        $meeting->update([
            'status' => $newStatus,
            'current_stage' => $newStage,
        ]);

        TwwdaAuditService::log(
            'stage_advanced',
            "Stage {$newStage}",
            TwwdaMeeting::class,
            $meeting->id,
            "Meeting advanced from stage {$oldStage} to {$newStage}",
            ['status' => $oldStatus, 'stage' => $oldStage],
            ['status' => $newStatus, 'stage' => $newStage],
            $meeting->id
        );
    }

    /**
     * Check if user can initiate a meeting for a given committee.
     */
    public function canInitiate($user, $committee): bool
    {
        $isAudit = $this->isAuditCommittee($committee);

        // DD ICT can initiate all
        if ($user->hasRole('DD ICT') || $user->hasRole('Superadmin')) {
            return true;
        }

        // DD Internal Audit can initiate audit committee only
        if ($user->hasRole('DD Internal Audit') || $user->hasRole('Division Head')) {
            if ($isAudit) return true;
        }

        // Corporation Secretary can initiate all EXCEPT audit
        if ($user->hasRole('Corporation Secretary')) {
            return !$isAudit;
        }

        return false;
    }

    private function isAuditCommittee($committee): bool
    {
        return (bool) ($committee->is_audit_committee ?? false);
    }

    private function getCommitteePrefix(string $name): string
    {
        $map = [
            'audit' => 'AUD',
            'finance' => 'FIN',
            'technical' => 'TPS',
            'human resource' => 'HRA',
            'full board' => 'FB',
        ];

        $lower = strtolower($name);
        foreach ($map as $key => $prefix) {
            if (str_contains($lower, $key)) return $prefix;
        }
        return strtoupper(substr($name, 0, 3));
    }

    private function generateTitle(AlmanacSchedule $schedule): string
    {
        $committee = $schedule->boardCommittee;
        $count = TwwdaMeeting::where('board_committee_id', $committee->id)->count() + 1;
        $ordinal = $this->ordinal($count);
        return "The {$ordinal} {$committee->name} Committee Meeting";
    }

    private function ordinal(int $number): string
    {
        $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number . 'th';
        }
        return $number . $suffix[$number % 10];
    }
}
