<?php

namespace App\Http\Services\Twwda;

use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaBoardPaperComment;
use App\Models\Twwda\TwwdaStickyNote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CeoPaperReviewService
{
    /** Audit committee IDs — DDIA approves papers for these committees. */
    protected array $auditCommitteeIds;

    /**
     * Resolve audit committee IDs from the database.
     */
    protected function getAuditCommitteeIds(): array
    {
        if (!isset($this->auditCommitteeIds)) {
            $this->auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
        }
        return $this->auditCommitteeIds;
    }

    /**
     * Check if a paper belongs to an audit committee meeting.
     */
    protected function isAuditPaper(TwwdaBoardPaper $paper): bool
    {
        $meeting = $paper->meeting ?? \App\Models\Twwda\TwwdaMeeting::find($paper->meeting_id);
        return $meeting && in_array($meeting->board_committee_id, $this->getAuditCommitteeIds());
    }

    /**
     * CEO/DDIA approves a board paper with optional signature.
     * For audit committee papers, DDIA is the approver.
     */
    public function approve(TwwdaBoardPaper $paper, ?string $signature = null): void
    {
        DB::transaction(function () use ($paper, $signature) {
            $oldStatus = $paper->status;
            $isAudit = $this->isAuditPaper($paper);
            $approverRole = $isAudit ? 'DD Internal Audit' : 'CEO';

            $paper->update([
                'status' => 'ceo_approved',
                'ceo_approved_at' => now(),
                'ceo_signature' => $signature,
            ]);

            // Update assignment status
            $assignment = $paper->assignment;
            if ($assignment) {
                $assignment->update(['status' => 'paper_approved']);
            }

            // Notify the paper author
            TwwdaNotificationService::notify(
                $paper->prepared_by,
                "Board Paper Approved by {$approverRole}",
                "Your board paper \"{$paper->title}\" has been approved by the {$approverRole} for meeting \"{$paper->meeting->title}\".",
                'paper_ceo_approved',
                "/workflow/board-papers/{$paper->id}",
                $paper->meeting_id,
                $paper->author->email ?? null,
                'workflow.emails.paper-ceo-approved',
                ['paper' => $paper]
            );

            // Notify the director if one exists
            if ($assignment && $assignment->director_id) {
                $director = User::find($assignment->director_id);
                if ($director) {
                    TwwdaNotificationService::notify(
                        $director->id,
                        "Board Paper Approved by {$approverRole}",
                        "Board paper \"{$paper->title}\" that you reviewed has been approved by the {$approverRole}.",
                        'paper_ceo_approved',
                        "/workflow/board-papers/{$paper->id}",
                        $paper->meeting_id
                    );
                }
            }

            // If audit committee, also notify CEO for information
            if ($isAudit) {
                $ceoUsers = User::role('CEO')->get();
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Board Paper Approved (FYI — Audit Committee)',
                        "Board paper \"{$paper->title}\" for meeting \"{$paper->meeting->title}\" (Audit Committee) has been approved by the DD Internal Audit.",
                        'paper_approved_fyi',
                        "/workflow/ceo/meeting-papers/{$paper->id}/review",
                        $paper->meeting_id
                    );
                }
            }

            TwwdaAuditService::log(
                'board_paper_ceo_approved',
                'Stage 7: CEO Paper Review',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper approved by {$approverRole}: {$paper->title}",
                ['status' => $oldStatus],
                ['status' => 'ceo_approved', 'approver_role' => $approverRole],
                $paper->meeting_id
            );

            // Check if ALL papers for this meeting are now CEO-approved
            // If so, advance the meeting to Stage 8 (Meeting Preparation)
            $this->checkAndAdvanceMeetingStage($paper->meeting);
        });
    }

    /**
     * Check if all board papers for a meeting are CEO-approved.
     * If yes, advance the meeting to Stage 8 (Meeting Preparation).
     */
    protected function checkAndAdvanceMeetingStage($meeting): void
    {
        $totalPapers = TwwdaBoardPaper::where('meeting_id', $meeting->id)->count();
        $approvedPapers = TwwdaBoardPaper::where('meeting_id', $meeting->id)
            ->where('status', 'ceo_approved')
            ->count();

        if ($totalPapers > 0 && $totalPapers === $approvedPapers) {
            // All papers approved — advance to Stage 8
            if ($meeting->current_stage < 8) {
                $meeting->update([
                    'status' => 'papers_approved',
                    'current_stage' => 8,
                ]);

                // Notify Corporation Secretary that meeting is ready for preparation
                $csUsers = User::role('Corporation Secretary')->get();
                foreach ($csUsers as $cs) {
                    TwwdaNotificationService::notify(
                        $cs->id,
                        'All Board Papers Approved — Ready for Meeting Preparation',
                        "All board papers for meeting \"{$meeting->title}\" have been approved by the CEO. You can now proceed with meeting preparation and agenda finalisation.",
                        'meeting_papers_approved',
                        "/workflow/meetings/{$meeting->id}/prepare",
                        $meeting->id,
                        $cs->email,
                        'workflow.emails.meeting-papers-ready',
                        ['meeting' => $meeting]
                    );
                }

                TwwdaAuditService::log(
                    'meeting_advanced_to_preparation',
                    'Stage 8: Meeting Preparation',
                    \App\Models\Twwda\TwwdaMeeting::class,
                    $meeting->id,
                    "All papers approved. Meeting \"{$meeting->title}\" advanced to Stage 8 (Meeting Preparation).",
                    ['current_stage' => 7],
                    ['current_stage' => 8],
                    $meeting->id
                );
            }
        }
    }

    /**
     * CEO/DDIA rejects a board paper.
     * Routes back to director if one exists, otherwise directly to the DD.
     */
    public function reject(TwwdaBoardPaper $paper, string $comment): void
    {
        DB::transaction(function () use ($paper, $comment) {
            $oldStatus = $paper->status;
            $assignment = $paper->assignment;
            $hasDirector = $assignment && $assignment->director_id;
            $isAudit = $this->isAuditPaper($paper);
            $approverRole = $isAudit ? 'DD Internal Audit' : 'CEO';

            $paper->update([
                'status' => 'rejected_by_ceo',
            ]);

            TwwdaBoardPaperComment::create([
                'board_paper_id' => $paper->id,
                'user_id' => Auth::id(),
                'comment' => $comment,
                'action' => 'rejected',
                'reviewer_role' => $isAudit ? 'ddia' : 'ceo',
                'iteration' => $paper->version,
            ]);

            if ($hasDirector) {
                // Notify the director to review comments and relay to DD
                $director = User::find($assignment->director_id);
                if ($director) {
                    TwwdaNotificationService::notify(
                        $director->id,
                        "Board Paper Rejected by {$approverRole}",
                        "Board paper \"{$paper->title}\" has been rejected by the {$approverRole}. Please review the comments and coordinate with your deputy for revision.",
                        'paper_ceo_rejected',
                        "/workflow/director/board-papers/{$paper->id}",
                        $paper->meeting_id,
                        $director->email,
                        'workflow.emails.paper-ceo-rejected-director',
                        ['paper' => $paper, 'comment' => $comment]
                    );
                }
            }

            // Always notify the paper author (DD)
            TwwdaNotificationService::notify(
                $paper->prepared_by,
                "Board Paper Rejected by {$approverRole}",
                "Your board paper \"{$paper->title}\" has been rejected by the {$approverRole}. Please review the comments and resubmit.",
                'paper_ceo_rejected',
                "/workflow/board-papers/{$paper->id}",
                $paper->meeting_id,
                $paper->author->email ?? null,
                'workflow.emails.paper-ceo-rejected',
                ['paper' => $paper, 'comment' => $comment]
            );

            TwwdaAuditService::log(
                'board_paper_ceo_rejected',
                'Stage 7: CEO Paper Review',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper rejected by {$approverRole}: {$paper->title}. Comments: {$comment}",
                ['status' => $oldStatus],
                ['status' => 'rejected_by_ceo', 'approver_role' => $approverRole],
                $paper->meeting_id
            );
        });
    }

    /**
     * Add a sticky note annotation to a board paper.
     *
     * Data should contain: page_number, x_position, y_position, content, color (optional)
     */
    public function addStickyNote(TwwdaBoardPaper $paper, array $data): TwwdaStickyNote
    {
        $note = TwwdaStickyNote::create([
            'board_paper_id' => $paper->id,
            'user_id' => Auth::id(),
            'page_number' => $data['page_number'] ?? 1,
            'x_position' => $data['x_position'] ?? 0,
            'y_position' => $data['y_position'] ?? 0,
            'content' => $data['content'],
            'color' => $data['color'] ?? 'yellow',
        ]);

        TwwdaAuditService::log(
            'sticky_note_added',
            'Stage 7: CEO Paper Review',
            TwwdaStickyNote::class,
            $note->id,
            "Sticky note added to paper \"{$paper->title}\"",
            null,
            $note->toArray(),
            $paper->meeting_id
        );

        return $note;
    }

    /**
     * Remove a sticky note from a board paper.
     */
    public function removeStickyNote(TwwdaStickyNote $note): void
    {
        $noteData = $note->toArray();
        $paperTitle = $note->boardPaper->title ?? 'Unknown';
        $meetingId = $note->boardPaper->meeting_id ?? null;

        $note->delete();

        TwwdaAuditService::log(
            'sticky_note_removed',
            'Stage 7: CEO Paper Review',
            TwwdaStickyNote::class,
            $noteData['id'],
            "Sticky note removed from paper \"{$paperTitle}\"",
            $noteData,
            null,
            $meetingId
        );
    }
}
