<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\MeetingPreparationService;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaBoardPaperAnnexure;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaMeetingAgendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingPreparationController extends Controller
{
    protected MeetingPreparationService $preparationService;

    /**
     * Audit committee IDs — DDIA self-approves, no CEO needed.
     */
    protected array $auditCommitteeIds;

    public function __construct(MeetingPreparationService $preparationService)
    {
        $this->preparationService = $preparationService;
        $this->auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
    }

    // ═══════════════════════════════════════════════════════════════
    //  INDEX — Main preparation page with agenda seeding
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the meeting preparation page.
     * Seeds agenda items from the official notice on first visit.
     */
    public function index(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee', 'preparation']);

            // Seed agenda items from official notice (idempotent — runs only once)
            $this->preparationService->seedAgendaItemsFromNotice($meeting);

            // Reload preparation after potential seeding
            $meeting->load('preparation');
            $preparation = $meeting->preparation;

            $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);

            // Get agenda items by status
            $agendaItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->visibleToAdmin()
                ->ordered()
                ->with(['boardPaper.author', 'boardPaper.annexures', 'addedByUser'])
                ->get();

            $pulledDownItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->pulledDown()
                ->ordered()
                ->with(['boardPaper.author', 'pulldownRequestedByUser', 'pulldownApprovedByUser'])
                ->get();

            $pendingItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->pendingCeoApproval()
                ->ordered()
                ->with(['boardPaper.author', 'addedByUser'])
                ->get();

            $rejectedItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->where('status', 'rejected')
                ->ordered()
                ->with(['addedByUser'])
                ->get();

            // Available CEO-approved papers not yet assigned to an agenda item
            $unassignedPapers = TwwdaBoardPaper::where('meeting_id', $meeting->id)
                ->approved()
                ->whereDoesntHave('agendaItem')
                ->with(['author'])
                ->get();

            return view('workflow.preparations.index', compact(
                'meeting',
                'preparation',
                'agendaItems',
                'pulledDownItems',
                'pendingItems',
                'rejectedItems',
                'unassignedPapers',
                'isAudit'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Preparation index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load meeting preparation.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  REORDER — Drag-and-drop agenda items (AJAX)
    // ═══════════════════════════════════════════════════════════════

    public function reorder(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'order' => 'required|array|min:1',
            'order.*' => 'integer|exists:twwda_meeting_agenda_items,id',
        ]);

        try {
            $this->preparationService->reorderAgendaItems($meeting, $request->order);

            return response()->json([
                'success' => true,
                'message' => 'Agenda order updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('TWWDA Reorder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder agenda items.',
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADD ITEM — New agenda item with optional board paper upload
    // ═══════════════════════════════════════════════════════════════

    public function addItem(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'title'       => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'item_number' => 'nullable|string|max:50',
            'board_paper' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'annexures'   => 'nullable|array',
            'annexures.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:20480',
        ]);

        try {
            $boardPaperId = null;

            // If a board paper file was uploaded, create a TwwdaBoardPaper record
            if ($request->hasFile('board_paper')) {
                $paper = $this->createBoardPaperFromUpload($request, $meeting);
                $boardPaperId = $paper->id;

                // Handle annexures for this board paper
                if ($request->hasFile('annexures')) {
                    $this->uploadAnnexures($paper, $request->file('annexures'));
                }
            }

            $this->preparationService->addNewAgendaItem(
                $meeting,
                $request->only(['title', 'description', 'item_number']),
                $boardPaperId
            );

            $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);

            session()->flash('message', $isAudit
                ? 'Agenda item added and auto-approved.'
                : 'Agenda item added. It is now pending CEO approval.');
            session()->flash('alert-type', 'success');

            return redirect()->route('workflow.prepare.index', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Add Agenda Item error: ' . $e->getMessage());
            session()->flash('message', 'Failed to add agenda item: ' . $e->getMessage());
            session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  PULLDOWN — Request to hide an agenda item from board members
    // ═══════════════════════════════════════════════════════════════

    public function pulldown(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'item_id'       => 'required|integer|exists:twwda_meeting_agenda_items,id',
            'justification' => 'required|string|max:2000',
        ]);

        try {
            $item = TwwdaMeetingAgendaItem::where('id', $request->item_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            if (!$item->canBePulledDown()) {
                session()->flash('message', 'This agenda item cannot be pulled down in its current status.');
                session()->flash('alert-type', 'error');
                return back();
            }

            $this->preparationService->requestPulldown($item, $request->justification, $meeting);

            $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);

            session()->flash('message', $isAudit
                ? 'Agenda item pulled down successfully.'
                : 'Pulldown request sent to CEO for approval.');
            session()->flash('alert-type', 'success');

            return redirect()->route('workflow.prepare.index', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Pulldown error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process pulldown request.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  ASSIGN PAPER — Link a board paper to an agenda item (AJAX)
    // ═══════════════════════════════════════════════════════════════

    public function assignPaper(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'item_id'  => 'required|integer|exists:twwda_meeting_agenda_items,id',
            'paper_id' => 'required|integer|exists:twwda_board_papers,id',
        ]);

        try {
            $item = TwwdaMeetingAgendaItem::where('id', $request->item_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $this->preparationService->assignPaperToAgendaItem($item, $request->paper_id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Board paper assigned to agenda item.',
                ]);
            }

            session()->flash('message', 'Board paper assigned to agenda item.');
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.prepare.index', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Assign Paper error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to assign paper.'], 500);
            }

            session()->flash('message', 'Failed to assign paper.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  REMOVE PAPER — Legacy endpoint (kept for backwards compat)
    // ═══════════════════════════════════════════════════════════════

    public function removePaper(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'paper_id' => 'required|integer|exists:twwda_board_papers,id',
        ]);

        try {
            $this->preparationService->removePaper($meeting, $request->paper_id);

            session()->flash('message', 'Board paper removed from agenda.');
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.prepare.index', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Remove Paper error: ' . $e->getMessage());
            session()->flash('message', 'Failed to remove paper from agenda.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  REMOVE ITEM — Legacy endpoint (kept for backwards compat)
    // ═══════════════════════════════════════════════════════════════

    public function removeItem(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'item_index' => 'required|integer|min:0',
        ]);

        try {
            $this->preparationService->removeAgendaItem($meeting, $request->item_index);

            session()->flash('message', 'Agenda item removed successfully.');
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.prepare.index', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Remove Agenda Item error: ' . $e->getMessage());
            session()->flash('message', 'Failed to remove agenda item.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  FINALIZE — Lock the agenda
    // ═══════════════════════════════════════════════════════════════

    public function finalize(Request $request, TwwdaMeeting $meeting)
    {
        try {
            $this->preparationService->finalize($meeting);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Meeting preparations finalized. The meeting is ready to commence.',
                    'redirect' => route('workflow.meetings.show', $meeting),
                ]);
            }

            session()->flash('message', 'Meeting preparations finalized. The meeting is ready to commence.');
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Finalize error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            session()->flash('message', $e->getMessage());
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  CEO AGENDA ITEM REVIEW — Pending new items + pulldown requests
    // ═══════════════════════════════════════════════════════════════

    /**
     * CEO: List all pending agenda items across all meetings.
     */
    public function ceoAgendaPending()
    {
        $pendingItems = TwwdaMeetingAgendaItem::pendingCeoApproval()
            ->with(['meeting.boardCommittee', 'boardPaper', 'addedByUser', 'pulldownRequestedByUser'])
            ->latest()
            ->paginate(20);

        return view('workflow.preparations.ceo-pending', compact('pendingItems'));
    }

    /**
     * CEO: Review a single agenda item.
     */
    public function ceoAgendaReview(TwwdaMeetingAgendaItem $item)
    {
        $item->load([
            'meeting.boardCommittee',
            'boardPaper.author',
            'boardPaper.annexures',
            'addedByUser',
            'pulldownRequestedByUser',
        ]);

        return view('workflow.preparations.ceo-review', compact('item'));
    }

    /**
     * CEO: Approve or reject an agenda item / pulldown request.
     */
    public function ceoAgendaDecision(Request $request, TwwdaMeetingAgendaItem $item)
    {
        $request->validate([
            'decision'      => 'required|in:approve,reject',
            'reason'        => 'required_if:decision,reject|nullable|string|max:2000',
            'ceo_signature' => 'nullable|string', // base64 signature for board paper
        ]);

        try {
            if ($item->status === 'pending_approval') {
                // New agenda item approval/rejection
                if ($request->decision === 'approve') {
                    $this->preparationService->approveNewAgendaItem($item, $request->ceo_signature);
                    $msg = 'Agenda item approved.';
                } else {
                    $this->preparationService->rejectNewAgendaItem($item, $request->reason);
                    $msg = 'Agenda item rejected.';
                }
            } elseif ($item->status === 'pending_pulldown') {
                // Pulldown request approval/rejection
                if ($request->decision === 'approve') {
                    $this->preparationService->approvePulldown($item);
                    $msg = 'Pulldown request approved. Item is now hidden from board members.';
                } else {
                    $this->preparationService->rejectPulldown($item, $request->reason);
                    $msg = 'Pulldown request rejected. Item remains visible.';
                }
            } else {
                session()->flash('message', 'This item is not pending any approval.');
                session()->flash('alert-type', 'error');
                return redirect()->route('workflow.ceo.agenda-items');
            }

            session()->flash('message', $msg);
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.ceo.agenda-items');
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Agenda Decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process decision.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Create a board paper record from an uploaded file.
     */
    private function createBoardPaperFromUpload(Request $request, TwwdaMeeting $meeting): TwwdaBoardPaper
    {
        $file = $request->file('board_paper');
        $filePath = $file->store('twwda/board-papers', 'public');

        return TwwdaBoardPaper::create([
            'meeting_id'       => $meeting->id,
            'prepared_by'      => auth()->id(),
            'title'            => $request->title,
            'paper_type'       => 'supplementary',
            'file_path'        => $filePath,
            'status'           => 'draft', // will be promoted on CEO approval
            'committee_name'   => $meeting->boardCommittee->name ?? '',
        ]);
    }

    /**
     * Upload annexures for a board paper.
     */
    private function uploadAnnexures(TwwdaBoardPaper $paper, array $files): void
    {
        foreach ($files as $index => $file) {
            $filePath = $file->store('twwda/annexures', 'public');

            TwwdaBoardPaperAnnexure::create([
                'board_paper_id' => $paper->id,
                'title'          => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path'      => $filePath,
                'file_name'      => $file->getClientOriginalName(),
                'file_size'      => $file->getSize(),
                'file_type'      => $file->getClientMimeType(),
                'sort_order'     => $index + 1,
            ]);
        }
    }
}
