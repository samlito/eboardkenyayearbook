<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\BoardMeetingService;
use App\Http\Services\BoardNoticeService;
use App\Models\BoardMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingsController extends Controller
{
    //

    public function index()
    {
        $meetings = BoardMeetingService::upcomingMeetingsForLoggedInMember();
        return view('board.meetings.index', compact('meetings'));
    }

    public function show($meetingId)
    {
        // Authorize: the logged-in member must belong to the meeting's committee
        $userId = Auth::id();

        $meeting = BoardMeeting::where('id', $meetingId)
            ->whereHas('boardCommittee.boardMembers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['committee', 'boardPapers.annexures', 'boardPapers.user'])
            ->firstOrFail();

        return view('board.meetings.show', compact('meeting'));
    }
}
