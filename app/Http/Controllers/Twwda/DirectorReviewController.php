<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\DirectorReviewService;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaOfficerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DirectorReviewController extends Controller
{
    protected DirectorReviewService $reviewService;

    public function __construct(DirectorReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * List board papers pending director review.
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Get assignments where the current user is the director
            $assignmentIds = TwwdaOfficerAssignment::where('director_id', $userId)
                ->pluck('id');

            // Get papers linked to those assignments that are pending director review
            $papers = TwwdaBoardPaper::whereIn('assignment_id', $assignmentIds)
                ->pendingDirectorReview()
                ->with(['meeting.boardCommittee', 'author', 'assignment.user', 'annexures', 'comments.user'])
                ->orderBy('submitted_at', 'desc')
                ->paginate(15);

            // Papers this director approved that are still with CEO (recallable)
            $recallablePapers = TwwdaBoardPaper::where('director_approved_by', $userId)
                ->whereIn('status', ['director_approved', 'ceo_review'])
                ->where('is_auto_approved', false)
                ->with(['meeting.boardCommittee', 'author'])
                ->orderBy('director_approved_at', 'desc')
                ->get();

            return view('workflow.papers.director-review', compact('papers', 'recallablePapers'));
        } catch (\Exception $e) {
            Log::error('TWWDA Director Review index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load papers for review.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Show a specific board paper for director review.
     */
    public function show(TwwdaBoardPaper $paper)
    {
        try {
            $paper->load([
                'meeting.boardCommittee',
                'assignment.user',
                'assignment.director',
                'author',
                'annexures',
                'comments.user',
            ]);

            return view('workflow.papers.show', compact('paper'));
        } catch (\Exception $e) {
            Log::error('TWWDA Director Review show error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board paper.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.director.papers');
        }
    }

    /**
     * Director recalls a board paper from CEO review.
     */
    public function recall(TwwdaBoardPaper $paper)
    {
        try {
            $userId = Auth::id();

            // Only the director who approved can recall
            if ((int) $paper->director_approved_by !== (int) $userId) {
                session()->flash('message', 'You are not authorized to recall this board paper.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.director.papers');
            }

            if (!in_array($paper->status, ['director_approved', 'ceo_review'])) {
                session()->flash('message', 'This board paper cannot be recalled at its current stage.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.director.papers');
            }

            $this->reviewService->recall($paper);

            session()->flash('message', 'Board paper recalled from CEO review. It is now back under your review.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.director.papers');
        } catch (\Exception $e) {
            Log::error('TWWDA Director Recall error: ' . $e->getMessage());
            session()->flash('message', 'Failed to recall board paper.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Director approves or rejects the board paper.
     */
    public function decision(Request $request, TwwdaBoardPaper $paper)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'comment' => 'required_if:decision,reject|nullable|string|max:2000',
        ]);

        try {
            if ($request->decision === 'approve') {
                $this->reviewService->approve($paper, $request->comment);
                session()->flash('message', 'Board paper approved and forwarded to CEO for review.'); session()->flash('alert-type', 'success');
            } else {
                $this->reviewService->reject($paper, $request->comment);
                session()->flash('message', 'Board paper rejected. The author has been notified to revise.'); session()->flash('alert-type', 'success');
            }

            return redirect()->route('workflow.director.papers');
        } catch (\Exception $e) {
            Log::error('TWWDA Director Decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process decision.'); session()->flash('alert-type', 'error');
            return back();
        }
    }
}
