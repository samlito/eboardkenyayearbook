<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaBoardNotice;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaMeetingAgendaItem;
use App\Models\Twwda\TwwdaOfficerAssignment;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaMeetingPreparation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingPreparationService
{
    /**
     * Audit committee IDs — DDIA has self-approval powers (no CEO needed).
     */
    protected array $auditCommitteeIds;

    public function __construct()
    {
        $this->auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
    }

    // ═══════════════════════════════════════════════════════════════
    //  SEEDING — Populate agenda items from the official board notice
    // ═══════════════════════════════════════════════════════════════

    /**
     * Seed agenda items from the latest sent board notice for this meeting.
     * Only runs once (tracks via agenda_items_seeded_at on preparation record).
     */
    public function seedAgendaItemsFromNotice(TwwdaMeeting $meeting): void
    {
        $preparation = $this->getOrCreatePreparation($meeting);

        // Already seeded? Skip.
        if ($preparation->agenda_items_seeded_at) {
            return;
        }

        // Find the latest approved or sent notice for this meeting
        $notice = TwwdaBoardNotice::where('meeting_id', $meeting->id)
            ->whereIn('status', ['approved', 'sent'])
            ->latest('updated_at')
            ->first();

        if (!$notice) {
            return;
        }

        $agendaItems = is_array($notice->agenda_items)
            ? $notice->agenda_items
            : (json_decode($notice->agenda_items, true) ?? []);

        if (empty($agendaItems)) {
            return;
        }

        // Build a lookup of assignment agenda titles to their approved board papers
        $assignmentPaperMap = $this->buildAssignmentPaperMap($meeting);

        DB::transaction(function () use ($meeting, $notice, $agendaItems, $preparation, $assignmentPaperMap) {
            foreach ($agendaItems as $index => $itemText) {
                $boardPaperId = $assignmentPaperMap[$itemText] ?? null;

                TwwdaMeetingAgendaItem::create([
                    'meeting_id'           => $meeting->id,
                    'source'               => 'notice',
                    'original_notice_text' => $itemText,
                    'title'                => $itemText,
                    'item_number'          => ($index + 1) . '.0',
                    'display_order'        => $index + 1,
                    'status'               => 'active',
                    'added_by'             => $notice->created_by,
                    'board_paper_id'       => $boardPaperId,
                ]);
            }

            $preparation->update(['agenda_items_seeded_at' => now()]);

            $autoAssigned = count(array_filter($assignmentPaperMap));
            TwwdaAuditService::log(
                'agenda_items_seeded',
                'Stage 8: Meeting Preparation',
                TwwdaMeeting::class,
                $meeting->id,
                "Seeded " . count($agendaItems) . " agenda items from official notice (Ref: {$notice->reference_number}). Auto-assigned {$autoAssigned} board paper(s).",
                null,
                ['notice_id' => $notice->id, 'item_count' => count($agendaItems), 'auto_assigned' => $autoAssigned],
                $meeting->id
            );
        });
    }

    /**
     * Build a map of agenda_item_title => board_paper_id for auto-assignment.
     * Matches officer assignment titles to their CEO-approved board papers.
     */
    private function buildAssignmentPaperMap(TwwdaMeeting $meeting): array
    {
        $map = [];

        $assignments = TwwdaOfficerAssignment::where('meeting_id', $meeting->id)
            ->whereNotNull('agenda_item_title')
            ->where('agenda_item_title', '!=', '')
            ->with(['boardPapers' => function ($q) {
                $q->where(function ($sq) {
                    $sq->where('status', 'ceo_approved')
                       ->orWhere('is_auto_approved', true);
                });
            }])
            ->get();

        foreach ($assignments as $assignment) {
            $paper = $assignment->boardPapers->first();
            if ($paper) {
                $map[$assignment->agenda_item_title] = $paper->id;
            }
        }

        return $map;
    }

    // ═══════════════════════════════════════════════════════════════
    //  REORDER — Drag-and-drop agenda items
    // ═══════════════════════════════════════════════════════════════

    /**
     * Reorder agenda items. Accepts an array of item IDs in desired order.
     */
    public function reorderAgendaItems(TwwdaMeeting $meeting, array $orderedIds): void
    {
        DB::transaction(function () use ($meeting, $orderedIds) {
            $oldOrder = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->visibleToAdmin()
                ->ordered()
                ->pluck('id')
                ->toArray();

            foreach ($orderedIds as $position => $itemId) {
                TwwdaMeetingAgendaItem::where('id', $itemId)
                    ->where('meeting_id', $meeting->id)
                    ->update(['display_order' => $position + 1]);
            }

            TwwdaAuditService::log(
                'agenda_items_reordered',
                'Stage 8: Meeting Preparation',
                TwwdaMeeting::class,
                $meeting->id,
                "Agenda items reordered for meeting: {$meeting->title}",
                ['order' => $oldOrder],
                ['order' => $orderedIds],
                $meeting->id
            );
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADD NEW AGENDA ITEM — CS or DDIA introduces a new item
    // ═══════════════════════════════════════════════════════════════

    /**
     * Add a new agenda item with optional board paper.
     * CS-added items require CEO approval. DDIA-added items are auto-approved.
     *
     * @param array $data  Keys: title, description, item_number
     * @param int|null $boardPaperId  Optional linked board paper
     */
    public function addNewAgendaItem(TwwdaMeeting $meeting, array $data, ?int $boardPaperId = null): TwwdaMeetingAgendaItem
    {
        $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);
        $source = $isAudit ? 'ddia_added' : 'cs_added';
        $requiresCeo = !$isAudit; // CS needs CEO approval, DDIA doesn't

        // Determine display_order (append at end)
        $maxOrder = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)->max('display_order') ?? 0;

        $item = TwwdaMeetingAgendaItem::create([
            'meeting_id'           => $meeting->id,
            'source'               => $source,
            'title'                => $data['title'],
            'description'          => $data['description'] ?? null,
            'item_number'          => $data['item_number'] ?? null,
            'display_order'        => $maxOrder + 1,
            'status'               => $requiresCeo ? 'pending_approval' : 'approved',
            'board_paper_id'       => $boardPaperId,
            'requires_ceo_approval'=> $requiresCeo,
            'added_by'             => Auth::id(),
            'approved_by'          => $requiresCeo ? null : Auth::id(),
            'approved_at'          => $requiresCeo ? null : now(),
        ]);

        // Audit
        TwwdaAuditService::log(
            'agenda_item_added',
            'Stage 8: Meeting Preparation',
            TwwdaMeetingAgendaItem::class,
            $item->id,
            "New agenda item added: {$data['title']}" . ($requiresCeo ? ' (pending CEO approval)' : ' (auto-approved)'),
            null,
            $item->toArray(),
            $meeting->id
        );

        // Notify CEO if approval is needed
        if ($requiresCeo) {
            $ceos = User::role('CEO')->get();
            foreach ($ceos as $ceo) {
                TwwdaNotificationService::notify(
                    $ceo->id,
                    'New Agenda Item Pending Approval',
                    "A new agenda item \"{$data['title']}\" has been added to meeting \"{$meeting->title}\" and requires your approval.",
                    'agenda_item_pending',
                    "/workflow/meetings/{$meeting->id}/prepare",
                    $meeting->id,
                    $ceo->email
                );
            }
        }

        return $item;
    }

    // ═══════════════════════════════════════════════════════════════
    //  CEO APPROVE / REJECT NEW AGENDA ITEM
    // ═══════════════════════════════════════════════════════════════

    /**
     * CEO approves a new agenda item (and optionally signs the board paper).
     */
    public function approveNewAgendaItem(TwwdaMeetingAgendaItem $item, ?string $ceoSignature = null): void
    {
        DB::transaction(function () use ($item, $ceoSignature) {
            $item->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // If a board paper is attached, mark it CEO-approved and sign
            if ($item->board_paper_id && $ceoSignature) {
                $paper = TwwdaBoardPaper::find($item->board_paper_id);
                if ($paper) {
                    $paper->update([
                        'status'          => 'ceo_approved',
                        'ceo_approved_at' => now(),
                        'ceo_signature'   => $ceoSignature,
                    ]);
                }
            }

            // Notify the person who added the item
            if ($item->added_by) {
                TwwdaNotificationService::notify(
                    $item->added_by,
                    'Agenda Item Approved',
                    "Your agenda item \"{$item->title}\" has been approved by the CEO.",
                    'agenda_item_approved',
                    "/workflow/meetings/{$item->meeting_id}/prepare",
                    $item->meeting_id
                );
            }

            TwwdaAuditService::log(
                'agenda_item_approved',
                'Stage 8: Meeting Preparation',
                TwwdaMeetingAgendaItem::class,
                $item->id,
                "CEO approved new agenda item: {$item->title}",
                ['status' => 'pending_approval'],
                ['status' => 'approved'],
                $item->meeting_id
            );
        });
    }

    /**
     * CEO rejects a new agenda item.
     */
    public function rejectNewAgendaItem(TwwdaMeetingAgendaItem $item, string $reason): void
    {
        DB::transaction(function () use ($item, $reason) {
            $item->update([
                'status'          => 'rejected',
                'rejected_reason' => $reason,
            ]);

            // Notify the person who added the item
            if ($item->added_by) {
                TwwdaNotificationService::notify(
                    $item->added_by,
                    'Agenda Item Rejected',
                    "Your agenda item \"{$item->title}\" has been rejected by the CEO. Reason: {$reason}",
                    'agenda_item_rejected',
                    "/workflow/meetings/{$item->meeting_id}/prepare",
                    $item->meeting_id
                );
            }

            TwwdaAuditService::log(
                'agenda_item_rejected',
                'Stage 8: Meeting Preparation',
                TwwdaMeetingAgendaItem::class,
                $item->id,
                "CEO rejected new agenda item: {$item->title}. Reason: {$reason}",
                ['status' => 'pending_approval'],
                ['status' => 'rejected', 'reason' => $reason],
                $item->meeting_id
            );
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  PULLDOWN — Hide an agenda item from board members
    // ═══════════════════════════════════════════════════════════════

    /**
     * Request to pull down an agenda item.
     * CS: creates a pending_pulldown request for CEO approval.
     * DDIA: pulls down immediately (self-approve).
     */
    public function requestPulldown(TwwdaMeetingAgendaItem $item, string $justification, TwwdaMeeting $meeting): void
    {
        $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);

        DB::transaction(function () use ($item, $justification, $meeting, $isAudit) {
            if ($isAudit) {
                // DDIA: immediate pulldown (self-approve)
                $item->update([
                    'status'                 => 'pulled_down',
                    'pulldown_justification' => $justification,
                    'pulldown_requested_by'  => Auth::id(),
                    'pulldown_requested_at'  => now(),
                    'pulldown_approved_by'   => Auth::id(),
                    'pulldown_approved_at'   => now(),
                ]);

                TwwdaAuditService::log(
                    'agenda_item_pulled_down',
                    'Stage 8: Meeting Preparation',
                    TwwdaMeetingAgendaItem::class,
                    $item->id,
                    "DDIA pulled down agenda item: {$item->title}. Justification: {$justification}",
                    ['status' => $item->getOriginal('status')],
                    ['status' => 'pulled_down'],
                    $meeting->id
                );
            } else {
                // CS: pending CEO approval
                $item->update([
                    'status'                 => 'pending_pulldown',
                    'pulldown_justification' => $justification,
                    'pulldown_requested_by'  => Auth::id(),
                    'pulldown_requested_at'  => now(),
                ]);

                // Notify CEO
                $ceos = User::role('CEO')->get();
                foreach ($ceos as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Agenda Item Pulldown Request',
                        "A request to pull down agenda item \"{$item->title}\" for meeting \"{$meeting->title}\" requires your approval. Justification: {$justification}",
                        'pulldown_pending',
                        "/workflow/meetings/{$meeting->id}/prepare",
                        $meeting->id,
                        $ceo->email
                    );
                }

                TwwdaAuditService::log(
                    'pulldown_requested',
                    'Stage 8: Meeting Preparation',
                    TwwdaMeetingAgendaItem::class,
                    $item->id,
                    "CS requested pulldown for agenda item: {$item->title}. Justification: {$justification}",
                    ['status' => $item->getOriginal('status')],
                    ['status' => 'pending_pulldown'],
                    $meeting->id
                );
            }
        });
    }

    /**
     * CEO approves a pulldown request.
     */
    public function approvePulldown(TwwdaMeetingAgendaItem $item): void
    {
        DB::transaction(function () use ($item) {
            $item->update([
                'status'               => 'pulled_down',
                'pulldown_approved_by' => Auth::id(),
                'pulldown_approved_at' => now(),
            ]);

            // Notify requester
            if ($item->pulldown_requested_by) {
                TwwdaNotificationService::notify(
                    $item->pulldown_requested_by,
                    'Pulldown Request Approved',
                    "Your request to pull down agenda item \"{$item->title}\" has been approved by the CEO.",
                    'pulldown_approved',
                    "/workflow/meetings/{$item->meeting_id}/prepare",
                    $item->meeting_id
                );
            }

            TwwdaAuditService::log(
                'pulldown_approved',
                'Stage 8: Meeting Preparation',
                TwwdaMeetingAgendaItem::class,
                $item->id,
                "CEO approved pulldown for agenda item: {$item->title}",
                ['status' => 'pending_pulldown'],
                ['status' => 'pulled_down'],
                $item->meeting_id
            );
        });
    }

    /**
     * CEO rejects a pulldown request — item returns to its previous active status.
     */
    public function rejectPulldown(TwwdaMeetingAgendaItem $item, string $reason): void
    {
        DB::transaction(function () use ($item, $reason) {
            // Restore to active (or approved if it was a new item)
            $restoreStatus = $item->source === 'notice' ? 'active' : 'approved';

            $item->update([
                'status'                 => $restoreStatus,
                'pulldown_justification' => null,
                'pulldown_requested_by'  => null,
                'pulldown_requested_at'  => null,
            ]);

            if ($item->pulldown_requested_by) {
                TwwdaNotificationService::notify(
                    $item->getOriginal('pulldown_requested_by'),
                    'Pulldown Request Rejected',
                    "Your request to pull down agenda item \"{$item->title}\" has been rejected. Reason: {$reason}",
                    'pulldown_rejected',
                    "/workflow/meetings/{$item->meeting_id}/prepare",
                    $item->meeting_id
                );
            }

            TwwdaAuditService::log(
                'pulldown_rejected',
                'Stage 8: Meeting Preparation',
                TwwdaMeetingAgendaItem::class,
                $item->id,
                "CEO rejected pulldown for agenda item: {$item->title}. Reason: {$reason}",
                ['status' => 'pending_pulldown'],
                ['status' => $restoreStatus, 'reason' => $reason],
                $item->meeting_id
            );
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  ASSIGN BOARD PAPER TO AGENDA ITEM
    // ═══════════════════════════════════════════════════════════════

    /**
     * Assign an existing CEO-approved board paper to an agenda item.
     */
    public function assignPaperToAgendaItem(TwwdaMeetingAgendaItem $item, int $boardPaperId): void
    {
        $paper = TwwdaBoardPaper::where('id', $boardPaperId)
            ->where('meeting_id', $item->meeting_id)
            ->firstOrFail();

        $item->update(['board_paper_id' => $boardPaperId]);

        TwwdaAuditService::log(
            'paper_assigned_to_agenda',
            'Stage 8: Meeting Preparation',
            TwwdaMeetingAgendaItem::class,
            $item->id,
            "Board paper \"{$paper->title}\" assigned to agenda item \"{$item->title}\"",
            ['board_paper_id' => null],
            ['board_paper_id' => $boardPaperId],
            $item->meeting_id
        );
    }

    // ═══════════════════════════════════════════════════════════════
    //  LEGACY COMPATIBILITY — Keep old reorderPapers for existing code
    // ═══════════════════════════════════════════════════════════════

    /**
     * @deprecated Use reorderAgendaItems instead
     */
    public function reorderPapers(TwwdaMeeting $meeting, array $order): void
    {
        DB::transaction(function () use ($meeting, $order) {
            $preparation = $this->getOrCreatePreparation($meeting);

            foreach ($order as $position => $paperId) {
                TwwdaBoardPaper::where('id', $paperId)
                    ->where('meeting_id', $meeting->id)
                    ->update(['display_order' => $position + 1]);
            }

            $preparation->update(['paper_order' => $order]);
        });
    }

    /**
     * @deprecated Use addNewAgendaItem instead
     */
    public function addAgendaItem(TwwdaMeeting $meeting, array $item): void
    {
        DB::transaction(function () use ($meeting, $item) {
            $preparation = $this->getOrCreatePreparation($meeting);
            $existingItems = $preparation->additional_agenda_items ?? [];

            $newItem = [
                'item_number' => $item['item_number'] ?? null,
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'type' => $item['type'] ?? 'general',
                'added_by' => Auth::id(),
                'added_at' => now()->toIso8601String(),
            ];

            $existingItems[] = $newItem;
            $preparation->update(['additional_agenda_items' => $existingItems]);
        });
    }

    /**
     * @deprecated
     */
    public function removeAgendaItem(TwwdaMeeting $meeting, int $index): void
    {
        DB::transaction(function () use ($meeting, $index) {
            $preparation = $this->getOrCreatePreparation($meeting);
            $items = $preparation->additional_agenda_items ?? [];

            if (!isset($items[$index])) {
                throw new \InvalidArgumentException('Agenda item not found at the specified index.');
            }

            array_splice($items, $index, 1);
            $preparation->update(['additional_agenda_items' => array_values($items)]);
        });
    }

    /**
     * Remove/pull a board paper from the meeting agenda.
     */
    public function removePaper(TwwdaMeeting $meeting, int $paperId): void
    {
        DB::transaction(function () use ($meeting, $paperId) {
            $paper = TwwdaBoardPaper::where('id', $paperId)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $preparation = $this->getOrCreatePreparation($meeting);
            $removedPapers = $preparation->removed_papers ?? [];

            $removedPapers[] = [
                'paper_id' => $paperId,
                'title' => $paper->title,
                'removed_by' => Auth::id(),
                'removed_at' => now()->toIso8601String(),
            ];

            $preparation->update(['removed_papers' => $removedPapers]);

            $currentOrder = $preparation->paper_order ?? [];
            $updatedOrder = array_values(array_filter($currentOrder, fn($id) => $id !== $paperId));
            $preparation->update(['paper_order' => $updatedOrder]);

            $paper->update(['status' => 'removed_from_agenda']);

            TwwdaNotificationService::notify(
                $paper->prepared_by,
                'Board Paper Removed from Agenda',
                "Your board paper \"{$paper->title}\" has been removed from the agenda for meeting \"{$meeting->title}\".",
                'paper_removed',
                "/workflow/board-papers/{$paper->id}",
                $meeting->id
            );

            TwwdaAuditService::log(
                'paper_removed_from_agenda',
                'Stage 8: Meeting Preparation',
                TwwdaBoardPaper::class,
                $paperId,
                "Board paper removed from agenda: {$paper->title}",
                ['status' => $paper->getOriginal('status')],
                ['status' => 'removed_from_agenda'],
                $meeting->id
            );
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  FINALIZE
    // ═══════════════════════════════════════════════════════════════

    /**
     * Finalize meeting preparations, advancing to the next stage.
     */
    public function finalize(TwwdaMeeting $meeting): void
    {
        DB::transaction(function () use ($meeting) {
            // Verify no pending items
            $pendingCount = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->whereIn('status', ['pending_approval', 'pending_pulldown'])
                ->count();

            if ($pendingCount > 0) {
                throw new \RuntimeException("Cannot finalize: {$pendingCount} agenda item(s) are still pending CEO approval.");
            }

            $preparation = $this->getOrCreatePreparation($meeting);

            $preparation->update([
                'finalized_at' => now(),
            ]);

            $meeting->update([
                'status' => 'meeting_prepared',
                'current_stage' => 9,
            ]);

            $stakeholders = User::role(['CEO', 'Corporation Secretary'])->get();
            foreach ($stakeholders as $stakeholder) {
                TwwdaNotificationService::notify(
                    $stakeholder->id,
                    'Meeting Preparations Finalized',
                    "Preparations for meeting \"{$meeting->title}\" have been finalized. The meeting is ready to commence.",
                    'meeting_finalized',
                    "/workflow/meetings/{$meeting->id}",
                    $meeting->id,
                    $stakeholder->email
                );
            }

            TwwdaAuditService::log(
                'meeting_preparations_finalized',
                'Stage 8: Meeting Preparation',
                TwwdaMeeting::class,
                $meeting->id,
                "Meeting preparations finalized for: {$meeting->title}",
                ['status' => 'papers_approved'],
                ['status' => 'meeting_prepared', 'finalized_at' => now()->toIso8601String()],
                $meeting->id
            );
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Get or create the preparation record for a meeting.
     */
    public function getOrCreatePreparation(TwwdaMeeting $meeting): TwwdaMeetingPreparation
    {
        return TwwdaMeetingPreparation::firstOrCreate(
            ['meeting_id' => $meeting->id],
            [
                'prepared_by' => Auth::id(),
                'paper_order' => [],
                'additional_agenda_items' => [],
                'removed_papers' => [],
            ]
        );
    }
}
