<?php

namespace App\Http\Services\Twwda;

use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaBoardPaperComment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectorReviewService
{
    /**
     * Director approves a board paper and forwards it to the CEO for review.
     */
    public function approve(TwwdaBoardPaper $paper, ?string $comment = null): void
    {
        DB::transaction(function () use ($paper, $comment) {
            $oldStatus = $paper->status;

            $paper->update([
                'status' => 'director_approved',
                'director_approved_by' => Auth::id(),
                'director_approved_at' => now(),
            ]);

            // Record approval comment if provided
            if ($comment) {
                TwwdaBoardPaperComment::create([
                    'board_paper_id' => $paper->id,
                    'user_id' => Auth::id(),
                    'comment' => $comment,
                    'action' => 'approved',
                    'reviewer_role' => 'director',
                    'iteration' => $paper->version,
                ]);
            }

            // Notify the paper author
            TwwdaNotificationService::notify(
                $paper->prepared_by,
                'Board Paper Approved by Director',
                "Your board paper \"{$paper->title}\" has been approved by the director and forwarded to the CEO for review.",
                'paper_approved',
                "/workflow/board-papers/{$paper->id}",
                $paper->meeting_id,
                $paper->author->email ?? null,
                'workflow.emails.paper-director-approved',
                ['paper' => $paper]
            );

            // Notify CEO for next review stage
            $ceoUsers = User::role('CEO')->get();
            foreach ($ceoUsers as $ceo) {
                TwwdaNotificationService::notify(
                    $ceo->id,
                    'Board Paper Awaiting CEO Review',
                    "Board paper \"{$paper->title}\" for meeting \"{$paper->meeting->title}\" has been approved by the director and is awaiting your review.",
                    'ceo_paper_review',
                    "/workflow/ceo/meeting-papers/{$paper->id}/review",
                    $paper->meeting_id,
                    $ceo->email,
                    'workflow.emails.paper-submitted-ceo',
                    ['paper' => $paper]
                );
            }

            TwwdaAuditService::log(
                'board_paper_director_approved',
                'Stage 6: Director Review',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper approved by director: {$paper->title}",
                ['status' => $oldStatus],
                ['status' => 'director_approved'],
                $paper->meeting_id
            );
        });
    }

    /**
     * Director recalls a board paper from CEO review back under director control.
     * Status: director_approved / ceo_review → submitted
     */
    public function recall(TwwdaBoardPaper $paper): void
    {
        DB::transaction(function () use ($paper) {
            $oldStatus = $paper->status;
            $meeting = $paper->meeting;

            $paper->update([
                'status' => 'submitted',
                'director_approved_at' => null,
                'director_approved_by' => null,
            ]);

            // Notify the paper author
            TwwdaNotificationService::notify(
                $paper->prepared_by,
                'Board Paper Recalled by Director',
                "Your board paper \"{$paper->title}\" has been recalled from CEO review by the director. It is now back under director review.",
                'paper_recalled',
                "/workflow/board-papers/{$paper->id}",
                $paper->meeting_id,
                $paper->author->email ?? null
            );

            // Notify CEO/DDIA that paper was recalled
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAuditCommittee = $meeting && in_array($meeting->board_committee_id, $auditCommitteeIds);
            $approverRole = $isAuditCommittee ? 'DD Internal Audit' : 'CEO';
            $approverUsers = User::role($approverRole)->get();
            foreach ($approverUsers as $approver) {
                TwwdaNotificationService::notify(
                    $approver->id,
                    'Board Paper Recalled',
                    "Board paper \"{$paper->title}\" has been recalled by the director and is no longer pending your review.",
                    'paper_recalled',
                    "/workflow/ceo/meeting-papers",
                    $paper->meeting_id,
                    $approver->email
                );
            }

            TwwdaAuditService::log(
                'board_paper_director_recalled',
                'Stage 6: Director Review',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper recalled from CEO review by director: {$paper->title}",
                ['status' => $oldStatus],
                ['status' => 'submitted'],
                $paper->meeting_id
            );
        });
    }

    /**
     * Director rejects a board paper back to the DD for revision.
     */
    public function reject(TwwdaBoardPaper $paper, string $comment): void
    {
        DB::transaction(function () use ($paper, $comment) {
            $oldStatus = $paper->status;

            $paper->update([
                'status' => 'rejected_by_director',
            ]);

            TwwdaBoardPaperComment::create([
                'board_paper_id' => $paper->id,
                'user_id' => Auth::id(),
                'comment' => $comment,
                'action' => 'rejected',
                'reviewer_role' => 'director',
                'iteration' => $paper->version,
            ]);

            // Notify the paper author (DD) to revise
            TwwdaNotificationService::notify(
                $paper->prepared_by,
                'Board Paper Rejected by Director',
                "Your board paper \"{$paper->title}\" has been rejected by the director. Please review the comments and resubmit.",
                'paper_rejected',
                "/workflow/board-papers/{$paper->id}",
                $paper->meeting_id,
                $paper->author->email ?? null,
                'workflow.emails.paper-director-rejected',
                ['paper' => $paper, 'comment' => $comment]
            );

            TwwdaAuditService::log(
                'board_paper_director_rejected',
                'Stage 6: Director Review',
                TwwdaBoardPaper::class,
                $paper->id,
                "Board paper rejected by director: {$paper->title}. Comments: {$comment}",
                ['status' => $oldStatus],
                ['status' => 'rejected_by_director'],
                $paper->meeting_id
            );
        });
    }
}
