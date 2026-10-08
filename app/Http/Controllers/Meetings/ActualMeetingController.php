<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Http\Services\AuditLogService;
use App\Mail\BoardMeetingInvitationMail;
use App\Models\BoardMeeting;
use App\Models\BoardMeetingMinute;
use App\Models\ConflictOfInterestDeclaration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Services\ActualMeetingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ActualMeetingController extends Controller
{
    //

    protected $service;

    public function __construct(ActualMeetingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $meetings = $this->service->getMeetingsWithNotices();
        return view('backend.actual_meetings.index', compact('meetings'));
    }
    public function prepareNotice($id)
    {
        $meeting = $this->service->getMeetingWithPapers($id);
        return view('backend.actual_meetings.prepare_notice', compact('meeting'));
    }

    public function publishNotice(Request $request, $id)
    {
        $validated = $request->validate([
            'official_notice' => 'required|string',
            'agenda_order' => 'required|array',
        ]);

        $this->service->saveOfficialNoticeAndAgenda($id, $validated, $request);

        return redirect()->route('actual.meetings.index')->with('success', 'Official notice prepared and meeting published.');
    }
    public function createNotice($id)
    {
        $meeting = BoardMeeting::with('boardPapers')->findOrFail($id);

        return view('backend.actual_meetings.add_notice', compact('meeting'));
    }

    public function storeNotice(Request $request, $id)
    {
        $request->validate([
            'notice_text' => 'required|string',
            'agenda_order' => 'required|array',
            'agenda_order.*' => 'exists:board_papers,id',
            'reference_number' => 'required|string|max:255',
        ]);


        $meeting = BoardMeeting::findOrFail($id);
        $data = [
            'notice_text' => $request->notice_text,
            'agenda_order' => $request->agenda_order,
            'published_at' => now(),
        ];

        ActualMeetingService::saveNotice($meeting, $data, $request);


        return redirect()->route('actual.meetings.index')->with('success', 'Official meeting notice added successfully.');
    }

    public function preview0($id)
    {
        $meeting = BoardMeeting::with(['committee', 'officialNotice', 'boardPapers' => function ($q) {
            $q->orderBy('presentation_order');
        }])->findOrFail($id);


        return response()->json([
            'html' => view('backend.actual_meetings.partials.preview_notice_modal', compact('meeting'))->render()
        ]);
    }
    public function preview1($id)
    {
        $meeting = BoardMeeting::with(['committee', 'officialNotice', 'boardPapers'])->findOrFail($id);

        // Sort board papers based on agenda_order from notice
        $orderedPapers = collect();

        if ($meeting->officialNotice && is_array($meeting->officialNotice->agenda_order)) {
            foreach ($meeting->officialNotice->agenda_order as $paperId) {
                $paper = $meeting->boardPapers->firstWhere('id', $paperId);
                if ($paper) {
                    $orderedPapers->push($paper);
                }
            }
        } else {
            $orderedPapers = $meeting->boardPapers; // fallback: original order
        }

        return response()->json([
            'html' => view('backend.actual_meetings.partials.preview_notice_modal', [
                'meeting' => $meeting,
                'orderedPapers' => $orderedPapers,
            ])->render()
        ]);
    }
    public function preview2($id)
    {
        $meeting = BoardMeeting::with(['committee', 'officialNotice', 'boardPapers'])->findOrFail($id);

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

        // ✅ Return full Blade view, not JSON
        return view('backend.actual_meetings.preview_notice_pdf', [
            'meeting' => $meeting,
            'orderedPapers' => $orderedPapers,
        ]);
    }
    public function preview($id)
    {
        $meeting = BoardMeeting::with(['committee', 'officialNotice', 'boardPapers'])->findOrFail($id);

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
        return view('backend.actual_meetings.partials.preview_notice_modal', [
            'meeting' => $meeting,
            'orderedPapers' => $orderedPapers,
        ]);
    }
    public function send(Request $request,$id)
    {
        $meeting = BoardMeeting::with(['committee.members', 'officialNotice'])->findOrFail($id);

        foreach ($meeting->committee->members as $member) {
            Mail::to($member->email)->queue(new BoardMeetingInvitationMail($meeting));
        }
        // ✅ Update meeting with final notice details
        $meeting->update([
            'reference_number'          => $meeting->reference_number,
            'final_board_notice_sent_at'=> now(),
            'final_notice_sent_by'      => Auth::user()->id,
        ]);

        app(AuditLogService::class)
            ->log($request, 'UPDATE', 'board_meetings', $meeting->id, null, [
                'reference_number'           => $meeting->reference_number,
                'final_board_notice_sent_at' => $meeting->final_board_notice_sent_at,
                'final_notice_sent_by'       => $meeting->final_notice_sent_by,
            ]);
        return redirect()->back()->with('success', 'Invitations sent to all board members successfully.');
    }


    public function captureMinutes($meetingId)
    {
        $meeting = BoardMeeting::with('committee')->findOrFail($meetingId);
        return view('backend.actual_meetings.capture_minutes', compact('meeting'));
    }

    public function storeMinutes0(Request $request, $meetingId)
    {
        $request->validate([
            'discussion' => 'required|string',
            'comments' => 'nullable|string',
            'resolutions' => 'required|string',
            'recommendations' => 'nullable|string',
        ]);

        // Store minutes (create model/table if needed)
        \App\Models\BoardMeetingMinute::create([
            'meeting_id' => $meetingId,
            'user_id' => auth()->id(),
            'discussion' => $request->discussion,
            'comments' => $request->comments,
            'resolutions' => $request->resolutions,
            'recommendations' => $request->recommendations,
        ]);

        return redirect()->route('actual.meetings.index')->with('success', 'Minutes captured successfully.');
    }

    public function create1($meetingId)
    {
        $meeting = BoardMeeting::with('committee.members')->findOrFail($meetingId);
        $boardMembers = $meeting->committee->members;

        return view('backend.actual_meetings.capture_minutes', compact('meeting', 'boardMembers'));
    }
    public function create($meetingId)
    {
//        dd('here');
        $meeting = BoardMeeting::with('committee.members.assignsalutation')->findOrFail($meetingId);

        if (!$meeting->committee) {
            abort(404, 'This meeting has no committee assigned.');
        }

//        dd($meeting->committee);

//        $boardMembers = $meeting->committee->members;
//        // Get board members through the committee relationship
        $boardMembers = $meeting->committee?->members ?? collect();

        return view('backend.actual_meetings.capture_minutes', compact('meeting', 'boardMembers'));
    }

    public function storeMinutes(Request $request, $meetingId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'members_present' => 'array',
            'members_absent' => 'array',
            'members_present.*' => 'exists:board_members,id',
            'members_absent.*' => 'exists:board_members,id',
            'members_in_attendance' => 'nullable|string',
            'minute_items.*.agenda_item' => 'required|string',
            'minute_items.*.description' => 'nullable|string',
            'minute_items.*.comments' => 'nullable|string',
            'minute_items.*.resolution' => 'nullable|string',
            'minute_items.*.proposer' => 'nullable|string',
            'minute_items.*.seconder' => 'nullable|string',
        ]);

        $minute = BoardMeetingMinute::create([
            'meeting_id' => $meetingId,
            'minutes_title' => $request->title,
            'present_members' => $request->members_present,
            'absent_members' => $request->members_absent,
            'in_attendance' => $request->members_in_attendance,
        ]);

        foreach ($request->minute_items as $item) {
            $minute->minuteItems()->create([

                    'minute_number' => $item['minute_number'],
                'agenda_title' => $item['agenda_item'],
                'description' => $item['description'] ?? null,
                'board_comments' => $item['comments'] ?? null,
                'board_resolution' => $item['resolution'] ?? null,
                'proposer' => $item['proposer'] ?? null,
                'seconder' => $item['seconder'] ?? null,
            ]);
        }

