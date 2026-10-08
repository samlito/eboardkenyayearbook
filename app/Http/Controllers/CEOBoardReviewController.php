<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use App\Http\Services\BoardPaperService;
use App\Http\Services\CEOBoardMeetingService;
use App\Http\Services\CEOBoardReviewService;
use App\Mail\BoardPaperStatusUpdatedMail;
use App\Models\BoardPaper;
use App\Models\BoardPaperVersion;
use Illuminate\Http\Request;

use App\Models\BoardMeeting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class CEOBoardReviewController extends Controller
{
    //
    public function index()
    {
        $meetings = CEOBoardReviewService::getMeetingsReadyForReview();
        return view('backend.ceo.review_index', compact('meetings'));
    }

    public function show0($id, Request $request)
    {
        $meeting = CEOBoardReviewService::getMeetingWithPapers($id);

        // Audit viewing action
        AuditLogService::log($request,'VIEW', 'board_meetings', $meeting->id, null, ['action' => 'CEO viewed board papers']);

        return view('backend.ceo.review_show', compact('meeting'));
    }

    public function show($id, Request $request)
    {

        $meeting = CEOBoardReviewService::getMeetingWithPapers($id);
        $papers = $meeting->papers; // Add this line

        AuditLogService::log($request, 'VIEW', 'board_meetings', $meeting->id, null, ['action' => 'CEO viewed board papers']);
        $allApproved = $papers->every(fn($paper) => $paper->status === 'Approved');
        return view('backend.ceo.review_show', compact('meeting', 'papers','allApproved')); // Pass $papers to the view
    }



    public function approve(Request $request, $id)
    {
        $result = CEOBoardReviewService::approveBoardPapers($id);

        if ($result['status'] === 'success') {
            return redirect()->route('ceo.board.review.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }


    public function decision0(Request $request, $paperId)
    {
//        dd('This is the decision method');
        $request->validate([
            'status' => 'required|in:Approved,Needs Revision',
            'ceo_comments' => 'nullable|string|max:1000',
        ]);

        $paper = BoardPaper::with('user', 'meeting')->findOrFail($paperId);

        $oldValues = $paper->only(['status', 'ceo_comments']);

        $paper->status = $request->status;
        $paper->ceo_comments = $request->ceo_comments;
        $paper->save();

        // Save paper version for audit trail
        BoardPaperVersion::create([
            'board_paper_id' => $paper->id,
            'status' => $paper->status,
            'ceo_comments' => $paper->ceo_comments,
            'acted_by' => auth()->id(),
        ]);

        // Audit log
        AuditLogService::log($request, 'UPDATE', 'board_papers', $paper->id, $oldValues, [
            'new' => $paper->only(['status', 'ceo_comments']),
            'action' => 'CEO updated board paper status',
        ]);

        // Email the owner of the paper
        Mail::to($paper->user->email)->queue(new BoardPaperStatusUpdatedMail($paper));

        return back()->with('success', 'Decision submitted and logged.');
    }




    public function decision(Request $request, $paperId, BoardPaperService $boardPaperService)
    {
        $request->validate([
            'status' => 'required|in:Approved,Needs Revision',
            'ceo_comments' => 'nullable|string|max:1000',
        ]);

        $boardPaperService->handleCeoDecision(
            $paperId,
            $request->status,
            $request->ceo_comments,
            $request // for audit log
        );

        return back()->with('success', 'Decision submitted and logged.');
    }
    public function sendNotice($id, Request $request)
    {
//        dd('This is the sendNotice method');
        try {
            $meeting = CEOBoardMeetingService::sendNotice($id, $request);

            return back()->with('success', 'Notice sent successfully to board members.');
        } catch (\Exception $e) {
            Log::error("Error sending notice: " . $e->getMessage());
            return back()->with('error', 'Failed to send notice: ' . $e->getMessage());
        }
    }

}
