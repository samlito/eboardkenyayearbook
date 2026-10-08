<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\AuditLogService;
use App\Http\Services\BoardMeetingService;
use App\Models\BoardMeeting;
use App\Models\BoardMember;
use Illuminate\Http\Request;

class BoardMemberMeetingController extends Controller
{
    //
    public function index(Request $request)
    {
        $meetings = BoardMeetingService::upcomingMeetingsForLoggedInMember();

        AuditLogService::log($request, 'VIEW', 'board_meetings', null, null, [
            'action' => 'Viewed list of upcoming meetings',
        ]);

        return view('board.meetings.index', compact('meetings'));
    }

    public function show(BoardMeeting $meeting, Request $request)
    {
        // Check that user belongs to this meeting's committee
        if (!$meeting->committee || !$meeting->committee->boardMembers->contains('user_id', auth()->id())) {
            abort(403, 'Unauthorized');
        }

        AuditLogService::log($request, 'VIEW', 'board_meetings', $meeting->id, null, [
            'action' => 'Viewed meeting details',
        ]);

        return view('board.meetings.show', compact('meeting'));
    }

    public function active()
    {
        $meetings = BoardMeetingService::upcomingMeetingsForLoggedInMember();



        return view('board.meetings.active', compact('meetings'));
    }
}
