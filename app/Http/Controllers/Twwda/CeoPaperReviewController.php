<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\CeoPaperReviewService;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaStickyNote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CeoPaperReviewController extends Controller
{
    protected CeoPaperReviewService $reviewService;

    public function __construct(CeoPaperReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * List meetings with papers pending CEO/DDIA review.
     * DDIA only sees audit committee (board_committee_id=4) meetings.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $isDDIA = $user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']);
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();

            // Get meetings that have papers pending review
            $meetingIds = TwwdaBoardPaper::pendingCeoReview()
                ->pluck('meeting_id')
                ->unique();

            $query = TwwdaMeeting::whereIn('id', $meetingIds)
                ->with(['boardCommittee'])
                ->withCount(['boardPapers as pending_papers_count' => function ($q) {
                    $q->pendingCeoReview();
                }])
                ->orderBy('meeting_date');

            // DDIA only sees audit committee meetings
            if ($isDDIA) {
                $query->whereIn('board_committee_id', $auditCommitteeIds);
            }

            $meetings = $query->paginate(15);

            return view('workflow.papers.ceo-pending', compact('meetings', 'isDDIA'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Paper Review index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load meetings with pending papers.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Show all papers for a meeting pending CEO/DDIA review.
     */
    public function show(TwwdaMeeting $meeting)
    {
        try {
            $user = auth()->user();
            $isDDIA = $user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']);
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();

            // DDIA can only view audit committee meetings
            if ($isDDIA && !in_array($meeting->board_committee_id, $auditCommitteeIds)) {
                session()->flash('message', 'You can only review papers for Audit Committee meetings.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.ceo.papers');
            }

            $meeting->load(['boardCommittee']);

            $papers = TwwdaBoardPaper::where('meeting_id', $meeting->id)
                ->pendingCeoReview()
                ->with(['author', 'assignment.user', 'assignment.director', 'directorApprover'])
                ->orderBy('display_order')
                ->get();

            // Also get already reviewed papers for reference
            $reviewedPapers = TwwdaBoardPaper::where('meeting_id', $meeting->id)
                ->whereIn('status', ['ceo_approved', 'rejected_by_ceo'])
                ->with(['author'])
                ->orderBy('display_order')
                ->get();

            $isAuditCommittee = in_array($meeting->board_committee_id, $auditCommitteeIds);

            return view('workflow.papers.ceo-meeting', compact('meeting', 'papers', 'reviewedPapers', 'isDDIA', 'isAuditCommittee'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Paper Meeting show error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load meeting papers.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.papers');
        }
    }

    /**
     * CEO/DDIA review page - PDF viewer with sticky notes.
     */
    public function review(TwwdaBoardPaper $paper)
    {
        try {
            $user = auth()->user();
            $isDDIA = $user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']);

            $paper->load([
                'meeting.boardCommittee',
                'assignment.user',
                'assignment.director',
                'author',
                'directorApprover',
                'annexures',
                'comments.user',
                'stickyNotes.user',
            ]);

            // DDIA can only review audit committee papers
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            if ($isDDIA && $paper->meeting && !in_array($paper->meeting->board_committee_id, $auditCommitteeIds)) {
                session()->flash('message', 'You can only review papers for Audit Committee meetings.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.ceo.papers');
            }

            $isAuditCommittee = $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds);

            return view('workflow.papers.ceo-review', compact('paper', 'isDDIA', 'isAuditCommittee'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Review error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load paper for review.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.papers');
        }
    }

    /**
     * CEO/DDIA approves or rejects a board paper.
     */
    public function decision(Request $request, TwwdaBoardPaper $paper)
    {
        $user = auth()->user();
        $isDDIA = $user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']);
        $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
        $isAuditCommittee = $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds);

        // DDIA can only decide on audit committee papers
        if ($isDDIA && !$isAuditCommittee) {
            session()->flash('message', 'You can only approve/reject Audit Committee papers.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.papers');
        }

        // Signature is required for CEO approval but optional for DDIA
        $signatureRule = ($isDDIA && $isAuditCommittee)
            ? 'nullable|string'
            : 'required_if:decision,approve|nullable|string';

        $request->validate([
            'decision' => 'required|in:approve,reject',
            'comment' => 'required_if:decision,reject|nullable|string|max:2000',
            'ceo_signature' => $signatureRule,
        ]);

        try {
            if ($request->decision === 'approve') {
                $this->reviewService->approve($paper, $request->input('ceo_signature'));
                session()->flash('message', 'Board paper approved successfully.'); session()->flash('alert-type', 'success');
            } else {
                $this->reviewService->reject($paper, $request->comment);
                session()->flash('message', 'Board paper rejected. Relevant parties have been notified.'); session()->flash('alert-type', 'success');
            }

            return redirect()->route('workflow.ceo.papers.show', $paper->meeting_id);
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process decision.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Add a sticky note to a board paper (AJAX).
     */
    public function addStickyNote(Request $request, TwwdaBoardPaper $paper)
    {
        $request->validate([
            'page_number' => 'nullable|integer|min:1',
            'x_position' => 'nullable|numeric|min:0|max:1',
            'y_position' => 'nullable|numeric|min:0|max:1',
            'content' => 'required|string|max:1000',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $note = $this->reviewService->addStickyNote($paper, $request->only([
                'page_number', 'x_position', 'y_position', 'content', 'color',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Sticky note added.',
                'note' => $note->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('TWWDA Add Sticky Note error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add sticky note.',
            ], 500);
        }
    }

    /**
     * Download approved board paper as PDF with CEO signature.
     */
    public function downloadPdf(TwwdaBoardPaper $paper)
    {
        try {
            $paper->load(['meeting.boardCommittee', 'author', 'assignment.user', 'annexures']);

            if (!in_array($paper->status, ['ceo_approved', 'approved'])) {
                session()->flash('message', 'Only approved papers can be downloaded as PDF.');
                session()->flash('alert-type', 'error');
                return back();
            }

            $pdf = Pdf::loadView('workflow.papers.pdf-approved', compact('paper'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'sans-serif',
                ]);

            $filename = str_replace(['/', ' '], ['-', '_'], $paper->paper_number) . '_APPROVED.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('TWWDA PDF Download error: ' . $e->getMessage());
            session()->flash('message', 'Failed to generate PDF.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Remove a sticky note (AJAX).
     */
    public function removeStickyNote(TwwdaStickyNote $note)
    {
        try {
            $this->reviewService->removeStickyNote($note);

            return response()->json([
                'success' => true,
                'message' => 'Sticky note removed.',
            ]);
        } catch (\Exception $e) {
            Log::error('TWWDA Remove Sticky Note error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove sticky note.',
            ], 500);
        }
    }
}
