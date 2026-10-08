<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\TwwdaAuditService;
use App\Http\Services\Twwda\TwwdaNotificationService;
use App\Mail\Twwda\BoardNoticeMail;
use App\Models\BoardMember;
use App\Models\Twwda\TwwdaBoardNotice;
use App\Models\Twwda\TwwdaBoardNoticeComment;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaOfficerAssignment;
use App\Models\BoardCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BoardNoticeWorkflowController extends Controller
{
    protected TwwdaNotificationService $notificationService;

    /** Audit committee IDs — these use DDIA self-approval (no CEO). */
    protected array $auditCommitteeIds;

    public function __construct(TwwdaNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
    }

    /**
     * Board Notices Hub — index page with stats.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            $query = TwwdaBoardNotice::with(['meeting.boardCommittee', 'creator'])
                ->orderByDesc('created_at');

            // Non-superadmin / non-CEO users see only their own notices
            if (!$user->hasAnyRole(['Superadmin', 'CEO'])) {
                $query->where('created_by', $user->id);
            }

            $notices = $query->get();

            $stats = [
                'total'    => $notices->count(),
                'pending'  => $notices->where('status', 'submitted')->count(),
                'approved' => $notices->where('status', 'approved')->count(),
                'sent'     => $notices->where('status', 'sent')->count(),
            ];

            // Load all initiated meetings so the user can create notices for any of them
            $meetingsWithNotices = $notices->pluck('meeting_id')->unique()->toArray();

            $availableMeetings = TwwdaMeeting::with('boardCommittee')
                ->whereNotIn('status', ['cancelled'])
                ->orderByDesc('meeting_date')
                ->get()
                ->map(function ($meeting) use ($meetingsWithNotices) {
                    $meeting->has_notice = in_array($meeting->id, $meetingsWithNotices);
                    return $meeting;
                });

            return view('workflow.board-notices.index', compact('notices', 'stats', 'availableMeetings'));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notices Index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board notices.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.dashboard');
        }
    }

    /**
     * Create board notice form.
     */
    public function create(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee.boardMembers', 'boardNotices']);

            $notice = $meeting->boardNotices->first();
            $boardMembers = optional($meeting->boardCommittee)->boardMembers ?? collect();
            $isAuditCommittee = (bool) optional($meeting->boardCommittee)->is_audit_committee;

            // Latest rejection comment
            $rejectionComment = null;
            if ($notice && $notice->status === 'rejected') {
                $rejectionComment = $notice->comments()->where('action', 'rejected')->first()?->comment;
            }

            // Default values for new notice
            $committeeName = optional($meeting->boardCommittee)->name ?? 'BOARD';
            $meetingDate = $meeting->meeting_date
                ? \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d F Y')
                : 'TBD';
            $meetingTime = $meeting->meeting_time ?? '10:00 AM';
            $venue = $meeting->venue ?? setting('default_venue', 'Headquarters');

            $defaultRefNumber = setting('reference_number_prefix', 'ORG') . '/BN/' . date('Y') . '/' . str_pad(
                TwwdaBoardNotice::whereYear('created_at', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT
            );

            $defaultSubject = "RE: NOTICE OF THE " . strtoupper($committeeName) . " COMMITTEE MEETING";

            $defaultBody = "Notice is hereby given that the {$committeeName} Committee Meeting of the " . setting('institution_name', 'Organization') . " (" . setting('institution_acronym', 'ORG') . ") will be held on <strong>{$meetingDate}</strong> at <strong>{$meetingTime}</strong> at <strong>{$venue}</strong>. All members are requested to attend.";

            // Pull agenda items from officer assignments for this meeting
            $assignmentAgendaItems = TwwdaOfficerAssignment::where('meeting_id', $meeting->id)
                ->orderBy('agenda_item_number')
                ->whereNotNull('agenda_item_title')
                ->where('agenda_item_title', '!=', '')
                ->pluck('agenda_item_title')
                ->unique()
                ->values()
                ->toArray();

            $defaultAgendaItems = array_merge(
                [
                    'Preliminary Matters',
                    'Confirmation of Previous Minutes',
                    'Matters Arising from Previous Minutes',
                ],
                $assignmentAgendaItems,
                [
                    'Any Other Business',
                ]
            );

            $canReorderAgenda = Auth::user()->hasAnyRole(['Corporation Secretary', 'DD Internal Audit']);

            return view('workflow.board-notices.create', compact(
                'meeting',
                'notice',
                'boardMembers',
                'isAuditCommittee',
                'rejectionComment',
                'defaultRefNumber',
                'defaultSubject',
                'defaultBody',
                'defaultAgendaItems',
                'canReorderAgenda',
                'assignmentAgendaItems'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice create error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board notice form.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Store a new board notice.
     */
    public function store(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'notice_type' => 'required|in:21_day,7_day',
            'reference_number' => 'required|string|max:100',
            'notice_date' => 'required|date',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'addressees' => 'nullable|array',
            'agenda_items' => 'nullable|array',
            'agenda_items.*' => 'string',
            'closing_text' => 'nullable|string|max:500',
            'cc' => 'nullable|array',
        ]);

        try {
            $isSelfApprove = $request->input('action') === 'self_approve';
            $isDraft = $request->input('action') === 'draft';

            $notice = DB::transaction(function () use ($request, $meeting, $isSelfApprove, $isDraft) {
                $isAudit = (bool) optional($meeting->boardCommittee)->is_audit_committee;

                $notice = TwwdaBoardNotice::create([
                    'meeting_id' => $meeting->id,
                    'notice_type' => $request->notice_type,
                    'reference_number' => $request->reference_number,
                    'reference_date' => $request->notice_date,
                    'notice_date' => $request->notice_date,
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'addressees' => $request->addressees,
                    'agenda_items' => array_values(array_filter($request->agenda_items ?? [])),
                    'closing_text' => $request->closing_text,
                    'cc' => $request->cc,
                    'status' => $isDraft ? 'draft' : ($isSelfApprove ? 'approved' : 'submitted'),
                    'requires_ceo_approval' => !$isAudit,
                    'created_by' => Auth::id(),
                    'submitted_at' => $isDraft ? null : now(),
                    'approved_by' => $isSelfApprove ? Auth::id() : null,
                    'approved_at' => $isSelfApprove ? now() : null,
                    'version' => 1,
                ]);

                TwwdaAuditService::log(
                    'board_notice_created',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $notice->id,
                    "Board notice created ({$request->notice_type}) for meeting: {$meeting->title}",
                    null,
                    $notice->toArray(),
                    $meeting->id
                );

                // If submitted (not draft, not self-approve), notify CEO
                if (!$isDraft && !$isSelfApprove) {
                    $ceoUsers = User::role('CEO')->get();
                    foreach ($ceoUsers as $ceo) {
                        TwwdaNotificationService::notify(
                            $ceo->id,
                            'Board Notice Awaiting Approval',
                            "A board notice ({$request->notice_type}) for \"{$meeting->title}\" is awaiting your approval.",
                            'board_notice_approval',
                            "/workflow/ceo/board-notices/{$notice->id}",
                            $meeting->id,
                            $ceo->email,
                            'workflow.emails.board-notice-submitted',
                            ['notice' => $notice]
                        );
                    }
                }

                return $notice;
            });

            $msg = $isDraft ? 'Board notice saved as draft.'
                : ($isSelfApprove ? 'Board notice approved and signed.' : 'Board notice submitted for CEO approval.');
            session()->flash('message', $msg);
            session()->flash('alert-type', 'success');

            return redirect()->route('workflow.board-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice store error: ' . $e->getMessage());
            session()->flash('message', 'Failed to create board notice.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Update an existing board notice.
     */
    public function update(Request $request, TwwdaBoardNotice $notice)
    {
        $request->validate([
            'reference_number' => 'sometimes|string|max:100',
            'notice_date' => 'sometimes|date',
            'notice_type' => 'sometimes|in:21_day,7_day',
            'subject' => 'sometimes|string|max:500',
            'body' => 'sometimes|string',
            'addressees' => 'nullable|array',
            'agenda_items' => 'nullable|array',
            'agenda_items.*' => 'string',
            'closing_text' => 'nullable|string|max:500',
            'cc' => 'nullable|array',
        ]);

        try {
            $isSelfApprove = $request->input('action') === 'self_approve';
            $isDraft = $request->input('action') === 'draft';

            $oldValues = $notice->only(['reference_number', 'subject', 'body', 'status']);

            $updateData = $request->only([
                'reference_number', 'notice_type', 'subject', 'body',
                'addressees', 'closing_text', 'cc',
            ]);

            if ($request->has('notice_date')) {
                $updateData['notice_date'] = $request->notice_date;
                $updateData['reference_date'] = $request->notice_date;
            }

            if ($request->has('agenda_items')) {
                $updateData['agenda_items'] = array_values(array_filter($request->agenda_items ?? []));
            }

            // Handle status based on action
            if ($isSelfApprove) {
                $updateData['status'] = 'approved';
                $updateData['approved_by'] = Auth::id();
                $updateData['approved_at'] = now();
                $updateData['submitted_at'] = now();
            } elseif (!$isDraft && $request->input('action') === 'submit') {
                $updateData['status'] = 'submitted';
                $updateData['submitted_at'] = now();
            }

            $notice->update($updateData);

            TwwdaAuditService::log(
                'board_notice_updated',
                'Board Notice',
                TwwdaBoardNotice::class,
                $notice->id,
                "Board notice updated: {$notice->subject}",
                $oldValues,
                $notice->only(['reference_number', 'subject', 'body', 'status']),
                $notice->meeting_id
            );

            // If submitted, notify CEO
            if (!$isDraft && !$isSelfApprove && $request->input('action') === 'submit') {
                $ceoUsers = User::role('CEO')->get();
                $meeting = $notice->meeting;
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Board Notice Awaiting Approval',
                        "A board notice for \"{$meeting->title}\" is awaiting your approval.",
                        'board_notice_approval',
                        "/workflow/ceo/board-notices/{$notice->id}",
                        $notice->meeting_id,
                        $ceo->email,
                        'workflow.emails.board-notice-submitted',
                        ['notice' => $notice]
                    );
                }
            }

            $msg = $isDraft ? 'Board notice saved as draft.'
                : ($isSelfApprove ? 'Board notice approved and signed.' : 'Board notice updated successfully.');
            session()->flash('message', $msg);
            session()->flash('alert-type', 'success');

            return redirect()->route('workflow.board-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice update error: ' . $e->getMessage());
            session()->flash('message', 'Failed to update board notice.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Submit board notice to CEO for approval.
     */
    public function submit(TwwdaBoardNotice $notice)
    {
        try {
            DB::transaction(function () use ($notice) {
                $notice->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                // Notify CEO
                $ceoUsers = User::role('CEO')->get();
                foreach ($ceoUsers as $ceo) {
                    TwwdaNotificationService::notify(
                        $ceo->id,
                        'Board Notice Awaiting Approval',
                        "A board notice ({$notice->notice_type}) for \"{$notice->meeting->title}\" is awaiting your approval.",
                        'board_notice_approval',
                        "/workflow/ceo/board-notices/{$notice->id}",
                        $notice->meeting_id,
                        $ceo->email,
                        'workflow.emails.board-notice-submitted',
                        ['notice' => $notice]
                    );
                }

                TwwdaAuditService::log(
                    'board_notice_submitted',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $notice->id,
                    "Board notice submitted for CEO approval",
                    null,
                    ['status' => 'submitted'],
                    $notice->meeting_id
                );
            });

            session()->flash('message', 'Board notice submitted for CEO approval.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $notice->meeting_id);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice submit error: ' . $e->getMessage());
            session()->flash('message', 'Failed to submit board notice.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Preview board notice with letterhead.
     */
    public function preview(TwwdaBoardNotice $notice)
    {
        try {
            $notice->load(['meeting.boardCommittee', 'creator', 'approver', 'comments']);

            // Resolve board members for addressee ID lookup
            $boardMembers = \App\Models\BoardMember::all();

            return view('workflow.board-notices.preview', compact('notice', 'boardMembers'));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice preview error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load notice preview.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Send a reminder to board members for a sent notice (with PDF attachment).
     */
    public function reminder(TwwdaBoardNotice $notice)
    {
        try {
            if ($notice->status !== 'sent') {
                session()->flash('message', 'Reminders can only be sent for notices that have already been sent.'); session()->flash('alert-type', 'error');
                return back();
            }

            DB::transaction(function () use ($notice) {
                $notice->update([
                    'reminder_sent_at' => now(),
                    'reminder_count' => ($notice->reminder_count ?? 0) + 1,
                ]);

                // Send reminder with PDF attachment
                $meeting = $notice->meeting()->with('boardCommittee')->first();
                $sentCount = 0;

                if ($meeting && $meeting->boardCommittee) {
                    $boardMembers = $meeting->boardCommittee->boardMembers ?? collect();

                    foreach ($boardMembers as $member) {
                        // In-system notification for members with user accounts
                        if ($member->user_id) {
                            TwwdaNotificationService::notify(
                                $member->user_id,
                                'Board Meeting Notice - Reminder',
                                "This is a reminder for the {$notice->notice_type} notice for meeting \"{$meeting->title}\".",
                                'board_notice_reminder',
                                "/board/portal/notices/{$notice->id}",
                                $meeting->id
                            );
                        }

                        // Send reminder email with PDF attachment
                        if ($member->email) {
                            try {
                                Mail::to($member->email)->send(
                                    new BoardNoticeMail($notice, $member, true)
                                );
                                $sentCount++;
                            } catch (\Exception $e) {
                                Log::warning("TWWDA Board Notice reminder email failed for {$member->email}: " . $e->getMessage());
                            }
                        }
                    }
                }

                TwwdaAuditService::log(
                    'board_notice_reminder',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $notice->id,
                    "Board notice reminder #{$notice->reminder_count} sent to {$sentCount} board member(s) with PDF attachment",
                    null,
                    ['reminder_count' => $notice->reminder_count, 'reminder_sent_at' => now()->toIso8601String(), 'emails_sent' => $sentCount],
                    $notice->meeting_id
                );
            });

            session()->flash('message', 'Reminder with PDF attachment sent to board members successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.board-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice reminder error: ' . $e->getMessage());
            session()->flash('message', 'Failed to send reminder.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Self-approve a board notice (for audit committee notices that don't require CEO approval).
     */
    public function selfApprove(Request $request, TwwdaBoardNotice $notice)
    {
        try {
            if ($notice->requires_ceo_approval) {
                session()->flash('message', 'This notice requires CEO approval and cannot be self-approved.'); session()->flash('alert-type', 'error');
                return back();
            }

            if (!in_array($notice->status, ['draft', 'rejected'])) {
                session()->flash('message', 'Only draft or rejected notices can be approved.'); session()->flash('alert-type', 'error');
                return back();
            }

            DB::transaction(function () use ($request, $notice) {
                $notice->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'submitted_at' => now(),
                    'ceo_signature' => $request->input('signature'),
                    'signatory_name' => $request->input('signatory_name', Auth::user()->name),
                    'signatory_title' => $request->input('signatory_title', setting('signatory_title_audit_head', 'DIRECTOR, INTERNAL AUDIT')),
                ]);

                TwwdaAuditService::log(
                    'board_notice_self_approved',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $notice->id,
                    "Board notice self-approved (audit committee)",
                    ['status' => $notice->getOriginal('status')],
                    ['status' => 'approved'],
                    $notice->meeting_id
                );
            });

            session()->flash('message', 'Board notice approved and signed successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.board-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice self-approve error: ' . $e->getMessage());
            session()->flash('message', 'Failed to approve board notice.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Send approved board notice to board members (with PDF attachment).
     */
    public function send(TwwdaBoardNotice $notice)
    {
        try {
            if ($notice->status !== 'approved') {
                session()->flash('message', 'Board notice must be approved before sending.'); session()->flash('alert-type', 'error');
                return back();
            }

            DB::transaction(function () use ($notice) {
                $notice->update([
                    'sent_at' => now(),
                    'sent_by' => Auth::id(),
                    'status' => 'sent',
                ]);

                // Send to board members with PDF attachment
                $meeting = $notice->meeting()->with('boardCommittee')->first();
                $sentCount = 0;

                if ($meeting && $meeting->boardCommittee) {
                    $boardMembers = $meeting->boardCommittee->boardMembers ?? collect();

                    foreach ($boardMembers as $member) {
                        // Create in-system notification for members with user accounts
                        if ($member->user_id) {
                            TwwdaNotificationService::notify(
                                $member->user_id,
                                'Board Meeting Notice',
                                "You have received a {$notice->notice_type} notice for meeting \"{$meeting->title}\".",
                                'board_notice_sent',
                                "/board/portal/notices/{$notice->id}",
                                $meeting->id
                            );
                        }

                        // Send email with PDF attachment to all members with email
                        if ($member->email) {
                            try {
                                Mail::to($member->email)->send(
                                    new BoardNoticeMail($notice, $member, false)
                                );
                                $sentCount++;
                            } catch (\Exception $e) {
                                Log::warning("TWWDA Board Notice email failed for {$member->email}: " . $e->getMessage());
                            }
                        }
                    }
                }

                TwwdaAuditService::log(
                    'board_notice_sent',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $notice->id,
                    "Board notice sent to {$sentCount} board member(s) with PDF attachment",
                    null,
                    ['status' => 'sent', 'sent_at' => now()->toIso8601String(), 'emails_sent' => $sentCount],
                    $notice->meeting_id
                );
            });

            session()->flash('message', 'Board notice sent to board members with PDF attachment.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.board-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice send error: ' . $e->getMessage());
            session()->flash('message', 'Failed to send board notice.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Reschedule — create a new notice linked to the original via parent_notice_id.
     */
    public function reschedule(Request $request, TwwdaBoardNotice $notice)
    {
        try {
            $meeting = $notice->meeting;
            if (!$meeting) {
                session()->flash('message', 'Meeting not found for this notice.'); session()->flash('alert-type', 'error');
                return back();
            }

            $newNotice = DB::transaction(function () use ($notice, $meeting) {
                $isAudit = in_array($meeting->board_committee_id, $this->auditCommitteeIds);

                $newNotice = TwwdaBoardNotice::create([
                    'meeting_id'           => $meeting->id,
                    'notice_type'          => $notice->notice_type,
                    'reference_number'     => $notice->reference_number . '-R',
                    'reference_date'       => now(),
                    'notice_date'          => now(),
                    'subject'              => $notice->subject,
                    'body'                 => $notice->body,
                    'addressees'           => $notice->addressees,
                    'agenda_items'         => $notice->agenda_items,
                    'closing_text'         => $notice->closing_text,
                    'cc'                   => $notice->cc,
                    'status'               => 'draft',
                    'requires_ceo_approval'=> !$isAudit,
                    'created_by'           => Auth::id(),
                    'version'              => ($notice->version ?? 1) + 1,
                    'parent_notice_id'     => $notice->id,
                ]);

                TwwdaAuditService::log(
                    'board_notice_rescheduled',
                    'Board Notice',
                    TwwdaBoardNotice::class,
                    $newNotice->id,
                    "New notice created from rescheduled meeting. Parent notice #{$notice->id}",
                    null,
                    ['parent_notice_id' => $notice->id, 'version' => $newNotice->version],
                    $meeting->id
                );

                return $newNotice;
            });

            session()->flash('message', 'New rescheduled notice created. Please update the details and submit.');
            session()->flash('alert-type', 'success');

            return redirect()->route('workflow.board-notice.create', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice reschedule error: ' . $e->getMessage());
            session()->flash('message', 'Failed to reschedule notice.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Audit trail / history for a board notice.
     */
    public function history(TwwdaBoardNotice $notice)
    {
        try {
            $notice->load(['meeting.boardCommittee', 'creator', 'approver', 'sender', 'comments.user', 'parentNotice', 'replacementNotices']);

            // Load audit logs for this notice
            $auditLogs = \App\Models\Twwda\TwwdaAuditLog::where('entity_type', TwwdaBoardNotice::class)
                ->where('entity_id', $notice->id)
                ->orderByDesc('created_at')
                ->get();

            return view('workflow.board-notices.history', compact('notice', 'auditLogs'));
        } catch (\Exception $e) {
            Log::error('TWWDA Board Notice history error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load notice history.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * CEO's pending board notices list.
     */
    public function ceoPending()
    {
        try {
            $notices = TwwdaBoardNotice::pending()
                ->with(['meeting.boardCommittee', 'creator'])
                ->orderBy('submitted_at', 'desc')
                ->paginate(15);

            return view('workflow.board-notices.ceo-pending', compact('notices'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Board Notices error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load pending board notices.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * CEO review page for a specific board notice.
     */
    public function ceoReview(TwwdaBoardNotice $notice)
    {
        try {
            $notice->load(['meeting.boardCommittee', 'creator', 'comments']);

            return view('workflow.board-notices.ceo-review', compact('notice'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Board Notice Review error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load board notice for review.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.board-notices');
        }
    }

    /**
     * CEO approves or rejects a board notice.
     */
    public function ceoDecision(Request $request, TwwdaBoardNotice $notice)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'comment' => 'required_if:decision,reject|nullable|string|max:1000',
            'signature' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $notice) {
                if ($request->decision === 'approve') {
                    $notice->update([
                        'status' => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'ceo_signature' => $request->signature,
                        'signatory_name' => $request->input('signatory_name', Auth::user()->name),
                        'signatory_title' => $request->input('signatory_title', setting('signatory_title_ceo', 'CHIEF EXECUTIVE OFFICER')),
                    ]);

                    // Notify the creator
                    TwwdaNotificationService::notify(
                        $notice->created_by,
                        'Board Notice Approved',
                        "Your board notice for \"{$notice->meeting->title}\" has been approved by the CEO. You may now send it to board members.",
                        'board_notice_approved',
                        "/workflow/board-notices/{$notice->id}/preview",
                        $notice->meeting_id,
                        $notice->creator->email ?? null,
                        'workflow.emails.board-notice-approved',
                        ['notice' => $notice]
                    );

                    TwwdaAuditService::log(
                        'board_notice_approved',
                        'Board Notice',
                        TwwdaBoardNotice::class,
                        $notice->id,
                        "Board notice approved by CEO",
                        ['status' => 'submitted'],
                        ['status' => 'approved'],
                        $notice->meeting_id
                    );
                } else {
                    $notice->update([
                        'status' => 'rejected',
                    ]);

                    TwwdaBoardNoticeComment::create([
                        'board_notice_id' => $notice->id,
                        'user_id' => Auth::id(),
                        'comment' => $request->comment,
                        'action' => 'rejected',
                    ]);

                    // Notify the creator
                    TwwdaNotificationService::notify(
                        $notice->created_by,
                        'Board Notice Rejected',
                        "Your board notice for \"{$notice->meeting->title}\" has been rejected. Please review the comments and resubmit.",
                        'board_notice_rejected',
                        "/workflow/meetings/{$notice->meeting_id}/board-notice",
                        $notice->meeting_id,
                        $notice->creator->email ?? null,
                        'workflow.emails.board-notice-rejected',
                        ['notice' => $notice, 'comment' => $request->comment]
                    );

                    TwwdaAuditService::log(
                        'board_notice_rejected',
                        'Board Notice',
                        TwwdaBoardNotice::class,
                        $notice->id,
                        "Board notice rejected by CEO. Comments: {$request->comment}",
                        ['status' => 'submitted'],
                        ['status' => 'rejected'],
                        $notice->meeting_id
                    );
                }
            });

            if ($request->decision === 'approve') {
                session()->flash('message', 'Board notice approved successfully.'); session()->flash('alert-type', 'success');
            } else {
                session()->flash('message', 'Board notice rejected. The creator has been notified.'); session()->flash('alert-type', 'success');
            }

            return redirect()->route('workflow.ceo.board-notices');
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Board Notice Decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process decision.'); session()->flash('alert-type', 'error');
            return back();
        }
    }
}
