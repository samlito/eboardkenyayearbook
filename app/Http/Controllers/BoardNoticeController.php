<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use App\Http\Services\BoardNoticeService;
use App\Models\BoardMeeting;
use App\Models\BoardMeetingNotice;
use App\Models\BoardMeetingNotice10Day;
use App\Models\BoardNotice;
use Illuminate\Http\Request;

class BoardNoticeController extends Controller
{
    //

    public function index(Request $request)
    {

        $notices = BoardNoticeService::fetchForLoggedInMember();
        AuditLogService::log($request, 'VIEW', 'board_notices', null, null, [
            'action' => 'Viewed list of board meeting notices'
        ]);

        return view('board.notices.index', compact('notices'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'meeting_id' => 'required|exists:board_meetings,id',
            'contentb' => 'required|string',
            'reference_number'  => 'required|string|max:255',
            'reference_date'    => 'required|date',
        ]);

        // Save the notice
        BoardNotice::create([
            'meeting_id' => $request->meeting_id,
            'content' => $request->contentb,
            'status' => 'Approved',
            'reference_number' => $request->reference_number,
            'reference_date'   => $request->reference_date,
        ]);

        // Optionally update the meeting status
        BoardMeeting::where('id', $request->meeting_id)->update(['status' => 'Initiated','title'=>$request->title]);

        return redirect()->route('board.meetings.all')->with('success', 'Notice submitted to CEO successfully.');
    }

    public function pending()
    {
        $notices = BoardNotice::where('status', 'Pending')->with('meeting')->get();
        return view('backend.ceo.notices.pending', compact('notices'));
    }

    public function review($id)
    {
        $notice = BoardNotice::with('meeting')->findOrFail($id);
        return view('backend.ceo.notices.review', compact('notice'));
    }

    public function decision(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:Approved,Rejected',
            'ceo_comments' => 'nullable|string',
        ]);

        $notice = BoardNotice::findOrFail($id);
        $notice->update([
            'status' => $request->action,
            'ceo_comments' => $request->ceo_comments,
        ]);
// ✅ Log CEO decision (no need for old values)
      AuditLogService::log(
            $request,
            'UPDATE',
            'board_notices',
            $notice->id,
            null,
            [
                'status'       => $request->action,
                'ceo_comments' => $request->ceo_comments,
            ]
        );
        return redirect()->route('ceo.notices.pending')->with('success', 'Notice updated successfully.');
    }
// BoardNoticeController.php
    public function show10Day(BoardMeetingNotice10Day $notice)
    {
        $meeting = $notice->meeting; // relationship: BoardMeetingNotice10Day belongsTo BoardMeeting

        return view('backend.actual_meetings.partials.preview_notice_modal_10', compact('meeting'));
    }
    public function show($id, Request $request)
    {

        $notice = BoardNoticeService::fetchByIdForLoggedInMember($id);
        AuditLogService::log($request, 'VIEW', 'board_notices', $id, null, [
            'action' => 'Viewed details of board meeting notice'
        ]);

        return view('board.notices.show', compact('notice'));
    }

    public function show3Day(BoardMeetingNotice $notice)
    {
        $meeting = $notice->meeting; // relationship: BoardMeetingNotice10Day belongsTo BoardMeeting

        return view('backend.actual_meetings.partials.preview_notice_modal', compact('meeting'));
    }
}
