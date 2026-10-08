<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\BoardMeeting;
use App\Models\BoardMember;
use App\Models\ConflictOfInterestDeclaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActualBoardMeetingController extends Controller
{
    //

    public function conflictForm($id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        return view('board.meetings.conflict_form', compact('meeting'));
    }

    public function submitConflict0(Request $request, $id)
    {
        $request->validate([
            'conflict_status' => 'required|in:Yes,No',
            'details' => 'nullable|string'
        ]);

        ConflictRegister::updateOrCreate(
            ['meeting_id' => $id, 'board_member_id' => auth()->user()->boardMember->id],
            ['conflict_status' => $request->conflict_status, 'details' => $request->details]
        );

        return back()->with('success', 'Conflict of interest recorded.');
    }

    public function submitConflict(Request $request, $meetingId)
    {
        $request->validate([
            'conflict_status' => 'required|in:Yes,No',
            'details' => 'nullable|string|max:1000',
        ]);

        $userEmail = auth()->user()->email;
        $member = BoardMember::where('email', $userEmail)->firstOrFail();

       ConflictOfInterestDeclaration::updateOrCreate(
            ['meeting_id' => $meetingId, 'board_member_id' => $member->id],
            ['conflict_status' => $request->conflict_status, 'details' => $request->details]
        );

        return redirect()->route('meetings.active')->with('success', 'Conflict of Interest declaration submitted successfully.');
    }

    public function startMeeting($id)
    {
        $meeting = BoardMeeting::with(['boardPapers', 'officialNotice'])->findOrFail($id);
        return view('board.meetings.start', compact('meeting'));
    }


    public function viewMeeting($id)
    {
        $user = Auth::user();

        $meeting = BoardMeeting::with([
            'committee.members',
            'officialNotice',
            'boardPapers' => function ($q) {
                $q->orderBy('id');
            }
        ])->findOrFail($id);

        // Check if the logged-in user belongs to the committee of this meeting
        $isMember = $meeting->committee?->members()->where('user_id', $user->id)->exists();

        if (!$isMember) {
            abort(403, 'Unauthorized access to this meeting.');
        }

        // Sort board papers based on agenda order if defined
        $orderedPapers = collect();
        if ($meeting->officialNotice && is_array($meeting->officialNotice->agenda_order)) {
            foreach ($meeting->officialNotice->agenda_order as $paperId) {
                $paper = $meeting->boardPapers->firstWhere('id', $paperId);
                if ($paper) {
                    $orderedPapers->push($paper);
                }
            }
        } else {
            $orderedPapers = $meeting->boardPapers;
        }

        return view('board.meetings.view', compact('meeting', 'orderedPapers'));
    }
}
