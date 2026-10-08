<?php

namespace App\Http\Services\Twwda;

use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaBoardPaperAnnexure;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BoardPaperWorkflowService
{
    /**
     * Assemble the structured JSON content from section_* request fields.
     */
    protected function assembleContent(Request $request): string
    {
        $sections = [];
        foreach (array_keys(TwwdaBoardPaper::sectionLabels()) as $key) {
            $value = $request->input("section_{$key}", '');
            // Normalize Quill empty states
            if ($value === '<p><br></p>' || trim(strip_tags($value)) === '') {
                $value = '';
            }
            $sections[$key] = $value;
        }
        return json_encode($sections);
    }

    /**
     * Create a new board paper for a meeting.
     */
    public function store(Request $request, TwwdaMeeting $meeting): TwwdaBoardPaper
    {
        return DB::transaction(function () use ($request, $meeting) {
            // Generate paper number
            $count = TwwdaBoardPaper::where('meeting_id', $meeting->id)->count() + 1;
            $paperNumber = $meeting->reference_number . '/BP/' . str_pad($count, 2, '0', STR_PAD_LEFT);

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store(
                    'twwda/board-papers/' . $meeting->id,
                    'public'
                );
            }

            $paper = TwwdaBoardPaper::create([
                'meeting_id'         => $meeting->id,
                'assignment_id'      => $request->assignment_id,
                'prepared_by'        => Auth::id(),
                'agenda_item_number' => $request->agenda_item_number,
                'paper_number'       => $paperNumber,
                'title'              => $request->title,
                'committee_name'     => $request->input('committee_name', $meeting->boardCommittee->name ?? ''),
                'paper_type'         => $request->input('paper_type', ''),
                'financial_year'     => $request->input('financial_year', ''),
                'reporting_period'   => $request->input('reporting_period', ''),
                'author_department'  => $request->input('author_department', ''),
                'content'            => $this->assembleContent($request),
                'file_path'          => $filePath,
                'status'             => 'draft',
                'version'            => 1,
                'display_order'      => $count,
            ]);

            // Update meeting stage if needed
            if ($meeting->current_stage < 5) {
                $meeting->update([
                    'status' => 'papers_in_progress',
                    'current_stage' => 5,
                ]);
            }

            TwwdaAuditService::log(
                'board_paper_created',
                'Stage 5: Board Paper Creation',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper created: {$paper->title}",
                null,
                $paper->toArray(),
                $meeting->id
            );

            return $paper;
        });
    }

    /**
     * Update an existing board paper.
     */
    public function update(Request $request, TwwdaBoardPaper $paper): TwwdaBoardPaper
    {
        return DB::transaction(function () use ($request, $paper) {
            $oldValues = $paper->only(['title', 'content', 'file_path', 'agenda_item_number', 'committee_name', 'paper_type', 'financial_year', 'reporting_period', 'author_department']);

            $data = [
                'title'              => $request->title ?? $paper->title,
                'agenda_item_number' => $request->agenda_item_number ?? $paper->agenda_item_number,
                'committee_name'     => $request->input('committee_name', $paper->committee_name),
                'paper_type'         => $request->input('paper_type', $paper->paper_type),
                'financial_year'     => $request->input('financial_year', $paper->financial_year),
                'reporting_period'   => $request->input('reporting_period', $paper->reporting_period),
                'author_department'  => $request->input('author_department', $paper->author_department),
                'version'            => $paper->version + 1,
            ];

            // Assemble structured content from section fields if present
            if ($request->has('section_background') || $request->has('section_body')) {
                $data['content'] = $this->assembleContent($request);
            }

            if ($request->hasFile('file')) {
                if ($paper->file_path) {
                    Storage::disk('public')->delete($paper->file_path);
                }
                $data['file_path'] = $request->file('file')->store(
                    'twwda/board-papers/' . $paper->meeting_id,
                    'public'
                );
            }

            // If paper was rejected, set status back to draft
            if (in_array($paper->status, ['rejected_by_director', 'rejected_by_ceo', 'director_rejected', 'ceo_rejected'])) {
                $data['status'] = 'draft';
            }

            $paper->update($data);

            TwwdaAuditService::log(
                'board_paper_updated',
                'Stage 5: Board Paper Creation',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper updated: {$paper->title} (version {$paper->version})",
                $oldValues,
                $paper->only(['title', 'content', 'file_path', 'agenda_item_number']),
                $paper->meeting_id
            );

            return $paper;
        });
    }

    /**
     * Submit a board paper for review.
     *
     * Routing logic:
     * 1. If the paper author IS the director (director_id === prepared_by),
     *    auto-approve and send directly to CEO.
     * 2. If assignment has a different director, send to that director for review.
     * 3. If no director is assigned, auto-approve and send directly to CEO.
     */
    public function submit(TwwdaBoardPaper $paper): void
    {
        DB::transaction(function () use ($paper) {
            $assignment = $paper->assignment;
            $hasDirector = $assignment && $assignment->director_id;

            // Check if the author IS the director (directors from directorates 1,2,3)
            $authorIsDirector = $hasDirector
                && (int) $assignment->director_id === (int) $paper->prepared_by;

            if ($hasDirector && !$authorIsDirector) {
                // Normal flow: send to director for review
                $paper->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                $director = User::find($assignment->director_id);
                if ($director) {
                    TwwdaNotificationService::notify(
                        $director->id,
                        'Board Paper Submitted for Review',
                        "Board paper \"{$paper->title}\" has been submitted for your review for meeting \"{$paper->meeting->title}\".",
                        'paper_review',
                        "/workflow/director/board-papers/{$paper->id}",
                        $paper->meeting_id,
                        $director->email,
                        'workflow.emails.paper-submitted-director',
                        ['paper' => $paper]
                    );
                }
            } else {
                // Auto-approve: either no director, or author IS the director
                $paper->update([
                    'status' => 'director_approved',
                    'submitted_at' => now(),
                    'is_auto_approved' => true,
                    'director_approved_at' => now(),
                    'director_approved_by' => $authorIsDirector ? $paper->prepared_by : null,
                ]);

                $ceoUsers = User::role('CEO')->get();
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Board Paper Awaiting CEO Review',
                        "Board paper \"{$paper->title}\" for meeting \"{$paper->meeting->title}\" is awaiting your review."
                            . ($authorIsDirector ? ' (Director-authored paper, auto-approved.)' : ''),
                        'ceo_paper_review',
                        "/workflow/ceo/meeting-papers/{$paper->id}/review",
                        $paper->meeting_id,
                        $ceo->email,
                        'workflow.emails.paper-submitted-ceo',
                        ['paper' => $paper]
                    );
                }
            }

            if ($assignment) {
                $assignment->update(['status' => 'paper_submitted']);
            }

            $auditMessage = match (true) {
                $authorIsDirector => "Board paper submitted by Director (auto-approved to CEO): {$paper->title}",
                $hasDirector      => "Board paper submitted for director review: {$paper->title}",
                default           => "Board paper submitted directly for CEO review (no director): {$paper->title}",
            };

            TwwdaAuditService::log(
                'board_paper_submitted',
                'Stage 5: Board Paper Creation',
                TwwdaBoardPaper::class,
                $paper->id,
                $auditMessage,
                ['status' => 'draft'],
                ['status' => $paper->status],
                $paper->meeting_id
            );
        });
    }

    /**
     * Add an annexure/attachment to a board paper.
     */
    public function addAnnexure(TwwdaBoardPaper $paper, UploadedFile $file, string $title): TwwdaBoardPaperAnnexure
    {
        $sortOrder = TwwdaBoardPaperAnnexure::where('board_paper_id', $paper->id)->count() + 1;

        $filePath = $file->store(
            'twwda/annexures/' . $paper->meeting_id . '/' . $paper->id,
            'public'
        );

        $annexure = TwwdaBoardPaperAnnexure::create([
            'board_paper_id' => $paper->id,
            'title' => $title,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'sort_order' => $sortOrder,
        ]);

        TwwdaAuditService::log(
            'annexure_added',
            'Stage 5: Board Paper Creation',
            TwwdaBoardPaperAnnexure::class,
            $annexure->id,
            "Annexure added to paper \"{$paper->title}\": {$title}",
            null,
            $annexure->toArray(),
            $paper->meeting_id
        );

        return $annexure;
    }

    /**
     * Author recalls a board paper.
     *
     * Two cases:
     * 1) Paper is with Director (status=submitted) → revert to draft
     * 2) Paper went directly to CEO (is_auto_approved) and CEO hasn't acted → revert to draft
     */
    public function authorRecall(TwwdaBoardPaper $paper): void
    {
        DB::transaction(function () use ($paper) {
            $oldStatus = $paper->status;
            $meeting = $paper->meeting;

            if ($paper->status === 'submitted') {
                // Recall from director
                $paper->update([
                    'status' => 'draft',
                    'submitted_at' => null,
                ]);

                // Notify the director
                $director = $paper->assignment?->director;
                if ($director) {
                    TwwdaNotificationService::notify(
                        $director->id,
                        'Board Paper Recalled',
                        "Board paper \"{$paper->title}\" has been recalled by the author and is no longer pending your review.",
                        'paper_recalled',
                        "/workflow/director/board-papers",
                        $paper->meeting_id,
                        $director->email
                    );
                }

                // Update assignment status
                if ($paper->assignment) {
                    $paper->assignment->update(['status' => 'assigned']);
                }

            } elseif (in_array($paper->status, ['director_approved', 'ceo_review']) && $paper->is_auto_approved) {
                // Recall from CEO (direct submission — author is director or no director assigned)
                $paper->update([
                    'status' => 'draft',
                    'submitted_at' => null,
                    'director_approved_at' => null,
                    'director_approved_by' => null,
                    'is_auto_approved' => false,
                ]);

                // Notify CEO/DDIA that paper was recalled
                $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
                $isAuditCommittee = $meeting && in_array($meeting->board_committee_id, $auditCommitteeIds);
                $approverRole = $isAuditCommittee ? 'DD Internal Audit' : 'CEO';
                $approverUsers = User::role($approverRole)->get();
                foreach ($approverUsers as $approver) {
                    TwwdaNotificationService::notify(
                        $approver->id,
                        'Board Paper Recalled',
                        "Board paper \"{$paper->title}\" has been recalled by the author and is no longer pending your review.",
                        'paper_recalled',
                        "/workflow/ceo/meeting-papers",
                        $paper->meeting_id,
                        $approver->email
                    );
                }

                if ($paper->assignment) {
                    $paper->assignment->update(['status' => 'assigned']);
                }
            }

            TwwdaAuditService::log(
                'board_paper_recalled',
                'Board Paper Recall',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper recalled by author: {$paper->title}",
                ['status' => $oldStatus],
                ['status' => 'draft'],
                $paper->meeting_id
            );
        });
    }

    /**
     * Remove an annexure from a board paper.
     */
    public function removeAnnexure(TwwdaBoardPaperAnnexure $annexure): void
    {
        $paperTitle = $annexure->boardPaper->title ?? 'Unknown';
        $meetingId = $annexure->boardPaper->meeting_id ?? null;
        $annexureData = $annexure->toArray();

        if ($annexure->file_path) {
            Storage::disk('public')->delete($annexure->file_path);
        }

        $annexure->delete();

        TwwdaAuditService::log(
            'annexure_removed',
            'Stage 5: Board Paper Creation',
            TwwdaBoardPaperAnnexure::class,
            $annexureData['id'],
            "Annexure removed from paper \"{$paperTitle}\": {$annexureData['title']}",
            $annexureData,
            null,
            $meetingId
        );
    }
}
