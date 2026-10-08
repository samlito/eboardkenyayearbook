<?php

namespace App\Http\Services;

use App\Models\BoardMeeting;
use App\Models\User;

class CEOBoardReviewService
{
//    public static function getMeetingsReadyForReview()
//    {
//        return BoardMeeting::whereHas('assignments')
//            ->with('assignments', 'papers', 'notice')
//            ->whereHas('papers')
//            ->latest()
//            ->get();
//    }
    public static function getMeetingsReadyForReview()
    {
        return BoardMeeting::whereHas('assignments')
            ->whereHas('papers')
//            ->where('board_committee_id', '!=', 4)
            ->with(['assignments.user', 'papers.user', 'notice'])
            ->latest()
            ->get();
    }

    public static function getMeetingWithPapers($id)
    {
        return BoardMeeting::with(['papers.user', 'assignments', 'notice', 'papers.annexures'])
            ->findOrFail($id);
    }

    public static function approveBoardPapers($meetingId)
    {
        $meeting = self::getMeetingWithPapers($meetingId);

        if (!$meeting) {
            return ['status' => 'error', 'message' => 'Meeting not found'];
        }

        // Notify board members
        $members = User::role('Board Member')->get();
        foreach ($members as $member) {
            $member->notify(new NotifyBoardMembersNotification($meeting));
        }

        // Log the action
        AuditLogService::log(
            'UPDATE',
            'board_meetings',
            $meeting->id,
            null,
            ['action' => 'CEO approved board papers and notified board members']
        );

        return ['status' => 'success', 'message' => 'Board members notified successfully.'];
    }
}
