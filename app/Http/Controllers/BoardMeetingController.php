<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use App\Http\Services\BoardMeetingService;
use App\Mail\BoardMeetingInvitationMail;
use App\Mail\BoardMeetingNoticeMail;
use App\Mail\TenDayBoardMeetingInvitationMail;
use App\Models\Almanac;
use App\Models\AlmanacSchedule;
use App\Models\BoardCommittee;
use App\Models\BoardCommitteeMember;
use App\Models\BoardMeeting;
use App\Models\BoardMeetingAssignment;
use App\Models\BoardMeetingNotice10Day;
use App\Models\BoardPaper;
use App\Models\User;
use App\Notifications\BoardMeetingPaperRequest;
use App\Notifications\ReviewBoardPapersNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class BoardMeetingController extends Controller
{
    //


    public function indexBoardPapers(Request $request)
    {
        $user = Auth::user();

        $meetings = $user->hasRole('Corporation Secretary')
            ? BoardMeetingService::meetingsForCorporationSecretary()
            : BoardMeetingService::upcomingMeetingsForLoggedInMember();

        AuditLogService::log($request, 'VIEW', 'board_meetings', null, null, [
            'action' => $user->hasRole('Corporation Secretary')
                ? 'Viewed list of meetings (CS view)'
                : 'Viewed list of upcoming meetings',
        ]);

        return view('backend.board_papers.index', compact('meetings'));
    }
    public function show(BoardMeeting $meeting, Request $request)
    {
        // Check that user belongs to this meeting's committee
//        if (!$meeting->committee || !$meeting->committee->boardMembers->contains('user_id', auth()->id())) {
//            abort(403, 'Unauthorized');
//        }
//
//        AuditLogService::log($request, 'VIEW', 'board_meetings', $meeting->id, null, [
//            'action' => 'Viewed meeting details',
//        ]);

        return view('backend.board_papers.show', compact('meeting'));
    }

    public function notifyCeo($id)
    {

        $meeting = BoardMeeting::findOrFail($id);

        // Optional: Check if all heads submitted
        $assigned = $meeting->assignments;
        $submittedIds = \App\Models\BoardPaper::where('meeting_id', $id)->pluck('user_id')->toArray();
        $pending = $assigned->filter(fn($a) => !in_array($a->user_id, $submittedIds));

        if ($pending->count() > 0) {
            return redirect()->back()->with('error', 'Not all division heads have submitted.');
        }

        // Find CEO (you can filter by role or email or any condition)
        $ceo = User::role('CEO')->first();
        if (!$ceo) {
            return redirect()->back()->with('error', 'CEO account not found.');
        }
//        dd('here2');
//        $ceo->notify(new ReviewBoardPapersNotification($meeting));
        $ceo->notify(new ReviewBoardPapersNotification($meeting));
        $meeting->update(['ceo_notified_at' => now()]);


        return redirect()->route('board.meetings.all')->with('success', 'CEO notified successfully to review board papers.');
    }

    public function index0()
    {

        $meetings = BoardMeeting::with(['notice', 'assignments.user'])->where('active',true)->latest()->get();
        return view('backend.board_meetings.index', compact('meetings'));
    }
    public function index1()
    {
        $meetings = BoardMeeting::with(['notice', 'assignments.user'])->latest()->get();

        // Prepare submitted user IDs per meeting
        $submittedUserIdsPerMeeting = [];

        foreach ($meetings as $meeting) {
            $submittedUserIdsPerMeeting[$meeting->id] = \App\Models\BoardPaper::where('meeting_id', $meeting->id)
                ->pluck('user_id')
                ->toArray();
        }

        return view('backend.board_meetings.index', compact('meetings', 'submittedUserIdsPerMeeting'));
    }
    public function index()
    {

//        $meetings = BoardMeeting::with(['notice', 'assignments.user'])->latest()->get();
        $meetings = BoardMeeting::with(['notice', 'assignments.user'])
->where('active',true)
            ->orderBy('date', 'desc')
            ->get();

        // Prepare submitted user IDs per meeting
        $submittedUserIdsPerMeeting = [];

        $meetings = $meetings->map(function ($meeting) use (&$submittedUserIdsPerMeeting) {
            // Get submitted paper user_ids
            $submittedUserIdsPerMeeting[$meeting->id] = \App\Models\BoardPaper::where('meeting_id', $meeting->id)
                ->pluck('user_id')
                ->toArray();

            // Check if all board papers have been reviewed by CEO
            $papers = \App\Models\BoardPaper::where('meeting_id', $meeting->id)->get();
            $meeting->allReviewedByCeo = $papers->every(fn($p) => $p->status === 'Approved');

            return $meeting;
        });
        $needsRevisionUserIdsPerMeeting = [];

        foreach ($meetings as $meeting) {
            $needsRevisionUserIdsPerMeeting[$meeting->id] = \App\Models\BoardPaper::where('meeting_id', $meeting->id)
                ->where('status', 'Needs Revision')
                ->pluck('user_id')
                ->toArray();
        }


        return view('backend.board_meetings.index', compact('meetings', 'submittedUserIdsPerMeeting','needsRevisionUserIdsPerMeeting'));
    }

    public function create()
    {
        $board_committees = BoardCommittee::all(); // Fetch all committees
        return view('backend.board_meetings.create',compact('board_committees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'date' => 'required|date|after:today',
            'venue' => 'required|string',
            'type' => 'required|string',
            'board_committee_id' => 'required|exists:board_committees,id',
        ]);

        BoardMeeting::create([
            'title' => $request->title,
            'date' => $request->date,
            'venue' => $request->venue,
            'type' => $request->type,
            'status' => 'Scheduled',
            'board_committee_id' => $request->board_committee_id,
        ]);

        return redirect()->route('board.meetings.all')->with('success', 'Meeting scheduled successfully');
    }

    public function initiate($id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        $daysRemaining = Carbon::now()->diffInDays(Carbon::parse($meeting->date), false);

        return view('backend.board_meetings.initiate', compact('meeting', 'daysRemaining'));
    }

    public function viewNotice($id)
    {
        $meeting = BoardMeeting::with('notice')->findOrFail($id);

        if (!$meeting->notice || $meeting->notice->status !== 'Approved') {
            abort(404, 'Notice not available or not approved yet.');
        }

        return view('backend.board_meetings.view_notice', compact('meeting'));
    }

    public function notifyDivisionHeads($id)
    {
        $meeting = BoardMeeting::findOrFail($id);

        // already assigned user_ids
        $alreadyAssigned = $meeting->assignments->pluck('user_id')->toArray();

        // get only active heads
        $heads = User::role('Division Head')
            ->where('status', 'active')
            ->with('division')
            ->orderBy('name')
            ->get();

        return view('backend.board_meetings.notify_heads', compact(
            'meeting',
            'heads',
            'alreadyAssigned'
        ));
    }



    public function sendDivisionNotifications0(Request $request, $id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        $request->validate(['heads' => 'required|array']);

        $users = User::whereIn('id', $request->heads)->get();

        foreach ($users as $user) {
            Notification::send($user, new BoardMeetingPaperRequest($meeting));
        }

        return redirect()->route('board.meetings.all')->with('success', 'Division Heads notified successfully.');
    }

    public function sendDivisionNotifications1(Request $request, $id)
    {
        $meeting = BoardMeeting::findOrFail($id);
        $request->validate(['heads' => 'required|array']);

        $users = User::whereIn('id', $request->heads)->get();

        foreach ($users as $user) {
            // Save assignment
            BoardMeetingAssignment::class::updateOrCreate([
                'meeting_id' => $meeting->id,
                'user_id' => $user->id
            ], [
                'notified' => true
            ]);

            // Send notification
//            Notification::send($user, new BoardMeetingPaperRequest($meeting));
        }

        return redirect()->route('board.meetings.all')->with('success', 'Division Heads notified and assignments saved.');
    }
    public function sendDivisionNotifications(Request $request, $id)
    {
        $meeting = BoardMeeting::findOrFail($id);

        $request->validate([
            'heads' => 'required|array',
        ]);

        // Special rule for Audit Committee
        $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
        if (in_array($meeting->board_committee_id, $auditCommitteeIds)) {
            $users = User::whereIn('id', $request->heads)->get();

            foreach ($users as $user) {
                if ($user->division_id != 1) {
                    return back()->withErrors([
                        'heads' => 'Only Internal Audit Division  can be allocated this meeting.'
                    ])->withInput();
                }
            }
        }

        $users = User::whereIn('id', $request->heads)->get();

        foreach ($users as $user) {
            BoardMeetingAssignment::updateOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'user_id' => $user->id
                ],
                [
                    'notified' => true
                ]
            );

            // Send notification (optional)
             Notification::send($user, new BoardMeetingPaperRequest($meeting));
        }
        // ✅ Log the notification activity (no old values needed)
        AuditLogService::log(
            $request,
            'INSERT',
            'board_meeting_assignments',
            $meeting->id,
            null,
            [
                'action'  => 'Division heads notified',
                'meeting' => $meeting->title,
                'heads'   => $users->pluck('name')->toArray()
            ]
        );
        return redirect()->route('board.meetings.all')
            ->with('success', 'Division Heads notified and assignments saved.');
    }


    public function notifyBoard($id, Request $request)
    {

        try {
            $meeting = BoardMeeting::with('committee')->findOrFail($id);

            // Check if all board papers have been reviewed and approved by CEO
//            $papers = BoardPaper::where('meeting_id', $id)->get();
//            $allApproved = $papers->every(fn($p) => $p->status === 'Approved');
//
//            if (!$allApproved) {
//                return redirect()->back()->with('error', 'Not all board papers have been approved by the CEO.');
//            }
            $papers = BoardPaper::where('meeting_id', $id)->get();

//            if ($papers->isEmpty()) {
//                return redirect()->back()->with('error', 'This meeting has no board papers. Ensure HODs submit all board papers first.');
//            }

//            $allApproved = $papers->every(fn($p) => $p->status === 'Approved');
//
//            if (!$allApproved) {
//                return redirect()->back()->with('error', 'Not all board papers have been approved by the CEO.');
//            }


            // Check for attached board committee
            if (!$meeting->board_committee_id) {
                return redirect()->back()->with('error', 'Meeting is not linked to any board committee.');
            }

            // Fetch board members via board_committee
            $memberUserIds = BoardCommitteeMember::where('board_committee_id', $meeting->board_committee_id)
                ->pluck('board_member_id');

            $users = DB::table('board_members')
                ->whereIn('id', $memberUserIds)
                ->select('email', 'names') // 'names' column assumed to hold full name
                ->get();

            if ($users->isEmpty()) {
                return redirect()->back()->with('error', 'No board members found for the selected committee.');
            }

//            // Send email to each board member
//            foreach ($users as $user) {
//                Mail::to($user->email)->queue(new BoardMeetingNoticeMail($meeting, $user));
//            }
            $meeting = BoardMeeting::with(['committee.members', 'officialNotice'])->findOrFail($id);

            foreach ($meeting->committee->members as $member) {
                Mail::to($member->email)->queue(new TenDayBoardMeetingInvitationMail($meeting));
            }
            // Update board notification timestamp (use new column `board_notified_at` if possible)
            $meeting->board_notified_at = now();
            $meeting->status= 'Notice Sent'; // Update status to indicate notice sent
            $meeting->save();

            // Log audit trail
            AuditLogService::log($request, 'UPDATE', 'board_meetings', $meeting->id, null, [
                'action' => 'CS sent meeting notice to board/committee members',
            ]);

            return redirect()->back()->with('success', 'Notice successfully sent to board members.');

        } catch (\Exception $e) {
            Log::error('Error notifying board members: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send board notices: ' . $e->getMessage());
        }
    }


    public function selectSchedule()
    {
        $currentAlmanac = Almanac::where('is_active', true)->latest()->first();

        if (!$currentAlmanac) {
            return back()->with('error', 'No active almanac found.');
        }

        $schedules = AlmanacSchedule::with(['boardCommittee', 'boardMeeting'])
            ->where('almanac_id', $currentAlmanac->id)
            ->orderBy('meeting_date', 'desc') // latest first
            ->get();

        return view('backend.board_meetings.select_schedule', compact('schedules', 'currentAlmanac'));
    }



    public function storeFromSchedule(Request $request, $id)
    {
        $schedule = AlmanacSchedule::with('boardCommittee')->findOrFail($id);

        if ($schedule->boardMeeting) {
            return response()->json(['message' => 'Meeting already exists'], 409);
        }
        Log::info('Creating board meeting from schedule', [
            'schedule_id' => $schedule->id,
            'committee_id' => $schedule->board_committee_id,
            'meeting_date' => $schedule->meeting_date,
        ]);

        $meeting = BoardMeeting::create([
            'title' =>  ($schedule->boardCommittee->name ?? 'Board') . ' Meeting',
            'date' => $schedule->meeting_date,
            'venue' => 'N/A',
            'type' => 'Committee',
            'status' => 'Scheduled',
            'board_committee_id' => $schedule->board_committee_id,
            'almanac_schedule_id' => $schedule->id,
        ]);

        return response()->json([
            'message' => 'Meeting has been created and can be viewed from the Board Meetings tab.',
            'meeting_id' => $meeting->id,
        ]);
    }

    public function send10DayNotice($id)
    {
        $meeting = BoardMeeting::with('boardPapers')->findOrFail($id);

        return view('backend.actual_meetings.add_notice_10', compact('meeting'));
    }

    public function storeNotice0(Request $request, $id)
    {
        $request->validate([
            'reference_number' => 'required|string',
            'reference' => 'required|string',
            'notice_text' => 'required|string',
            'agenda_order' => 'required|array',
            'agenda_order.*' => 'exists:board_papers,id',
        ]);


        $meeting = BoardMeeting::findOrFail($id);
        $data = [
            'notice_text' => $request->notice_text,
            'agenda_order' => $request->agenda_order,
            'published_at' => now(),
            'reference_number' => $request->reference_number,
            'reference' => $request->reference,
            'submission_date' => now(),
        ];

        ActualMeetingService::saveNotice($meeting, $data, $request);

        return redirect()->route('actual.meetings.index')->with('success', 'Official meeting notice added successfully.');
    }


    public function storeNotice1(Request $request, $id)
    {

        $request->validate([
            'reference_number' => 'required|string',
            'reference'        => 'required|string',
            'notice_text'      => 'required|string',
            'agenda_order'     => 'required|array',
            'agenda_order.*'   => 'exists:board_papers,id',
        ]);

        $meeting = BoardMeeting::findOrFail($id);

        $data = [
            'notice_text'      => $request->notice_text,
            'agenda_order'     => $request->agenda_order,
            'published_at'     => now(),
            'reference_number' => $request->reference_number,
            'reference'        => $request->reference,
            'submission_date'  => now(),
        ];

        // Save or update the notice
        $notice = BoardMeetingNotice10Day::updateOrCreate(
            ['meeting_id' => $meeting->id],
            $data
        );

        // ✅ Audit log
        AuditLogService::log(
            $request,
            'CREATE/UPDATE',
            'board_meeting_notices',
            $notice->id,
            null,
            [
                'message' => '10-day board meeting notice sent',
                'meeting_id' => $meeting->id,
                'reference_number' => $request->reference_number,
                'reference' => $request->reference,
            ]
        );

        return redirect()->route('board.meetings.all')
            ->with('success', 'Official 10-day meeting notice added successfully.');
    }
    public function storeNotice(Request $request, $id)
    {
        $request->validate([
            'reference_number' => 'required|string',
            'reference'        => 'required|string',
            'notice_text'      => 'required|string',
//            'agenda_order'     => 'required|array',
//            'agenda_order.*'   => 'exists:board_papers,id',
            'pdf_file'         => 'nullable|file|mimes:pdf', // validate PDF
        ]);

        $meeting = BoardMeeting::findOrFail($id);

        $data = [
            'notice_text'      => $request->notice_text,
            'agenda_order'     => $request->agenda_order,
            'published_at'     => now(),
            'reference_number' => $request->reference_number,
            'reference'        => $request->reference,
            'submission_date'  => now(),
        ];

        // Handle file upload
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('notices', 'public'); // stored in storage/app/public/notices
            $data['document_path'] = $path;
        }

        // Save or update the notice
        $notice = BoardMeetingNotice10Day::updateOrCreate(
            ['meeting_id' => $meeting->id],
            $data
        );

        // ✅ Audit log
        AuditLogService::log(
            $request,
            'CREATE/UPDATE',
            'board_meeting_notices',
            $notice->id,
            null,
            [
                'message' => '10-day board meeting notice sent',
                'meeting_id' => $meeting->id,
                'reference_number' => $request->reference_number,
                'reference' => $request->reference,
            ]
        );

        return redirect()->route('board.meetings.all')
            ->with('success', 'Official 10-day meeting notice added successfully.');
    }



    public function preview($id)
    {
        $meeting = BoardMeeting::with(['committee', 'officialNotice10', 'boardPapers'])->findOrFail($id);

        $orderedPapers = collect();

        if ($meeting->officialNotice && is_array($meeting->officialNotice->agenda_order)) {
            foreach ($meeting->officialNotice->agenda_order as $paperId) {
                $paper = $meeting->boardPapers->firstWhere('id', $paperId);
                if ($paper) {
                    $orderedPapers->push($paper);
                }
            }
        } else {
            $orderedPapers = $meeting->boardPapers;
        }

        // 👇 Return only the partial HTML (no JSON)
        return view('backend.actual_meetings.partials.preview_notice_modal_10', [
            'meeting' => $meeting,
            'orderedPapers' => $orderedPapers,
        ]);
    }
}
