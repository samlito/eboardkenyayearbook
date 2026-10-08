<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\AlmanacSchedule;
use App\Models\BoardCommittee;
use App\Models\BoardMember;
use App\Models\Salutation;
use App\Models\Twwda\TwwdaBoardNotice;
use App\Models\Twwda\TwwdaBoardPaper;
use App\Models\Twwda\TwwdaConflictDeclaration;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaMeetingAgendaItem;
use App\Models\Twwda\TwwdaMeetingAttendance;
use App\Models\Twwda\TwwdaNotification;
use App\Models\Twwda\TwwdaStickyNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BoardPortalController extends Controller
{
    /**
     * Resolve the logged-in user's BoardMember record.
     */
    private function getMember()
    {
        $user = Auth::user();
        $member = BoardMember::with(['committees', 'county', 'assignsalutation'])
            ->where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if (!$member && $user->hasRole('CEO')) {
            $member = BoardMember::create([
                'salutation'       => Salutation::first()->id ?? 1,
                'names'            => $user->name,
                'email'            => $user->email,
                'phone'            => $user->phone ?? '',
                'idno'             => $user->staff_number ?? '',
                'designation'      => 'CEO',
                'county_id'        => 47,
                'postal_address'   => '-',
                'place_of_residence' => '-',
                'appointment_date' => now()->toDateString(),
                'status'           => 'Active',
                'user_id'          => $user->id,
                'addedby'          => 'System (CEO Auto-Profile)',
            ]);

            $member->load(['committees', 'county', 'assignsalutation']);
        }

        return $member;
    }

    /**
     * Get committee IDs this board member belongs to.
     */
    private function getMemberCommitteeIds(BoardMember $member): array
    {
        return $member->committees->pluck('id')->toArray();
    }

    // ═══════════════════════════════════════════════════════════════
    //  DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function dashboard()
    {
        try {
            $member = $this->getMember();
            if (!$member) {
                return redirect()->route('workflow.dashboard')->with('message', 'Board member profile not found.')->with('alert-type', 'error');
            }

            $committeeIds = $this->getMemberCommitteeIds($member);

            // Upcoming meetings for this member's committees (V2 meetings)
            $upcomingMeetings = TwwdaMeeting::whereIn('board_committee_id', $committeeIds)
                ->where('meeting_date', '>=', today())
                ->with(['boardCommittee'])
                ->orderBy('meeting_date')
                ->limit(10)
                ->get();

            // Active/live meetings
            $activeMeetings = TwwdaMeeting::whereIn('board_committee_id', $committeeIds)
                ->where('is_started', true)
                ->where('current_stage', '>=', 9)
                ->with(['boardCommittee'])
                ->get();

            // Board calendar — almanac schedules for member's committees
            $calendarSchedules = AlmanacSchedule::whereIn('board_committee_id', $committeeIds)
                ->where('meeting_date', '>=', today()->subMonths(1))
                ->with(['boardCommittee'])
                ->orderBy('meeting_date')
                ->limit(20)
                ->get();

            // Official notices approved/sent for member's committees
            $recentNotices = TwwdaBoardNotice::whereIn('status', ['approved', 'sent'])
                ->whereHas('meeting', fn($q) => $q->whereIn('board_committee_id', $committeeIds))
                ->with(['meeting.boardCommittee'])
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();

            // Count of approved papers waiting for review
            $papersToReview = TwwdaBoardPaper::whereHas('meeting', function ($q) use ($committeeIds) {
                    $q->whereIn('board_committee_id', $committeeIds)
                      ->where('meeting_date', '>=', today());
                })
                ->where(function ($q) {
                    $q->where('status', 'ceo_approved')
                      ->orWhere('is_auto_approved', true);
                })
                ->count();

            // My sticky notes count
            $myNotesCount = TwwdaStickyNote::where('user_id', Auth::id())->count();

            // Unread notifications
            $unreadCount = TwwdaNotification::forUser(Auth::id())->unread()->count();

            // Profile completeness
            $checklist = [
                'photo' => !!$member->photo,
                'upload_cv' => !!$member->upload_cv,
                'qualifications' => !!$member->qualifications,
                'postal_address' => !!$member->postal_address,
                'county_id' => !!$member->county_id,
            ];
            $profileScore = (int) (collect($checklist)->filter()->count() / count($checklist) * 100);

            return view('board.portal.dashboard', compact(
                'member',
                'upcomingMeetings',
                'activeMeetings',
                'calendarSchedules',
                'recentNotices',
                'papersToReview',
                'myNotesCount',
                'unreadCount',
                'profileScore',
                'committeeIds'
            ));
        } catch (\Exception $e) {
            Log::error('Board Portal Dashboard error: ' . $e->getMessage());
            return back()->with('message', 'Failed to load dashboard.')->with('alert-type', 'error');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  COMMITTEES
    // ═══════════════════════════════════════════════════════════════

    public function committees()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committees = $member->committees()->with(['members', 'members.assignsalutation'])->get();

        return view('board.portal.committees', compact('member', 'committees'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  OFFICIAL NOTICES
    // ═══════════════════════════════════════════════════════════════

    public function notices()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committeeIds = $this->getMemberCommitteeIds($member);

        // Approved and sent notices for member's committees
        $notices = TwwdaBoardNotice::whereIn('status', ['approved', 'sent'])
            ->whereHas('meeting', fn($q) => $q->whereIn('board_committee_id', $committeeIds))
            ->with(['meeting.boardCommittee', 'creator', 'parentNotice', 'replacementNotices'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('board.portal.notices', compact('member', 'notices'));
    }

    /**
     * View a single official notice with PDF download.
     */
    public function noticeShow(TwwdaBoardNotice $notice)
    {
        $member = $this->getMember();
        $committeeIds = $member ? $this->getMemberCommitteeIds($member) : [];

        // Ensure member has access to this notice's committee and notice is approved/sent
        if (!$notice->meeting || !in_array($notice->meeting->board_committee_id, $committeeIds)
            || !in_array($notice->status, ['approved', 'sent'])) {
            return redirect()->route('board.portal.notices')->with('message', 'Access denied.')->with('alert-type', 'error');
        }

        $notice->load(['meeting.boardCommittee', 'creator', 'parentNotice', 'replacementNotices']);

        return view('board.portal.notice-show', compact('notice', 'member'));
    }

    /**
     * Download the official notice as PDF.
     */
    public function noticeDownload(TwwdaBoardNotice $notice)
    {
        $member = $this->getMember();
        $committeeIds = $member ? $this->getMemberCommitteeIds($member) : [];

        if (!$notice->meeting || !in_array($notice->meeting->board_committee_id, $committeeIds)
            || !in_array($notice->status, ['approved', 'sent'])) {
            abort(403);
        }

        $notice->load('meeting.boardCommittee');
        $boardMembers = BoardMember::all();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('workflow.board-notices.pdf', [
            'notice' => $notice,
            'boardMembers' => $boardMembers,
        ]);
        $pdf->setPaper('A4', 'portrait');

        $refClean = preg_replace('/[^A-Za-z0-9\-_]/', '_', $notice->reference_number ?? 'Board_Notice');
        return $pdf->download('Board_Notice_' . $refClean . '.pdf');
    }

    // ═══════════════════════════════════════════════════════════════
    //  BOARD PAPERS (Pre-meeting review)
    // ═══════════════════════════════════════════════════════════════

    /**
     * List meetings with approved papers available for pre-meeting review.
     */
    public function papers()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committeeIds = $this->getMemberCommitteeIds($member);

        // Meetings with approved papers, upcoming
        $meetings = TwwdaMeeting::whereIn('board_committee_id', $committeeIds)
            ->where('meeting_date', '>=', today())
            ->whereHas('boardPapers', function ($q) {
                $q->where('status', 'ceo_approved')->orWhere('is_auto_approved', true);
            })
            ->with(['boardCommittee', 'boardPapers' => function ($q) {
                $q->where(function ($sq) {
                    $sq->where('status', 'ceo_approved')->orWhere('is_auto_approved', true);
                })->with(['author', 'annexures']);
            }])
            ->orderBy('meeting_date')
            ->get();

        return view('board.portal.papers', compact('member', 'meetings'));
    }

    /**
     * View a single board paper with sticky note support.
     */
    public function paperShow(TwwdaBoardPaper $paper)
    {
        $member = $this->getMember();
        $committeeIds = $member ? $this->getMemberCommitteeIds($member) : [];

        // Verify access
        if (!$paper->meeting || !in_array($paper->meeting->board_committee_id, $committeeIds)) {
            return redirect()->route('board.portal.papers')->with('message', 'Access denied.')->with('alert-type', 'error');
        }

        // Only show approved papers
        if (!$paper->isApprovedByCeo()) {
            return redirect()->route('board.portal.papers')->with('message', 'This paper is not yet approved.')->with('alert-type', 'error');
        }

        $paper->load(['meeting.boardCommittee', 'author', 'annexures']);

        // Load this user's sticky notes for the paper
        $myNotes = TwwdaStickyNote::where('board_paper_id', $paper->id)
            ->where('user_id', Auth::id())
            ->orderBy('created_at')
            ->get();

        return view('board.portal.paper-show', compact('paper', 'member', 'myNotes'));
    }

    /**
     * Save a sticky note on a board paper (AJAX).
     */
    public function saveStickyNote(Request $request, TwwdaBoardPaper $paper)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $note = TwwdaStickyNote::create([
                'board_paper_id' => $paper->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'color' => $request->color ?? 'yellow',
                'page_number' => $request->page_number ?? 1,
                'x_position' => $request->x_position ?? 0,
                'y_position' => $request->y_position ?? 0,
            ]);

            return response()->json(['success' => true, 'note' => $note, 'message' => 'Note saved.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save note.'], 500);
        }
    }

    /**
     * Delete a sticky note (AJAX).
     */
    public function deleteStickyNote(TwwdaStickyNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $note->delete();
        return response()->json(['success' => true, 'message' => 'Note removed.']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  ACTIVE MEETING (Live meeting day view for board members)
    // ═══════════════════════════════════════════════════════════════

    /**
     * List active meetings for the board member.
     */
    public function activeMeetings()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committeeIds = $this->getMemberCommitteeIds($member);

        $activeMeetings = TwwdaMeeting::whereIn('board_committee_id', $committeeIds)
            ->where('is_started', true)
            ->with([
                'boardCommittee',
                'attendance',
                'conflictDeclarations.boardMember',
                'boardPapers' => function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('status', 'ceo_approved')->orWhere('is_auto_approved', true);
                    })->with(['author', 'annexures']);
                },
                'agendaItems' => function ($q) {
                    $q->visibleToBoard()
                      ->ordered()
                      ->with(['boardPaper.author', 'boardPaper.annexures']);
                },
            ])
            ->orderBy('started_at', 'desc')
            ->get();

        // Also show today's meetings that haven't been started yet
        $todaysMeetings = TwwdaMeeting::whereIn('board_committee_id', $committeeIds)
            ->whereDate('meeting_date', today())
            ->where('is_started', false)
            ->with(['boardCommittee'])
            ->get();

        // My sticky notes for papers in active meetings
        $activeMeetingIds = $activeMeetings->pluck('id');
        $myPaperNotes = TwwdaStickyNote::where('user_id', Auth::id())
            ->whereHas('boardPaper', fn($q) => $q->whereIn('meeting_id', $activeMeetingIds))
            ->with('boardPaper')
            ->get()
            ->groupBy('board_paper_id');

        // My conflict declarations for active meetings
        $myDeclarations = TwwdaConflictDeclaration::where('board_member_id', $member->id)
            ->whereIn('meeting_id', $activeMeetingIds)
            ->get()
            ->keyBy('meeting_id');

        return view('board.portal.active-meetings', compact(
            'member', 'activeMeetings', 'todaysMeetings', 'myPaperNotes', 'myDeclarations'
        ));
    }

    /**
     * View an active meeting — agenda items, papers, and conflict flow.
     */
    public function meetingLive(TwwdaMeeting $meeting)
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committeeIds = $this->getMemberCommitteeIds($member);

        if (!in_array($meeting->board_committee_id, $committeeIds)) {
            return redirect()->route('board.portal.active')->with('message', 'Access denied.')->with('alert-type', 'error');
        }

        // Check if meeting is started
        if (!$meeting->is_started) {
            return view('board.portal.meeting-not-started', compact('meeting', 'member'));
        }

        $meeting->load(['boardCommittee']);

        // Get agenda items visible to board (no pulled-down items)
        $agendaItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
            ->visibleToBoard()
            ->ordered()
            ->with(['boardPaper.author', 'boardPaper.annexures'])
            ->get();

        // Attendance register
        $attendance = TwwdaMeetingAttendance::where('meeting_id', $meeting->id)
            ->where('is_present', true)
            ->with(['boardMember', 'user'])
            ->get();

        // Conflict declarations for this meeting
        $conflicts = TwwdaConflictDeclaration::where('meeting_id', $meeting->id)
            ->with('boardMember')
            ->get();

        // My declaration
        $myDeclaration = TwwdaConflictDeclaration::where('meeting_id', $meeting->id)
            ->where('board_member_id', $member->id)
            ->first();

        // My sticky notes for papers in this meeting
        $myPaperNotes = TwwdaStickyNote::where('user_id', Auth::id())
            ->whereHas('boardPaper', fn($q) => $q->where('meeting_id', $meeting->id))
            ->with('boardPaper')
            ->get()
            ->groupBy('board_paper_id');

        return view('board.portal.meeting-live', compact(
            'meeting', 'member', 'agendaItems', 'attendance',
            'conflicts', 'myDeclaration', 'myPaperNotes'
        ));
    }

    // ═══════════════════════════════════════════════════════════════
    //  CALENDAR
    // ═══════════════════════════════════════════════════════════════

    public function calendar()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('board.portal.dashboard');
        }

        $committeeIds = $this->getMemberCommitteeIds($member);

        $schedules = AlmanacSchedule::whereIn('board_committee_id', $committeeIds)
            ->with(['boardCommittee', 'almanac'])
            ->orderBy('meeting_date')
            ->get();

        return view('board.portal.calendar', compact('member', 'schedules'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  DOCUMENT REPOSITORY (Board Member View)
    // ═══════════════════════════════════════════════════════════════

    public function repository()
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('dashboard');
        }

        $categories = \App\Models\DocumentCategory::where('is_active', true)
            ->withCount(['activeDocuments as total_documents'])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('board.portal.repository.index', compact('member', 'categories'));
    }

    public function repositoryCategory(\App\Models\DocumentCategory $category)
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('dashboard');
        }

        $memberCommitteeIds = $this->getMemberCommitteeIds($member);

        $documents = $category->documents()
            ->where('is_active', true)
            ->with(['uploader', 'accessRules'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn($doc) => $doc->isAccessibleBy($member));

        return view('board.portal.repository.category', compact('member', 'category', 'documents'));
    }

    public function repositoryDownload(\App\Models\RepositoryDocument $document)
    {
        $member = $this->getMember();
        if (!$member) {
            return redirect()->route('dashboard');
        }

        if (!$document->is_active || !$document->isAccessibleBy($member)) {
            session()->flash('message', 'You do not have access to this document.');
            session()->flash('alert-type', 'error');
            return back();
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            session()->flash('message', 'File not found.');
            session()->flash('alert-type', 'error');
            return back();
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
