<?php

namespace App\Http\Services;
use App\Mail\BoardMeetingNoticeMail;
use App\Models\BoardCommitteeMember;
use App\Models\BoardMeeting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class CEOBoardMeetingService
{
    public static function sendNotice($meetingId, Request $request)
    {
        $meeting = BoardMeeting::with('committee')->findOrFail($meetingId);

        if (!$meeting->board_committee_id) {
            throw new \Exception('Meeting not linked to any board committee.');
        }

        // Get board members via committee
        $memberUserIds = BoardCommitteeMember::where('board_committee_id', $meeting->board_committee_id)
            ->pluck('board_member_id');

//        $users = DB::table('board_members')
//            ->whereIn('id', $memberUserIds)
//            ->join('users', 'board_members.user_id', '=', 'users.id')
//            ->select('users.email', 'users.name')
//            ->get();
//        $users = DB::table('board_members')
//            ->whereIn('board_members.id', $memberUserIds)
//            ->join('users', 'board_members.user_id', '=', 'users.id')
//            ->select('users.email', 'users.name')
//            ->get();
        $users = DB::table('board_members')
            ->whereIn('id', $memberUserIds)
            ->select('email', 'names')
            ->get();


        if ($users->isEmpty()) {
            throw new \Exception('No board members found for the selected committee.');
        }

        // Send email to each member
        foreach ($users as $user) {
            Mail::to($user->email)->queue(new BoardMeetingNoticeMail($meeting, $user));
        }

        // Update meeting status
        $meeting->ceo_notified_at = now();
        $meeting->save();

        // Audit log
        AuditLogService::log($request, 'UPDATE', 'board_meetings', $meeting->id, null, [
            'action' => 'CEO sent meeting notice to board/committee members',
        ]);

        return $meeting;
    }
}
