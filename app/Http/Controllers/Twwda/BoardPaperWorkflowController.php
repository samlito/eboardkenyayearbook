<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\BoardPaperWorkflowService;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaBoardPaperAnnexure;
use App\Models\Twwda\TwwdaMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BoardPaperWorkflowController extends Controller
{
    protected BoardPaperWorkflowService $paperService;

    public function __construct(BoardPaperWorkflowService $paperService)
    {
        $this->paperService = $paperService;
    }

    /**
     * Board paper creation form.
     */
    public function create(TwwdaMeeting $meeting, Request $request)
    {
        try {
            $meeting->load(['boardCommittee', 'officerAssignments.user']);

            $assignments = $meeting->officerAssignments()
                ->where('user_id', auth()->id())
                ->get();

            $selectedAssignmentId = $request->query('assignment_id');

            $assignment = null;
            if ($selectedAssignmentId) {
                $assignment = $assignments->firstWhere('id', (int) $selectedAssignmentId);
            } elseif ($assignments->count() === 1) {
                $assignment = $assignments->first();
            }

            $paper = null;

            // Pre-compute template defaults
            $committeeName    = $meeting->boardCommittee->name ?? '';
            $financialYear    = $this->currentFinancialYear();
            $reportingPeriod  = '';
            $authorDepartment = auth()->user()->name ?? '';

            // Section labels & guidance for the form
            $sectionLabels   = TwwdaBoardPaper::sectionLabels();
            $sectionGuidance = TwwdaBoardPaper::sectionGuidance();
            $requiredSections = TwwdaBoardPaper::requiredSections();

            // Default committee request text
            $defaultCommitteeRequest = "Management requests the {$committeeName} to:\nConsider and Recommend to the Board to approve/adopt/note ";

            return view('workflow.papers.create', compact(
                'meeting', 'assignments', 'paper', 'assignment', 'selectedAssignmentId',
                'committeeName', 'financialYear', 'reportingPeriod', 'authorDepartment',
                'sectionLabels', 'sectionGuidance', 'requiredSections', 'defaultCommitteeRequest'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper create error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board paper form.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Store board paper via BoardPaperWorkflowService.
     */
    public function store(Request $request, TwwdaMeeting $meeting)
    {
        $isCeoSigned = $request->is_ceo_signed && $request->hasFile('file');
        $sectionRule = $isCeoSigned ? 'nullable|string' : 'required|string';

        $request->validate([
            'title'                     => 'required|string|max:500',
            'agenda_item_number'        => 'required|string|max:50',
            'assignment_id'             => 'nullable|exists:twwda_officer_assignments,id',
            'committee_name'            => 'nullable|string|max:255',
            'paper_type'                => 'nullable|string|max:500',
            'financial_year'            => 'nullable|string|max:20',
            'reporting_period'          => 'nullable|string|max:255',
            'author_department'         => 'nullable|string|max:255',
            'section_background'        => $sectionRule,
            'section_executive_summary' => $sectionRule,
            'section_body'              => $sectionRule,
            'section_challenges'        => 'nullable|string',
            'section_board_items'       => $sectionRule,
            'section_committee_request' => $sectionRule,
            'file'                      => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'is_ceo_signed'             => 'nullable|boolean',
            'annexures'                 => 'nullable|array',
            'annexures.*.title'         => 'required_with:annexures.*.file|string|max:500',
            'annexures.*.file'          => 'required_with:annexures.*.title|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

        try {
            $paper = $this->paperService->store($request, $meeting);

            if ($request->is_ceo_signed && $request->hasFile('file') && auth()->user()->hasAnyRole(['Corporation Secretary', 'DD Internal Audit'])) {
                $paper->update([
                    'is_auto_approved' => true,
                    'status'           => 'ceo_approved',
                    'ceo_approved_at'  => now(),
                ]);
            }

            // Process annexures if any were uploaded
            if ($request->has('annexures')) {
                foreach ($request->annexures as $annexureData) {
                    if (isset($annexureData['file']) && $annexureData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $this->paperService->addAnnexure(
                            $paper,
                            $annexureData['file'],
                            $annexureData['title'] ?? 'Annexure'
                        );
                    }
                }
            }

            session()->flash('message', 'Board paper created successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper store error: ' . $e->getMessage());
            session()->flash('message', 'Failed to create board paper.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * View a board paper with all details.
     */
    public function show(TwwdaBoardPaper $paper)
    {
        try {
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

            return view('workflow.papers.show', compact('paper'));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper show error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board paper.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Edit form for a board paper.
     */
    public function edit(TwwdaBoardPaper $paper)
    {
        try {
            // Only the paper's author can edit, and only in draft or rejected status
            $editableStatuses = ['draft', 'rejected_by_director', 'rejected_by_ceo'];
            if (!in_array($paper->status, $editableStatuses) || (int) $paper->prepared_by !== (int) auth()->id()) {
                session()->flash('message', 'You are not authorized to edit this board paper.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.papers.show', $paper);
            }

            $paper->load(['meeting.boardCommittee', 'assignment', 'annexures', 'comments.user']);

            $meeting = $paper->meeting;
            $assignment = $paper->assignment;
            $assignments = $meeting->officerAssignments()->where('user_id', auth()->id())->get();

            // If the author is a director who was self-assigned, ensure their assignment is included
            if ($assignment && !$assignments->contains('id', $assignment->id)) {
                $assignments = $assignments->push($assignment);
            }

            $selectedAssignmentId = $assignment->id ?? null;

            // Pre-fill defaults from paper
            $committeeName    = $paper->committee_name ?? $meeting->boardCommittee->name ?? '';
            $financialYear    = $paper->financial_year ?? $this->currentFinancialYear();
            $reportingPeriod  = $paper->reporting_period ?? '';
            $authorDepartment = $paper->author_department ?? auth()->user()->name ?? '';

            $sectionLabels    = TwwdaBoardPaper::sectionLabels();
            $sectionGuidance  = TwwdaBoardPaper::sectionGuidance();
            $requiredSections = TwwdaBoardPaper::requiredSections();
            $defaultCommitteeRequest = '';

            // Flag to show rejection feedback in the edit form
            $rejectionComment = null;
            if (in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo'])) {
                $rejectionComment = $paper->comments
                    ->whereIn('action', ['rejected', 'reject'])
                    ->sortByDesc('created_at')
                    ->first();
            }

            return view('workflow.papers.create', compact(
                'meeting', 'assignments', 'paper', 'assignment', 'selectedAssignmentId',
                'committeeName', 'financialYear', 'reportingPeriod', 'authorDepartment',
                'sectionLabels', 'sectionGuidance', 'requiredSections', 'defaultCommitteeRequest',
                'rejectionComment'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper edit error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load edit form.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.papers.show', $paper);
        }
    }

    /**
     * Update a board paper.
     */
    public function update(Request $request, TwwdaBoardPaper $paper)
    {
        $request->validate([
            'title'                     => 'sometimes|string|max:500',
            'agenda_item_number'        => 'sometimes|string|max:50',
            'committee_name'            => 'nullable|string|max:255',
            'paper_type'                => 'nullable|string|max:500',
            'financial_year'            => 'nullable|string|max:20',
            'reporting_period'          => 'nullable|string|max:255',
            'author_department'         => 'nullable|string|max:255',
            'section_background'        => 'sometimes|string',
            'section_executive_summary' => 'sometimes|string',
            'section_body'              => 'sometimes|string',
            'section_challenges'        => 'nullable|string',
            'section_board_items'       => 'sometimes|string',
            'section_committee_request' => 'sometimes|string',
            'file'                      => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'annexures'                 => 'nullable|array',
            'annexures.*.title'         => 'required_with:annexures.*.file|string|max:500',
            'annexures.*.file'          => 'required_with:annexures.*.title|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

        try {
            $this->paperService->update($request, $paper);

            // Process new annexures if any were uploaded
            if ($request->has('annexures')) {
                foreach ($request->annexures as $annexureData) {
                    if (isset($annexureData['file']) && $annexureData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $this->paperService->addAnnexure(
                            $paper,
                            $annexureData['file'],
                            $annexureData['title'] ?? 'Annexure'
                        );
                    }
                }
            }

            session()->flash('message', 'Board paper updated successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper update error: ' . $e->getMessage());
            session()->flash('message', 'Failed to update board paper.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Submit board paper for review.
     */
    public function submit(TwwdaBoardPaper $paper)
    {
        try {
            // Only allow submission from draft or rejected statuses, by the paper's author
            $submittableStatuses = ['draft', 'rejected_by_director', 'rejected_by_ceo'];
            if (!in_array($paper->status, $submittableStatuses) || (int) $paper->prepared_by !== (int) auth()->id()) {
                session()->flash('message', 'You are not authorized to submit this board paper.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.papers.show', $paper);
            }

            $this->paperService->submit($paper);

            session()->flash('message', 'Board paper submitted for review.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper submit error: ' . $e->getMessage());
            session()->flash('message', 'Failed to submit board paper.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Author recalls a submitted board paper.
     */
    public function recall(TwwdaBoardPaper $paper)
    {
        try {
            if ((int) $paper->prepared_by !== (int) auth()->id()) {
                session()->flash('message', 'You are not authorized to recall this board paper.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.papers.show', $paper);
            }

            $canRecallFromDirector = $paper->status === 'submitted';
            $canRecallFromCeo = in_array($paper->status, ['director_approved', 'ceo_review']) && $paper->is_auto_approved;

            if (!$canRecallFromDirector && !$canRecallFromCeo) {
                session()->flash('message', 'This board paper cannot be recalled at its current stage.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.papers.show', $paper);
            }

            $this->paperService->authorRecall($paper);

            session()->flash('message', 'Board paper recalled successfully. You can now edit and resubmit it.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Paper recall error: ' . $e->getMessage());
            session()->flash('message', 'Failed to recall board paper.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Upload an annexure to a board paper.
     */
    public function storeAnnexure(Request $request, TwwdaBoardPaper $paper)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png|max:20480',
        ]);

        try {
            $this->paperService->addAnnexure($paper, $request->file('file'), $request->title);

            session()->flash('message', 'Annexure uploaded successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Annexure store error: ' . $e->getMessage());
            session()->flash('message', 'Failed to upload annexure.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Remove an annexure from a board paper.
     */
    public function destroyAnnexure(TwwdaBoardPaper $paper, TwwdaBoardPaperAnnexure $annexure)
    {
        try {
            $this->paperService->removeAnnexure($annexure);

            session()->flash('message', 'Annexure removed successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.papers.show', $paper);
        } catch (\Exception $e) {
            Log::error('TWWDA Annexure destroy error: ' . $e->getMessage());
            session()->flash('message', 'Failed to remove annexure.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Compute current Kenya financial year (July-June).
     */
    protected function currentFinancialYear(): string
    {
        $now   = now();
        $month = $now->month;
        $year  = $now->year;

        if ($month >= 7) {
            return $year . '/' . ($year + 1);
        }
        return ($year - 1) . '/' . $year;
    }
}
