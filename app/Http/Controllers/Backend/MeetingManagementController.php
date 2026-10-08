<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BoardMeeting;
use Illuminate\Http\Request;

class MeetingManagementController extends Controller
{
    public function index()
    {
        $meetings = BoardMeeting::whereDate('date', '>=', now())
            ->orderBy('date')
            ->get();

        return view('backend.manage_meetings.index', compact('meetings'));
    }

    public function publishForm($id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        return view('corp_secretary.manage_meetings.publish', compact('meeting'));
    }

    public function publish(Request $request, $id)
    {
        $request->validate([
            'notice' => 'required|string',
            'agenda' => 'required|string',
        ]);

        $meeting = BoardMeeting::findOrFail($id);
        $meeting->update([
            'notice' => $request->notice,
            'agenda' => $request->agenda,
            'board_notified_at' => now(),
        ]);

        // Optionally trigger notification here...

        return redirect()->route('meetings.manage.index')->with('success', 'Meeting notice published successfully.');
    }

    public function sendReminder($id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        $members = $meeting->committee->boardMembers ?? [];

        foreach ($members as $member) {
            // Dispatch reminder job
            dispatch(new SendMeetingReminderJob($meeting, $member->user));
        }

        return back()->with('success', 'Reminders sent successfully.');
    }
}

