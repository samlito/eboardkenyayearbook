<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\OfficerAssignmentService;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaOfficerAssignment;
use App\Models\Twwda\TwwdaUserDirectorateMapping;
use App\Models\BoardCommittee;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfficerAssignmentController extends Controller
{
    protected OfficerAssignmentService $assignmentService;

    public function __construct(OfficerAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Show assignment page with list of directors/DDs.
     * For audit committee meetings (board_committee_id=4): only DD Internal Audit + Division Heads in Internal Audit Division.
     * Corporation Secretary users are always included (they can also add board papers).
     */
    public function index(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee', 'internalNotice', 'officerAssignments.user', 'officerAssignments.director']);

            $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
            $isAuditCommittee = in_array($meeting->board_committee_id, $auditCommitteeIds);

            if ($isAuditCommittee) {
                // ── Audit committee: only DD Internal Audit + Division Heads in Internal Audit Division ──
                $internalAuditDivisionId = Division::where('name', 'Internal Audit Division')->value('id');

                // DD Internal Audit users
                $ddiaUsers = User::role('DD Internal Audit')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($user) {
                        $user->_officer_type = 'ddia';
                        return $user;
                    });

                // Division Heads in Internal Audit Division
                $auditDivisionHeads = User::role('Division Head')
                    ->with(['division'])
                    ->when($internalAuditDivisionId, function ($q) use ($internalAuditDivisionId) {
                        $q->where('division_id', $internalAuditDivisionId);
                    })
                    ->orderBy('name')
                    ->get()
                    ->map(function ($user) {
                        $user->_officer_type = 'division_head';
                        return $user;
                    });

                $users = $ddiaUsers->merge($auditDivisionHeads);
            } else {
                // ── Non-audit committees: original logic ──
                // Directorates whose Directors prepare papers directly (no division-level papers)
                $directPaperDirectorateIds = [1, 2, 3]; // Infra Planning & Design, Infra Development, Bulk Water Services

                // Get division IDs under those 3 directorates (these DH are excluded)
                $excludedDivisionIds = Division::whereIn('directorate_id', $directPaperDirectorateIds)
                    ->pluck('id')
                    ->toArray();

                // 1. Division Heads NOT under the 3 excluded directorates
                $divisionHeads = User::role('Division Head')
                    ->with(['division.directorate'])
                    ->where(function ($q) use ($excludedDivisionIds) {
                        $q->whereNull('division_id')
                          ->orWhereNotIn('division_id', $excludedDivisionIds);
                    })
                    ->orderBy('name')
                    ->get()
                    ->map(function ($user) {
                        $user->_officer_type = 'division_head';
                        return $user;
                    });

                // 2. Directors from the 3 special directorates (via active mapping)
                $directorMappings = TwwdaUserDirectorateMapping::whereIn('directorate_id', $directPaperDirectorateIds)
                    ->active()
                    ->directors()
                    ->with(['user', 'directorate'])
                    ->get();

                $directors = $directorMappings->map(function ($mapping) {
                    $user = $mapping->user;
                    $user->_officer_type = 'director';
                    $user->_directorate_name = $mapping->directorate->name ?? '';
                    $user->_directorate_id = $mapping->directorate_id;
                    return $user;
                })->unique('id');

                // Combine: directors first, then division heads
                $users = $directors->merge($divisionHeads);
            }

            // ── Always include Corporation Secretary users (they can also add board papers) ──
            $csUsers = User::role('Corporation Secretary')
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    $user->_officer_type = 'corporation_secretary';
                    return $user;
                });

            // Merge CS users, avoiding duplicates
            $existingIds = $users->pluck('id')->toArray();
            $csUsers = $csUsers->filter(fn($u) => !in_array($u->id, $existingIds));
            $users = $users->merge($csUsers);

            // Already-assigned officers for this meeting
            $existingAssignments = $meeting->officerAssignments;

            return view('workflow.assignments.index', compact('meeting', 'users', 'existingAssignments', 'isAuditCommittee'));
        } catch (\Exception $e) {
            Log::error('TWWDA Assignment index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load assignment page.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Store officer assignments.
     */
    public function store(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.agenda_item_number' => 'required|string|max:50',
            'assignments.*.agenda_item_title' => 'required|string|max:500',
        ]);

        try {
            $this->assignmentService->assign(
                $meeting,
                $request->assignments,
                Auth::id()
            );

            session()->flash('message', 'Officers assigned successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.assignments.dashboard', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Assignment store error: ' . $e->getMessage());
            session()->flash('message', 'Failed to assign officers: ' . $e->getMessage()); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Add additional officers to an existing meeting.
     */
    public function addMore(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.agenda_item_number' => 'required|string|max:50',
            'assignments.*.agenda_item_title' => 'required|string|max:500',
        ]);

        try {
            $this->assignmentService->assign(
                $meeting,
                $request->assignments,
                Auth::id()
            );

            session()->flash('message', 'Additional officers assigned successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.assignments.dashboard', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Add More Officers error: ' . $e->getMessage());
            session()->flash('message', 'Failed to add additional officers.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Assignment status dashboard with read receipts.
     */
    public function dashboard(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee']);

            $assignments = TwwdaOfficerAssignment::where('meeting_id', $meeting->id)
                ->with(['user.division.directorate', 'director', 'assigner', 'boardPapers'])
                ->orderBy('agenda_item_number')
                ->get();

            // Summary statistics
            $stats = [
                'total' => $assignments->count(),
                'read' => $assignments->filter(fn($a) => $a->isRead())->count(),
                'unread' => $assignments->filter(fn($a) => !$a->isRead())->count(),
                'papers_submitted' => $assignments->flatMap(fn($a) => $a->boardPapers)->count(),
            ];

            return view('workflow.assignments.dashboard', compact('meeting', 'assignments', 'stats'));
        } catch (\Exception $e) {
            Log::error('TWWDA Assignment dashboard error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load assignment dashboard.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Mark an assignment as read by the assigned officer.
     */
    public function markRead(TwwdaOfficerAssignment $assignment)
    {
        try {
            $assignment->update([
                'system_read_at' => now(),
            ]);

            session()->flash('message', 'Assignment marked as read.'); session()->flash('alert-type', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('TWWDA Assignment markRead error: ' . $e->getMessage());
            session()->flash('message', 'Failed to mark assignment as read.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Officer-facing: List all assignments for the logged-in officer.
     */
    public function myAssignments()
    {
        try {
            $user = Auth::user();

            $assignments = TwwdaOfficerAssignment::where('user_id', $user->id)
                ->with(['meeting.boardCommittee', 'meeting.internalNotice.approver', 'assigner', 'boardPapers'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Group by meeting for better display
            $groupedByMeeting = $assignments->groupBy('meeting_id');

            // Stats
            $stats = [
                'total' => $assignments->count(),
                'unread' => $assignments->filter(fn($a) => !$a->isRead())->count(),
                'papers_submitted' => $assignments->filter(fn($a) => $a->boardPapers->count() > 0)->count(),
                'pending' => $assignments->filter(fn($a) => $a->boardPapers->count() === 0)->count(),
            ];

            return view('workflow.officer.my-assignments', compact('assignments', 'groupedByMeeting', 'stats'));
        } catch (\Exception $e) {
            Log::error('TWWDA Officer My Assignments error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load your assignments.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.dashboard');
        }
    }

    /**
     * Officer-facing: View a single assignment detail with mark-as-read.
     */
    public function showAssignment(TwwdaOfficerAssignment $assignment)
    {
        try {
            $assignment->load(['meeting.boardCommittee', 'meeting.internalNotice.approver', 'assigner', 'director', 'boardPapers.annexures']);

            // Auto-mark as read when the assigned officer views it
            if ($assignment->user_id === Auth::id() && !$assignment->system_read_at) {
                $assignment->update(['system_read_at' => now()]);
                $assignment->refresh();
            }

            return view('workflow.officer.assignment-detail', compact('assignment'));
        } catch (\Exception $e) {
            Log::error('TWWDA Officer Assignment Detail error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load assignment details.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.officer.assignments');
        }
    }
}