//        return redirect()->route('actual.meetings.index')
//            ->with('success', 'Board meeting minutes captured successfully.');

        return redirect()->route('actual.meetings.minutes.capture', $meetingId)
            ->with('success', 'Minutes saved. You may continue.');

    }

    public function previewMinutes($meetingId)
    {
        $meeting = BoardMeeting::with(['minutes.minuteItems'])->findOrFail($meetingId);

        if (!$meeting->minutes) {
            return back()->with('error', 'Minutes not yet started.');
        }

        return view('backend.actual_meetings.preview_minutes', compact('meeting'));
    }



    public function downloadConflictRegister($meetingId)
    {
        $meeting = BoardMeeting::with([
            'committee',
            'conflictDeclarations.boardMember.assignsalutation'
        ])->findOrFail($meetingId);

        $declarations = $meeting->conflictDeclarations;

        $pdf = Pdf::loadView('backend.actual_meetings.pdf.conflict_register', compact('meeting', 'declarations'));

        $filename = 'Conflict_Register_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadAttendanceRegister0($meetingId)
    {
        $meeting = BoardMeeting::with('committee.members.assignsalutation')->findOrFail($meetingId);

        $pdf = PDF::loadView('backend.actual_meetings.pdf.attendance_register', compact('meeting'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Attendance_Register_' . now()->format('Ymd_His') . '.pdf');
    }
    public function downloadAttendanceRegister($meetingId)
    {
        // Get meeting and related data
        $meeting = BoardMeeting::findOrFail($meetingId);

        // Load only board members who signed the conflict of interest register
        $declarations = ConflictOfInterestDeclaration::with('boardMember.assignsalutation')
            ->where('meeting_id', $meeting->id)
            ->get();

        // Extract the board members
        $attendingMembers = $declarations->pluck('boardMember')->filter();

        $pdf = PDF::loadView('backend.actual_meetings.pdf.attendance_register', [
            'meeting' => $meeting,
            'members' => $attendingMembers,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Attendance_Register_' . now()->format('Ymd_His') . '.pdf');
    }
    public function startMeeting(Request $request, $id)
    {
        $meeting = BoardMeeting::findOrFail($id);

        if ($meeting->is_started) {
            return back()->with('info', 'Meeting already started.');
        }

        $meeting->update([
            'is_started' => true,
            'started_at' => now(),
            'started_by' => Auth::id(),
        ]);

        // ✅ Audit log
        app(AuditLogService::class)
            ->log($request, 'UPDATE', 'board_meetings', $meeting->id, null, [
                'is_started' => true,
                'started_at' => $meeting->started_at,
                'started_by' => $meeting->started_by,
            ]);


        return back()->with('success', 'Meeting has been started.');
    }

}
