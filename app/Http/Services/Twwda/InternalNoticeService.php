<?php

namespace App\Http\Services\Twwda;

use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaInternalNotice;
use App\Models\Twwda\TwwdaInternalNoticeComment;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternalNoticeService
{
    protected TwwdaNotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new TwwdaNotificationService();
    }

    /**
     * Create or update an internal notice.
     */
    public function store(Request $request, TwwdaMeeting $meeting): TwwdaInternalNotice
    {
        return DB::transaction(function () use ($request, $meeting) {
            $notice = TwwdaInternalNotice::updateOrCreate(
                ['meeting_id' => $meeting->id],
                [
                    'reference_number' => $request->reference_number,
                    'reference_date' => $request->reference_date,
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'agenda_items' => $request->agenda_items,
                    'meeting_venue' => $request->meeting_venue ?? $meeting->venue,
                    'meeting_date' => $request->meeting_date ?? $meeting->meeting_date,
                    'meeting_time' => $request->meeting_time ?? $meeting->meeting_time,
                    'status' => 'draft',
                    'created_by' => Auth::id(),
                ]
            );

            $meeting->update(['status' => 'notice_created', 'current_stage' => 2]);

            TwwdaAuditService::log(
                'internal_notice_created',
                'Stage 2: Internal Notice',
                TwwdaInternalNotice::class,
                $notice->id,
                "Internal notice created for meeting: {$meeting->title}",
                null,
                $notice->toArray(),
                $meeting->id
            );

            return $notice;
        });
    }

    /** Audit committee IDs — DDIA approves internal notices for these committees. */
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
     * Check if a meeting belongs to the audit committee.
     */
    protected function isAuditMeeting(TwwdaInternalNotice $notice): bool
    {
        $meeting = $notice->meeting ?? \App\Models\Twwda\TwwdaMeeting::find($notice->meeting_id);
        return $meeting && in_array($meeting->board_committee_id, $this->getAuditCommitteeIds());
    }

    /**
     * Submit notice for approval.
     * Audit committee notices → DD Internal Audit approves, CEO gets FYI.
     * All other notices → CEO approves.
     */
    public function submit(TwwdaInternalNotice $notice): void
    {
        DB::transaction(function () use ($notice) {
            $notice->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $notice->meeting->update([
                'status' => 'notice_pending_approval',
                'current_stage' => 3,
            ]);

            if ($this->isAuditMeeting($notice)) {
                // Audit committee → Notify DD Internal Audit as approver
                $ddiaUsers = User::role('DD Internal Audit')->get();
                foreach ($ddiaUsers as $ddia) {
                    TwwdaNotificationService::notify(
                        $ddia->id,
                        'Internal Notice Awaiting Your Approval',
                        "An internal notice for \"{$notice->meeting->title}\" (Audit Committee) is awaiting your approval.",
                        'notice_approval',
                        "/workflow/ceo/internal-notices/{$notice->id}",
                        $notice->meeting_id,
                        $ddia->email,
                        'workflow.emails.notice-submitted',
                        ['notice' => $notice]
                    );
                }

                // Also notify CEO for information only
                $ceoUsers = User::role('CEO')->get();
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Internal Notice Submitted (FYI — Audit Committee)',
                        "An internal notice for \"{$notice->meeting->title}\" (Audit Committee) has been submitted. This will be approved by the DD Internal Audit.",
                        'notice_approval_fyi',
                        "/workflow/ceo/internal-notices/{$notice->id}",
                        $notice->meeting_id,
                        $ceo->email,
                        'workflow.emails.notice-submitted',
                        ['notice' => $notice]
                    );
                }

                TwwdaAuditService::log(
                    'internal_notice_submitted',
                    'Stage 3: Internal Notice Approval',
                    TwwdaInternalNotice::class,
                    $notice->id,
                    "Internal notice submitted for DD Internal Audit approval (Audit Committee)",
                    null,
                    ['status' => 'submitted', 'approver' => 'DDIA'],
                    $notice->meeting_id
                );
            } else {
                // Non-audit → Notify CEO as approver
                $ceoUsers = User::role('CEO')->get();
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Internal Notice Awaiting Approval',
                        "An internal notice for \"{$notice->meeting->title}\" is awaiting your approval.",
                        'notice_approval',
                        "/workflow/ceo/internal-notices/{$notice->id}",
                        $notice->meeting_id,
                        $ceo->email,
                        'workflow.emails.notice-submitted',
                        ['notice' => $notice]
                    );
                }

                TwwdaAuditService::log(
                    'internal_notice_submitted',
                    'Stage 3: Internal Notice Approval',
                    TwwdaInternalNotice::class,
                    $notice->id,
                    "Internal notice submitted for CEO approval",
                    null,
                    ['status' => 'submitted'],
                    $notice->meeting_id
                );
            }
        });
    }

    /**
     * Approve the notice (CEO or DDIA for audit committee).
     */
    public function approve(TwwdaInternalNotice $notice, ?string $signature = null): void
    {
        DB::transaction(function () use ($notice, $signature) {
            $isAudit = $this->isAuditMeeting($notice);
            $approverRole = $isAudit ? 'DD Internal Audit' : 'CEO';

            $notice->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'ceo_signature' => $signature,
            ]);

            $notice->meeting->update([
                'status' => 'notice_approved',
                'current_stage' => 4,
            ]);

            // Notify the creator
            TwwdaNotificationService::notify(
                $notice->created_by,
                'Internal Notice Approved',
                "Your internal notice for \"{$notice->meeting->title}\" has been approved by the {$approverRole}.",
                'notice_approved',
                "/workflow/meetings/{$notice->meeting_id}/internal-notice",
                $notice->meeting_id,
                optional($notice->creator)->email,
                'workflow.emails.notice-approved',
                ['notice' => $notice]
            );

            TwwdaAuditService::log(
                'internal_notice_approved',
                'Stage 3: Internal Notice Approval',
                TwwdaInternalNotice::class,
                $notice->id,
                "Internal notice approved by {$approverRole}",
                ['status' => 'submitted'],
                ['status' => 'approved', 'approver_role' => $approverRole],
                $notice->meeting_id
            );
        });
    }

    /**
     * Reject the notice with comments (CEO or DDIA for audit committee).
     */
    public function reject(TwwdaInternalNotice $notice, string $comment): void
    {
        DB::transaction(function () use ($notice, $comment) {
            $isAudit = $this->isAuditMeeting($notice);
            $approverRole = $isAudit ? 'DD Internal Audit' : 'CEO';

            $notice->update([
                'status' => 'rejected',
            ]);

            $notice->meeting->update([
                'status' => 'notice_rejected',
            ]);

            TwwdaInternalNoticeComment::create([
                'notice_id' => $notice->id,
                'user_id' => Auth::id(),
                'comment' => $comment,
                'action' => 'rejected',
                'iteration' => $notice->version,
            ]);

            // Notify the creator
            TwwdaNotificationService::notify(
                $notice->created_by,
                'Internal Notice Rejected',
                "Your internal notice for \"{$notice->meeting->title}\" has been rejected by the {$approverRole}. Please review the comments and resubmit.",
                'notice_rejected',
                "/workflow/meetings/{$notice->meeting_id}/internal-notice",
                $notice->meeting_id,
                optional($notice->creator)->email,
                'workflow.emails.notice-rejected',
                ['notice' => $notice, 'comment' => $comment]
            );

            TwwdaAuditService::log(
                'internal_notice_rejected',
                'Stage 3: Internal Notice Approval',
                TwwdaInternalNotice::class,
                $notice->id,
                "Internal notice rejected by {$approverRole}. Comments: {$comment}",
                ['status' => 'submitted'],
                ['status' => 'rejected'],
                $notice->meeting_id
            );
        });
    }

    /**
     * Resubmit after rejection.
     */
    public function resubmit(TwwdaInternalNotice $notice, Request $request): void
    {
        DB::transaction(function () use ($notice, $request) {
            $notice->update([
                'subject' => $request->subject ?? $notice->subject,
                'body' => $request->body ?? $notice->body,
                'agenda_items' => $request->agenda_items ?? $notice->agenda_items,
                'status' => 'submitted',
                'submitted_at' => now(),
                'version' => $notice->version + 1,
            ]);

            TwwdaInternalNoticeComment::create([
                'notice_id' => $notice->id,
                'user_id' => Auth::id(),
                'comment' => $request->resubmission_comment ?? 'Resubmitted with corrections.',
                'action' => 'resubmitted',
                'iteration' => $notice->version,
            ]);

            $notice->meeting->update([
                'status' => 'notice_pending_approval',
            ]);

            TwwdaAuditService::log(
                'internal_notice_resubmitted',
                'Stage 3: Internal Notice Approval',
                TwwdaInternalNotice::class,
                $notice->id,
                "Internal notice resubmitted (version {$notice->version})",
                null,
                ['version' => $notice->version],
                $notice->meeting_id
            );
        });
    }

    /**
     * Special approval when CEO is unavailable.
     */
    public function specialApprove(TwwdaInternalNotice $notice, string $reason): void
    {
        DB::transaction(function () use ($notice, $reason) {
            $notice->update([
                'status' => 'special_approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'is_special_approval' => true,
                'special_approval_reason' => $reason,
            ]);

            $notice->meeting->update([
                'status' => 'notice_approved',
                'current_stage' => 4,
            ]);

            // Notify the notice creator
            $approverRole = Auth::user()->getRoleNames()->first() ?? 'Authorized Officer';
            TwwdaNotificationService::notify(
                $notice->created_by,
                'Internal Notice Special Approved',
                "Your internal notice for \"{$notice->meeting->title}\" has been special-approved by {$approverRole} (" . Auth::user()->name . ") while the CEO is unavailable.",
                'notice_special_approved',
                "/workflow/meetings/{$notice->meeting_id}/internal-notice",
                $notice->meeting_id,
                $notice->creator->email ?? null,
                'workflow.emails.notice-approved',
                ['notice' => $notice]
            );

            // Also notify CEO for awareness
            $ceoUsers = User::role('CEO')->get();
            foreach ($ceoUsers as $ceo) {
                TwwdaNotificationService::notify(
                    $ceo->id,
                    'Internal Notice Special Approved (FYI)',
                    "The internal notice for \"{$notice->meeting->title}\" was special-approved by {$approverRole} (" . Auth::user()->name . ") while you were unavailable. Reason: {$reason}",
                    'notice_special_approved_fyi',
                    "/workflow/meetings/{$notice->meeting_id}",
                    $notice->meeting_id,
                    $ceo->email,
                    'workflow.emails.notice-approved',
                    ['notice' => $notice]
                );
            }

            TwwdaAuditService::log(
                'internal_notice_special_approved',
                'Stage 3: Internal Notice Approval',
                TwwdaInternalNotice::class,
                $notice->id,
                "Internal notice special approved by " . Auth::user()->name . " ({$approverRole}). Reason: {$reason}",
                null,
                ['status' => 'special_approved', 'reason' => $reason, 'approver_role' => $approverRole],
                $notice->meeting_id
            );
        });
    }
}
