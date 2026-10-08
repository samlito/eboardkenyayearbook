<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaOfficerAssignment;
use App\Models\Twwda\TwwdaUserDirectorateMapping;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficerAssignmentService
{
    /**
     * Assign officers with agenda items to a meeting.
     *
     * Each assignment entry should contain:
     * - user_id: The DD/officer being assigned
     * - agenda_item_number: Agenda item number
     * - agenda_item_title: Agenda item title
     */
    public function assign(TwwdaMeeting $meeting, array $assignments, int $assignedBy): array
    {
        return DB::transaction(function () use ($meeting, $assignments, $assignedBy) {
            $created = [];

            foreach ($assignments as $assignment) {
                // Resolve the director for this DD if one exists
                $directorId = $this->resolveDirector($assignment['user_id']);

                $record = TwwdaOfficerAssignment::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $assignment['user_id'],
                    'director_id' => $directorId,
                    'agenda_item_number' => $assignment['agenda_item_number'],
                    'agenda_item_title' => $assignment['agenda_item_title'],
                    'assigned_by' => $assignedBy,
                    'notified_at' => now(),
                    'status' => 'assigned',
                ]);

                // Notify the assigned officer (DD)
                $user = User::find($assignment['user_id']);
                if ($user) {
                    TwwdaNotificationService::notify(
                        $user->id,
                        'Board Paper Assignment',
                        "You have been assigned agenda item \"{$assignment['agenda_item_title']}\" for meeting \"{$meeting->title}\". Please prepare your board paper.",
                        'officer_assignment',
                        "/workflow/officer/assignments/{$record->id}",
                        $meeting->id,
                        $user->email,
                        'workflow.emails.officer-assigned',
                        ['assignment' => $record, 'meeting' => $meeting]
                    );
                }

                // For DDs with a director, also notify the director (but not if the director IS the assigned user)
                if ($directorId && $directorId !== (int) $assignment['user_id']) {
                    $director = User::find($directorId);
                    if ($director) {
                        TwwdaNotificationService::notify(
                            $director->id,
                            'Board Paper Assignment - Director Notification',
                            "Your deputy has been assigned agenda item \"{$assignment['agenda_item_title']}\" for meeting \"{$meeting->title}\". You will review the paper once submitted.",
                            'director_assignment_notice',
                            "/workflow/meetings/{$meeting->id}/assignment-dashboard",
                            $meeting->id,
                            $director->email,
                            'workflow.emails.director-assignment-notice',
                            ['assignment' => $record, 'meeting' => $meeting]
                        );
                    }
                }

                $created[] = $record;
            }

            // Advance the meeting to stage 4 if not already there
            if ($meeting->current_stage < 4) {
                $meeting->update([
                    'status' => 'officers_assigned',
                    'current_stage' => 4,
                ]);
            }

            TwwdaAuditService::log(
                'officers_assigned',
                'Stage 4: Officer Assignment',
                TwwdaMeeting::class,
                $meeting->id,
                "Assigned " . count($assignments) . " officer(s) to meeting: {$meeting->title}",
                null,
                ['assignments' => collect($created)->pluck('id')->toArray()],
                $meeting->id
            );

            return $created;
        });
    }

    /**
     * Add additional officers to an existing meeting assignment list.
     */
    public function addMore(TwwdaMeeting $meeting, array $assignments): array
    {
        return DB::transaction(function () use ($meeting, $assignments) {
            $created = [];

            foreach ($assignments as $assignment) {
                $directorId = $this->resolveDirector($assignment['user_id']);

                $record = TwwdaOfficerAssignment::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $assignment['user_id'],
                    'director_id' => $directorId,
                    'agenda_item_number' => $assignment['agenda_item_number'],
                    'agenda_item_title' => $assignment['agenda_item_title'],
                    'assigned_by' => Auth::id(),
                    'notified_at' => now(),
                    'status' => 'assigned',
                ]);

                $user = User::find($assignment['user_id']);
                if ($user) {
                    TwwdaNotificationService::notify(
                        $user->id,
                        'Additional Board Paper Assignment',
                        "You have been assigned an additional agenda item \"{$assignment['agenda_item_title']}\" for meeting \"{$meeting->title}\".",
                        'officer_assignment',
                        "/workflow/officer/assignments/{$record->id}",
                        $meeting->id,
                        $user->email,
                        'workflow.emails.officer-assigned',
                        ['assignment' => $record, 'meeting' => $meeting]
                    );
                }

                if ($directorId && $directorId !== (int) $assignment['user_id']) {
                    $director = User::find($directorId);
                    if ($director) {
                        TwwdaNotificationService::notify(
                            $director->id,
                            'Additional Board Paper Assignment - Director Notification',
                            "Your deputy has been assigned an additional agenda item \"{$assignment['agenda_item_title']}\" for meeting \"{$meeting->title}\".",
                            'director_assignment_notice',
                            "/workflow/meetings/{$meeting->id}/assignment-dashboard",
                            $meeting->id
                        );
                    }
                }

                $created[] = $record;
            }

            TwwdaAuditService::log(
                'officers_added',
                'Stage 4: Officer Assignment',
                TwwdaMeeting::class,
                $meeting->id,
                "Added " . count($assignments) . " additional officer(s) to meeting: {$meeting->title}",
                null,
                ['assignments' => collect($created)->pluck('id')->toArray()],
                $meeting->id
            );

            return $created;
        });
    }

    /**
     * Record a read receipt for an officer assignment.
     */
    public function markRead(TwwdaOfficerAssignment $assignment): void
    {
        $assignment->update([
            'system_read_at' => now(),
        ]);

        TwwdaAuditService::log(
            'assignment_read',
            'Stage 4: Officer Assignment',
            TwwdaOfficerAssignment::class,
            $assignment->id,
            "Officer read assignment for agenda item: {$assignment->agenda_item_title}",
            null,
            ['system_read_at' => now()->toIso8601String()],
            $assignment->meeting_id
        );
    }

    /**
     * Resolve the director for a given user based on their division's directorate.
     *
     * Chain: user → division → directorate → active director mapping
     *
     * If the user IS a director of one of the direct-paper directorates (1,2,3),
     * they are their own reviewer — return their own ID.
     */
    private function resolveDirector(int $userId): ?int
    {
        // Check if the user IS a director (via active mapping)
        $selfMapping = TwwdaUserDirectorateMapping::where('user_id', $userId)
            ->active()
            ->directors()
            ->first();

        if ($selfMapping) {
            // The user is a director — they are their own reviewer
            return $userId;
        }

        // 1. Get the user's division and its directorate
        $user = User::with('division')->find($userId);
        if (!$user || !$user->division || !$user->division->directorate_id) {
            return null;
        }

        $directorateId = $user->division->directorate_id;

        // 2. Find the active director for this directorate
        $directorMapping = TwwdaUserDirectorateMapping::where('directorate_id', $directorateId)
            ->active()
            ->directors()
            ->first();

        return $directorMapping ? $directorMapping->user_id : null;
    }
}
