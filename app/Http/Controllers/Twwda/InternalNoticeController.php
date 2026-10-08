<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\InternalNoticeService;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaInternalNotice;
use App\Models\Twwda\TwwdaMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InternalNoticeController extends Controller
{
    protected InternalNoticeService $noticeService;

    public function __construct(InternalNoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * Show form with existing notice if any.
     */
    public function create(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee', 'internalNotice.comments']);
            $notice = $meeting->internalNotice;
            $comments = $notice ? $notice->comments : collect();

            return view('workflow.internal-notices.create', compact('meeting', 'notice', 'comments'));
        } catch (\Exception $e) {
            Log::error('TWWDA Internal Notice create error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load internal notice form.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Store internal notice via InternalNoticeService.
     */
    public function store(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'reference_number' => 'required|string|max:100',
            'reference_date' => 'required|date',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'agenda_items' => 'nullable|array',
            'agenda_items.*' => 'string',
            'meeting_venue' => 'nullable|string|max:255',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable|string|max:10',
        ]);

        try {
            $notice = $this->noticeService->store($request, $meeting);

            // If user clicked "Submit to CEO", save then submit in one step
            if ($request->input('action') === 'submit') {
                $this->noticeService->submit($notice);

                session()->flash('message', 'Internal notice submitted for CEO approval.'); session()->flash('alert-type', 'success');
                return redirect()->route('workflow.meetings.show', $meeting);
            }

            session()->flash('message', 'Internal notice saved successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.internal-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Internal Notice store error: ' . $e->getMessage());
            session()->flash('message', 'Failed to save internal notice.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Update an existing internal notice.
     */
    public function update(Request $request, TwwdaInternalNotice $notice)
    {
        $request->validate([
            'reference_number' => 'sometimes|string|max:100',
            'reference_date' => 'sometimes|date',
            'subject' => 'sometimes|string|max:500',
            'body' => 'sometimes|string',
            'agenda_items' => 'nullable|array',
            'agenda_items.*' => 'string',
            'meeting_venue' => 'nullable|string|max:255',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable|string|max:10',
        ]);

        try {
            $notice->update($request->only([
                'reference_number', 'reference_date', 'subject', 'body',
                'agenda_items', 'meeting_venue', 'meeting_date', 'meeting_time',
            ]));

            // If user clicked "Submit to CEO", save the notice then submit it
            if ($request->input('action') === 'submit') {
                $this->noticeService->submit($notice);

                session()->flash('message', 'Internal notice submitted for CEO approval.'); session()->flash('alert-type', 'success');
                return redirect()->route('workflow.meetings.show', $notice->meeting_id);
            }

            session()->flash('message', 'Internal notice updated successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.internal-notice.preview', $notice);
        } catch (\Exception $e) {
            Log::error('TWWDA Internal Notice update error: ' . $e->getMessage());
            session()->flash('message', 'Failed to update internal notice.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Submit internal notice to CEO for approval.
     */
    public function submit(TwwdaInternalNotice $notice)
    {
        try {
            $this->noticeService->submit($notice);

            session()->flash('message', 'Internal notice submitted for CEO approval.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $notice->meeting_id);
        } catch (\Exception $e) {
            Log::error('TWWDA Internal Notice submit error: ' . $e->getMessage());
            session()->flash('message', 'Failed to submit internal notice.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Preview notice as official document with letterhead.
     */
    public function preview(TwwdaInternalNotice $notice)
    {
        try {
            $notice->load(['meeting.boardCommittee', 'creator', 'approver', 'comments']);

            return view('workflow.internal-notices.show', compact('notice'));
        } catch (\Exception $e) {
            Log::error('TWWDA Internal Notice preview error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load notice preview.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * List internal notices for review (CEO sees all; DDIA sees audit committee only).
     */
    public function ceoPending(Request $request)
    {
        try {
            $user = auth()->user();
            $tab = $request->get('tab', 'pending');

            // DDIA only sees audit committee notices
            $isDDIA = $user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']);
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();

            $query = TwwdaInternalNotice::with(['meeting.boardCommittee', 'creator', 'approver']);

            if ($isDDIA) {
                $query->whereHas('meeting', fn($q) => $q->whereIn('board_committee_id', $auditCommitteeIds));
            }

            switch ($tab) {
                case 'approved':
                    $query->whereIn('status', ['approved', 'special_approved'])->orderBy('approved_at', 'desc');
                    break;
                case 'rejected':
                    $query->where('status', 'rejected')->orderBy('updated_at', 'desc');
                    break;
                case 'all':
                    $query->orderBy('updated_at', 'desc');
                    break;
                default: // pending
                    $query->where('status', 'submitted')->orderBy('submitted_at', 'desc');
                    break;
            }

            $notices = $query->paginate(15)->appends(['tab' => $tab]);

            // Counts scoped the same way
            $countQuery = fn() => $isDDIA
                ? TwwdaInternalNotice::whereHas('meeting', fn($q) => $q->whereIn('board_committee_id', $auditCommitteeIds))
                : new TwwdaInternalNotice;

            $counts = [
                'pending' => $countQuery()->where('status', 'submitted')->count(),
                'approved' => $countQuery()->whereIn('status', ['approved', 'special_approved'])->count(),
                'rejected' => $countQuery()->where('status', 'rejected')->count(),
                'all' => $countQuery()->count(),
            ];

            return view('workflow.internal-notices.ceo-pending', compact('notices', 'tab', 'counts'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Pending Notices error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load notices.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Review page for a specific internal notice.
     * CEO can review all; DDIA can review audit committee notices only.
     */
    public function ceoReview(TwwdaInternalNotice $notice)
    {
        try {
            $notice->load(['meeting.boardCommittee', 'creator', 'approver', 'comments']);

            // DDIA can only review audit committee notices
            $user = auth()->user();
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAudit = $notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds);
            if ($user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']) && !$isAudit) {
                session()->flash('message', 'You can only review Audit Committee notices.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.ceo.internal-notices');
            }

            // If the notice is already approved/rejected, show read-only detail view
            if (in_array($notice->status, ['approved', 'special_approved', 'rejected'])) {
                return view('workflow.internal-notices.ceo-notice-detail', compact('notice'));
            }

            return view('workflow.internal-notices.ceo-review', compact('notice'));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Review error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load notice for review.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.internal-notices');
        }
    }

    /**
     * Approve or reject the internal notice.
     * CEO handles all committees; DDIA handles audit committee (id=4) only.
     */
    public function ceoDecision(Request $request, TwwdaInternalNotice $notice)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'comment' => 'required_if:decision,reject|nullable|string|max:1000',
            'ceo_signature' => 'nullable|string',
        ]);

        try {
            // Guard: DDIA can only decide on audit committee notices
            $user = auth()->user();
            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAudit = $notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds);
            if ($user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['CEO', 'Superadmin']) && !$isAudit) {
                session()->flash('message', 'You can only approve/reject Audit Committee notices.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.ceo.internal-notices');
            }

            if ($request->decision === 'approve') {
                $this->noticeService->approve($notice, $request->ceo_signature);
                session()->flash('message', 'Internal notice approved successfully.'); session()->flash('alert-type', 'success');
            } else {
                $this->noticeService->reject($notice, $request->comment);
                session()->flash('message', 'Internal notice rejected. The creator has been notified.'); session()->flash('alert-type', 'success');
            }

            return redirect()->route('workflow.ceo.internal-notices');
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process decision.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Special approval when CEO is unavailable.
     */
    public function specialApprove(Request $request, TwwdaInternalNotice $notice)
    {
        // Only Corporation Secretary or CS-Assistant may special-approve
        if (!auth()->user()->hasAnyRole(['Corporation Secretary', 'CS-Assistant'])) {
            session()->flash('message', 'You are not authorized to perform a special approval.'); session()->flash('alert-type', 'error');
            return back();
        }

        // Guard: only submitted notices can be special-approved
        if ($notice->status !== 'submitted') {
            session()->flash('message', 'This notice is not in a state that allows special approval.'); session()->flash('alert-type', 'error');
            return back();
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $this->noticeService->specialApprove($notice, $request->reason);

            session()->flash('message', 'Internal notice approved with special approval.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $notice->meeting_id);
        } catch (\Exception $e) {
            Log::error('TWWDA Special Approve error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process special approval.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Resubmit internal notice after rejection.
     */
    public function resubmit(Request $request, TwwdaInternalNotice $notice)
    {
        $request->validate([
            'subject' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'agenda_items' => 'nullable|array',
            'agenda_items.*' => 'string',
            'resubmission_comment' => 'nullable|string|max:1000',
        ]);

        try {
            $this->noticeService->resubmit($notice, $request);

            session()->flash('message', 'Internal notice resubmitted for CEO approval.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $notice->meeting_id);
        } catch (\Exception $e) {
            Log::error('TWWDA Resubmit error: ' . $e->getMessage());
            session()->flash('message', 'Failed to resubmit internal notice.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }
}
