<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\TwwdaNotificationService;
use App\Models\Almanac;
use App\Models\BoardCommittee;
use App\Models\Twwda\TwwdaBoardNotice;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaInternalNotice;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaNotification;
use App\Models\Twwda\TwwdaAuditLog;
use App\Models\Twwda\TwwdaOfficerAssignment;
use App\Models\Twwda\TwwdaUserDirectorateMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwwdaDashboardController extends Controller
{
    protected TwwdaNotificationService $notificationService;

    public function __construct(TwwdaNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Main V2 dashboard with meeting counts by status, upcoming meetings, and recent activity.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // ── User profile context (division, directorate, reporting director) ─
            $userDivision = $user->division;
            $userDirectorate = $userDivision?->directorate;
            $userDirector = null;
            $reportsDirectlyToCeo = true;

            if ($userDirectorate) {
                $dirMapping = TwwdaUserDirectorateMapping::where('directorate_id', $userDirectorate->id)
                    ->where('is_active', true)
                    ->where('role_type', 'director')
                    ->with('user')
                    ->first();

                if ($dirMapping && $dirMapping->user_id !== $user->id) {
                    $userDirector = $dirMapping->user;
                    $reportsDirectlyToCeo = false;
                }
            }

            // Meeting counts by status
            $meetingCounts = TwwdaMeeting::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Upcoming meetings
            $upcomingMeetings = TwwdaMeeting::upcoming()
                ->with(['boardCommittee', 'initiator'])
                ->limit(10)
                ->get();

            // Recent activity (audit logs) — scoped by role
            $isAdminUser = $user->hasAnyRole(['Corporation Secretary', 'CS-Assistant', 'DD Internal Audit']);
            if ($isAdminUser) {
                $recentActivity = TwwdaAuditLog::with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
            } else {
                $userMeetingIds = $this->getUserMeetingIds($user);
                $recentActivity = TwwdaAuditLog::with('user')
                    ->where(function ($q) use ($user, $userMeetingIds) {
                        $q->where('user_id', $user->id)
                          ->orWhereIn('meeting_id', $userMeetingIds);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
            }

            // Unread notifications count + recent notifications
            $unreadCount = TwwdaNotification::forUser($user->id)->unread()->count();
            $notifications = TwwdaNotification::forUser($user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // ── Board Papers analytics ────────────────────────────────
            $paperCounts = TwwdaBoardPaper::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $totalPapers = array_sum($paperCounts);

            // ── Internal Notices analytics ────────────────────────────
            $noticeCounts = TwwdaInternalNotice::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // ── Meetings by committee ─────────────────────────────────
            $meetingsByCommittee = TwwdaMeeting::select('board_committee_id', DB::raw('COUNT(*) as count'))
                ->groupBy('board_committee_id')
                ->with('boardCommittee')
                ->get()
                ->map(fn($m) => [
                    'name' => $m->boardCommittee->name ?? 'Unassigned',
                    'count' => $m->count,
                ]);

            // ── Meetings by month (last 12 months) ───────────────────
            $meetingsByMonth = TwwdaMeeting::select(
                    DB::raw("DATE_FORMAT(meeting_date, '%Y-%m') as month"),
                    DB::raw('COUNT(*) as count')
                )
                ->where('meeting_date', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            // ── Workflow stage distribution ────────────────────────────
            $stageCounts = TwwdaMeeting::selectRaw('current_stage, COUNT(*) as count')
                ->groupBy('current_stage')
                ->pluck('count', 'current_stage')
                ->toArray();

            // ── Officer assignments stats ─────────────────────────────
            $assignmentStats = [
                'total' => TwwdaOfficerAssignment::count(),
                'with_papers' => TwwdaOfficerAssignment::whereHas('boardPapers')->count(),
            ];

            // ── Board Notices analytics ───────────────────────────────
            $boardNoticeCounts = TwwdaBoardNotice::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // ── CEO-specific pending action items ──────────────────────
            $isCeo = $user->hasRole('CEO');
            $ceoPending = [];

            if ($isCeo) {
                // 1. Almanac approvals (V1 tables)
                $pendingAlmanacs = Almanac::where('status', 'Awaiting Approval')
                    ->with('financialyear')
                    ->latest()
                    ->get();

                // 2. Internal notices pending CEO approval
                $pendingInternalNotices = TwwdaInternalNotice::where('status', 'submitted')
                    ->with(['meeting.boardCommittee', 'creator'])
                    ->orderBy('submitted_at', 'desc')
                    ->get();

                // 3. Board papers pending CEO review
                $pendingBoardPapers = TwwdaBoardPaper::pendingCeoReview()
                    ->with(['meeting.boardCommittee', 'author', 'assignment.user'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                // 4. Board notices pending CEO approval
                $pendingBoardNotices = TwwdaBoardNotice::pending()
                    ->with(['meeting.boardCommittee', 'creator'])
                    ->orderBy('submitted_at', 'desc')
                    ->get();

                $ceoPending = [
                    'almanacs' => $pendingAlmanacs,
                    'internal_notices' => $pendingInternalNotices,
                    'board_papers' => $pendingBoardPapers,
                    'board_notices' => $pendingBoardNotices,
                    'total' => $pendingAlmanacs->count() + $pendingInternalNotices->count()
                        + $pendingBoardPapers->count() + $pendingBoardNotices->count(),
                ];
            }

            // ── Director-specific pending action items ────────────────
            $isDirector = $user->hasRole('Director');
            $directorPending = [];

            if ($isDirector) {
                // Get assignments where the current user is the director
                $directorAssignmentIds = TwwdaOfficerAssignment::where('director_id', $user->id)
                    ->pluck('id');

                // Papers pending director review
                $dirPendingPapers = TwwdaBoardPaper::whereIn('assignment_id', $directorAssignmentIds)
                    ->pendingDirectorReview()
                    ->with(['meeting.boardCommittee', 'author', 'assignment.user'])
                    ->orderBy('submitted_at', 'desc')
                    ->get();

                // Papers already approved by this director
                $dirApprovedPapers = TwwdaBoardPaper::whereIn('assignment_id', $directorAssignmentIds)
                    ->whereIn('status', ['director_approved', 'ceo_review', 'ceo_approved'])
                    ->count();

                // Papers rejected by this director
                $dirRejectedPapers = TwwdaBoardPaper::whereIn('assignment_id', $directorAssignmentIds)
                    ->where('status', 'rejected_by_director')
                    ->count();

                // Director's directorate info
                $directorateMapping = TwwdaUserDirectorateMapping::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->where('role_type', 'director')
                    ->with('directorate')
                    ->first();

                // Total assignments under this director
                $dirTotalAssignments = TwwdaOfficerAssignment::where('director_id', $user->id)->count();

                $directorPending = [
                    'pending_papers' => $dirPendingPapers,
                    'approved_count' => $dirApprovedPapers,
                    'rejected_count' => $dirRejectedPapers,
                    'total_assignments' => $dirTotalAssignments,
                    'directorate' => $directorateMapping,
                    'total' => $dirPendingPapers->count(),
                ];
            }

            return view('workflow.dashboard.index', compact(
                'meetingCounts',
                'upcomingMeetings',
                'recentActivity',
                'unreadCount',
                'notifications',
                'isCeo',
                'ceoPending',
                'isDirector',
                'directorPending',
                'paperCounts',
                'totalPapers',
                'noticeCounts',
                'meetingsByCommittee',
                'meetingsByMonth',
                'stageCounts',
                'assignmentStats',
                'boardNoticeCounts',
                'userDivision',
                'userDirectorate',
                'userDirector',
                'reportsDirectlyToCeo'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Dashboard error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load dashboard.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * List user's notifications (scoped by role).
     *
     * Corporation Secretary, CS-Assistant, DD Internal Audit → see ALL notifications for meetings
     * Everyone else → only their own notifications
     */
    public function notifications(Request $request)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->hasAnyRole(['Corporation Secretary', 'CS-Assistant', 'DD Internal Audit']);

            // Filter params
            $filterType = $request->get('type');
            $filterMeeting = $request->get('meeting_id');
            $filterRead = $request->get('read_status'); // 'unread', 'read', or null (all)

            if ($isAdmin) {
                // CS / DDIA see all notifications across all users for all meetings
                $query = TwwdaNotification::with(['user', 'meeting.boardCommittee'])
                    ->orderBy('created_at', 'desc');
            } else {
                // Everyone else sees only their own
                $query = TwwdaNotification::forUser($user->id)
                    ->with(['meeting.boardCommittee'])
                    ->orderBy('created_at', 'desc');
            }

            // Apply filters
            if ($filterType) {
                $query->where('type', $filterType);
            }
            if ($filterMeeting) {
                $query->where('meeting_id', $filterMeeting);
            }
            if ($filterRead === 'unread') {
                $query->unread();
            } elseif ($filterRead === 'read') {
                $query->whereNotNull('read_at');
            }

            $notifications = $query->paginate(30)->withQueryString();

            // Stats
            if ($isAdmin) {
                $unreadCount = TwwdaNotification::whereNull('read_at')->count();
                $totalCount = TwwdaNotification::count();
            } else {
                $unreadCount = TwwdaNotification::forUser($user->id)->unread()->count();
                $totalCount = TwwdaNotification::forUser($user->id)->count();
            }

            // Available notification types for filter
            $notificationTypes = TwwdaNotification::select('type')
                ->distinct()
                ->whereNotNull('type')
                ->orderBy('type')
                ->pluck('type');

            // Available meetings for filter
            $meetings = TwwdaMeeting::select('id', 'title', 'meeting_date')
                ->orderBy('meeting_date', 'desc')
                ->get();

            // Recent activity / audit logs (CS/DDIA see all, others see only their meeting-related logs)
            if ($isAdmin) {
                $recentActivity = TwwdaAuditLog::with(['user', 'meeting'])
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();
            } else {
                // Others see audit logs only for meetings where they have an involvement
                $userMeetingIds = $this->getUserMeetingIds($user);
                $recentActivity = TwwdaAuditLog::with(['user', 'meeting'])
                    ->where(function ($q) use ($user, $userMeetingIds) {
                        $q->where('user_id', $user->id)
                          ->orWhereIn('meeting_id', $userMeetingIds);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(30)
                    ->get();
            }

            return view('workflow.dashboard.notifications', compact(
                'notifications',
                'unreadCount',
                'totalCount',
                'isAdmin',
                'notificationTypes',
                'meetings',
                'recentActivity',
                'filterType',
                'filterMeeting',
                'filterRead'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Notifications error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('message', 'Failed to load notifications.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Mark all notifications as read.
     * CS/DDIA: marks all system notifications as read.
     * Others: marks only their own as read.
     */
    public function markAllRead()
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->hasAnyRole(['Corporation Secretary', 'CS-Assistant', 'DD Internal Audit']);

            if ($isAdmin) {
                // Mark all unread across the entire system
                TwwdaNotification::unread()->update(['read_at' => now()]);
            } else {
                TwwdaNotification::forUser($user->id)
                    ->unread()
                    ->update(['read_at' => now()]);
            }

            session()->flash('message', 'All notifications marked as read.'); session()->flash('alert-type', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('TWWDA Mark All Read error: ' . $e->getMessage());
            session()->flash('message', 'Failed to mark notifications as read.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Get meeting IDs where a user has involvement (assignments, papers, attendance, etc.)
     */
    private function getUserMeetingIds($user): array
    {
        $meetingIds = collect();

        // Meetings where user has officer assignments
        $assignmentMeetings = TwwdaOfficerAssignment::where('user_id', $user->id)
            ->orWhere('director_id', $user->id)
            ->pluck('meeting_id');
        $meetingIds = $meetingIds->merge($assignmentMeetings);

        // Meetings where user authored a board paper
        $paperMeetings = TwwdaBoardPaper::where('prepared_by', $user->id)
            ->pluck('meeting_id');
        $meetingIds = $meetingIds->merge($paperMeetings);

        // Meetings where user initiated
        $initiatedMeetings = TwwdaMeeting::where('initiated_by', $user->id)
            ->pluck('id');
        $meetingIds = $meetingIds->merge($initiatedMeetings);

        // Meetings where user created a board notice
        $noticeMeetings = TwwdaBoardNotice::where('created_by', $user->id)
            ->pluck('meeting_id');
        $meetingIds = $meetingIds->merge($noticeMeetings);

        // Meetings where user created an internal notice
        $internalNoticeMeetings = TwwdaInternalNotice::where('created_by', $user->id)
            ->pluck('meeting_id');
        $meetingIds = $meetingIds->merge($internalNoticeMeetings);

        return $meetingIds->unique()->filter()->values()->toArray();
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead($id)
    {
        try {
            $notification = TwwdaNotification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->markAsRead();

            session()->flash('message', 'Notification marked as read.'); session()->flash('alert-type', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('TWWDA Mark Read error: ' . $e->getMessage());
            session()->flash('message', 'Failed to mark notification as read.'); session()->flash('alert-type', 'error');
            return back();
        }
    }
}
