<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\MeetingWorkflowService;
use App\Models\Almanac;
use App\Models\AlmanacSchedule;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaAuditLog;
use App\Models\Twwda\TwwdaMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingWorkflowController extends Controller
{
    protected MeetingWorkflowService $workflowService;

    public function __construct(MeetingWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Show almanac schedules grouped by committee with analytics.
     * Displays which schedules already have twwda_meetings and initiation buttons.
     */
    public function almanac()
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();

            // Load the active almanac with its schedules grouped by committee
            $activeAlmanac = Almanac::where('status', 'Approved')
                ->latest()
                ->first();

            $allSchedules = collect();
            if ($activeAlmanac) {
                $query = AlmanacSchedule::where('almanac_id', $activeAlmanac->id)
                    ->with(['boardCommittee'])
                    ->orderBy('meeting_date');

                // DD Internal Audit only sees audit committee meetings
                if ($user->hasRole('DD Internal Audit') && !$user->hasAnyRole(['Corporation Secretary', 'Superadmin'])) {
                    $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
                    $query->whereIn('board_committee_id', $auditCommitteeIds);
                }

                $allSchedules = $query->get();
            }

            // Check which schedules already have twwda_meetings — keyed by schedule ID
            $existingMeetings = TwwdaMeeting::whereIn('almanac_schedule_id', $allSchedules->pluck('id'))
                ->get()
                ->keyBy('almanac_schedule_id');

            // Build canInitiate map per schedule
            $canInitiate = [];
            foreach ($allSchedules as $schedule) {
                $canInitiate[$schedule->id] = $this->workflowService->canInitiate($user, $schedule->boardCommittee);
            }

            // Group schedules by committee name
            $schedules = $allSchedules->groupBy(function ($schedule) {
                return $schedule->boardCommittee->name ?? 'Unknown';
            });

            // Analytics
            $initiatedCount = $allSchedules->filter(fn($s) => $existingMeetings->has($s->id))->count();
            $stats = [
                'total' => $allSchedules->count(),
                'initiated' => $initiatedCount,
                'pending' => $allSchedules->count() - $initiatedCount,
                'by_committee' => $schedules->count(),
            ];

            return view('workflow.meetings.almanac', compact(
                'activeAlmanac',
                'schedules',
                'existingMeetings',
                'canInitiate',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Almanac error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load almanac schedules.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Initiate a meeting from an almanac schedule.
     */
    public function initiate(AlmanacSchedule $schedule)
    {
        try {
            // Check if meeting already initiated for this schedule
            $existing = TwwdaMeeting::where('almanac_schedule_id', $schedule->id)->first();
            if ($existing) {
                session()->flash('message', 'A meeting has already been initiated for this schedule.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.meetings.show', $existing);
            }

            $meeting = $this->workflowService->initiate($schedule);

            session()->flash('message', 'Meeting initiated successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meetings.show', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Meeting initiation error: ' . $e->getMessage());
            session()->flash('message', 'Failed to initiate meeting: ' . $e->getMessage()); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.almanac');
        }
    }

    /**
     * Meeting detail page with all relationships loaded.
     */
    public function show(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load([
                'boardCommittee',
                'initiator',
                'almanacSchedule',
                'internalNotice',
                'officerAssignments.user',
                'officerAssignments.director',
                'boardPapers.author',
                'boardPapers.annexures',
                'boardPapers.comments.user',
                'boardPapers.directorApprover',
                'boardPapers.assignment.director',
                'preparation',
                'attendance',
                'conflictDeclarations',
                'boardNotices',
                'auditLogs' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                },
            ]);

            return view('workflow.meetings.show', compact('meeting'));
        } catch (\Exception $e) {
            Log::error('TWWDA Meeting show error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load meeting details.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.almanac');
        }
    }

    /**
     * Full audit log timeline for a meeting.
     */
    public function timeline(TwwdaMeeting $meeting)
    {
        try {
            $auditLogs = TwwdaAuditLog::where('meeting_id', $meeting->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            return view('workflow.meetings.timeline', compact('meeting', 'auditLogs'));
        } catch (\Exception $e) {
            Log::error('TWWDA Timeline error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load meeting timeline.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }
}
