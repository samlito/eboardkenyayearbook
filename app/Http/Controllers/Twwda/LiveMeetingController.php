<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Http\Services\Twwda\LiveMeetingService;
use App\Models\BoardMember;
use App\Models\Twwda\TwwdaConflictDeclaration;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaMeetingAgendaItem;
use App\Models\Twwda\TwwdaMeetingMinute;
use App\Models\Twwda\TwwdaMinuteItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LiveMeetingController extends Controller
{
    protected LiveMeetingService $meetingService;

    public function __construct(LiveMeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * Start/activate a meeting.
     */
    public function start(TwwdaMeeting $meeting)
    {
        try {
            if ($meeting->is_started) {
                session()->flash('message', 'This meeting has already been started.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.meeting.live', $meeting);
            }

            $this->meetingService->startMeeting($meeting);

            session()->flash('message', 'Meeting has been started successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.live', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Start Meeting error: ' . $e->getMessage());
            session()->flash('message', 'Failed to start meeting.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Live meeting dashboard.
     */
    public function index(TwwdaMeeting $meeting)
    {
        try {
            $meeting->load([
                'boardCommittee',
                'boardPapers' => function ($query) {
                    $query->approved()->orderBy('display_order');
                },
                'boardPapers.author',
                'attendance.boardMember',
                'attendance.user',
                'conflictDeclarations.boardMember',
                'preparation',
                'minutes.items',
            ]);

            // Agenda items visible to admin (not rejected)
            $agendaItems = TwwdaMeetingAgendaItem::where('meeting_id', $meeting->id)
                ->visibleToAdmin()
                ->ordered()
                ->with(['boardPaper.author', 'boardPaper.annexures'])
                ->get();

            // ── Board members from the committee ──
            $committeeMembers = BoardMember::whereHas('committees', function ($q) use ($meeting) {
                $q->where('board_committees.id', $meeting->board_committee_id);
            })->orderBy('names')->get();

            // ── Conflict declarations ──
            $allDeclarations = $meeting->conflictDeclarations;

            // Completed (signed) = board members who are PRESENT
            $completedDeclarations = $allDeclarations
                ->filter(fn($d) => $d->otp_verified_at && $d->signed_at);

            // Board members who haven't completed declarations yet
            $signedMemberIds = $completedDeclarations->pluck('board_member_id')->toArray();
            $pendingMembers = $committeeMembers->filter(fn($m) => !in_array($m->id, $signedMemberIds));

            // ── Attendance: separate board members from other attendees ──
            // Board attendance = records with board_member_id (auto-created on signing)
            $boardAttendance = $meeting->attendance
                ->whereNotNull('board_member_id')
                ->where('is_present', true);

            // Other attendees = staff, invitees, externals (manually added, no board_member_id)
            $otherAttendees = $meeting->attendance
                ->filter(fn($a) => is_null($a->board_member_id));

            $meetingMinutes = $meeting->minutes;

            return view('workflow.live-meeting.index', compact(
                'meeting', 'agendaItems', 'committeeMembers',
                'completedDeclarations', 'allDeclarations', 'pendingMembers',
                'boardAttendance', 'otherAttendees', 'meetingMinutes'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA Live Meeting index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load live meeting dashboard.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meetings.show', $meeting);
        }
    }

    /**
     * Record attendance for a meeting participant.
     */
    public function addAttendance(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'board_member_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'attendee_name' => 'required|string|max:255',
            'attendee_designation' => 'nullable|string|max:255',
            'attendance_type' => 'required|in:board_member,staff,external',
        ]);

        try {
            $this->meetingService->addAttendance($meeting, $request->only([
                'board_member_id', 'user_id', 'attendee_name',
                'attendee_designation', 'attendance_type',
            ]));

            session()->flash('message', 'Attendance recorded successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.live', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Add Attendance error: ' . $e->getMessage());
            session()->flash('message', 'Failed to record attendance.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Toggle attendance present/absent for a meeting participant (AJAX).
     */
    public function toggleAttendance(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'attendee_id' => 'required|integer|exists:twwda_meeting_attendance,id',
            'present' => 'required|boolean',
        ]);

        try {
            $attendance = \App\Models\Twwda\TwwdaMeetingAttendance::where('id', $request->attendee_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $attendance->update([
                'is_present' => $request->present,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance updated successfully.',
                ]);
            }

            session()->flash('message', 'Attendance updated.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.live', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Toggle Attendance error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update attendance.',
                ], 500);
            }

            session()->flash('message', 'Failed to update attendance.'); session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Conflict of interest declaration form.
     */
    public function conflictForm(Request $request, TwwdaMeeting $meeting)
    {
        try {
            $meeting->load(['boardCommittee', 'conflictDeclarations.boardMember']);

            // Load all board members for selection
            $boardMembers = BoardMember::orderBy('names')->get();

            // Determine selected board member: from query param, or from current user's board member record
            $boardMember = null;
            if ($request->has('board_member_id')) {
                $boardMember = BoardMember::find($request->board_member_id);
            } else {
                $boardMember = BoardMember::where('user_id', Auth::id())->first();
            }

            // Find existing declaration for the selected board member in this meeting
            $declaration = null;
            if ($boardMember) {
                $declaration = TwwdaConflictDeclaration::where('meeting_id', $meeting->id)
                    ->where('board_member_id', $boardMember->id)
                    ->first();
            }

            return view('workflow.live-meeting.conflict', compact('meeting', 'boardMembers', 'boardMember', 'declaration'));
        } catch (\Exception $e) {
            Log::error('TWWDA Conflict Form error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load conflict declaration form.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meeting.live', $meeting);
        }
    }

    /**
     * Submit a conflict of interest declaration.
     */
    public function submitConflict(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'board_member_id' => 'required|integer',
            'has_conflict' => 'required|boolean',
            'person_name' => 'required_if:has_conflict,1|nullable|string|max:255',
            'person_address' => 'nullable|string|max:500',
            'interest_nature' => 'required_if:has_conflict,1|nullable|string|max:2000',
            'interest_value' => 'nullable|string|max:255',
            'interest_ceased_on' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            $declaration = $this->meetingService->submitConflict($meeting, $request->only([
                'board_member_id', 'has_conflict',
                'person_name', 'person_address', 'interest_nature',
                'interest_value', 'interest_ceased_on', 'remarks',
            ]));

            // Send OTP for verification
            $this->meetingService->sendOtp($declaration);

            session()->flash('message', 'Conflict declaration submitted. An OTP has been sent for verification.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.conflict', ['meeting' => $meeting, 'board_member_id' => $request->board_member_id]);
        } catch (\Exception $e) {
            Log::error('TWWDA Submit Conflict error: ' . $e->getMessage());
            session()->flash('message', 'Failed to submit conflict declaration.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    /**
     * Resend OTP for conflict declaration (AJAX).
     */
    public function resendOtp(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'declaration_id' => 'required|integer|exists:twwda_conflict_declarations,id',
        ]);

        try {
            $declaration = TwwdaConflictDeclaration::where('id', $request->declaration_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $this->meetingService->sendOtp($declaration);

            return response()->json([
                'success' => true,
                'message' => 'OTP has been resent to the board member\'s email.',
            ]);
        } catch (\Exception $e) {
            Log::error('TWWDA Resend OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP.',
            ], 500);
        }
    }

    /**
     * Verify OTP for conflict declaration (AJAX).
     */
    public function verifyOtp(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'declaration_id' => 'required|integer|exists:twwda_conflict_declarations,id',
            'otp_code' => 'required|string|size:6',
        ]);

        try {
            $declaration = TwwdaConflictDeclaration::where('id', $request->declaration_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $verified = $this->meetingService->verifyOtp($declaration, $request->otp_code);

            $redirectParams = ['meeting' => $meeting, 'board_member_id' => $declaration->board_member_id];

            if ($verified) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP verified successfully.',
                    ]);
                }

                session()->flash('message', 'OTP verified successfully. Please sign your declaration.'); session()->flash('alert-type', 'success');
                return redirect()->route('workflow.meeting.conflict', $redirectParams);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please try again.',
                ], 422);
            }

            session()->flash('message', 'Invalid or expired OTP. Please try again.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meeting.conflict', $redirectParams);
        } catch (\Exception $e) {
            Log::error('TWWDA Verify OTP error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to verify OTP.',
                ], 500);
            }

            session()->flash('message', 'Failed to verify OTP.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meeting.conflict', $meeting);
        }
    }

    /**
     * Sign conflict declaration with stylus signature (AJAX).
     */
    public function signDeclaration(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'declaration_id' => 'required|integer|exists:twwda_conflict_declarations,id',
            'signature' => 'required|string',
        ]);

        try {
            $declaration = TwwdaConflictDeclaration::where('id', $request->declaration_id)
                ->where('meeting_id', $meeting->id)
                ->firstOrFail();

            $redirectParams = ['meeting' => $meeting, 'board_member_id' => $declaration->board_member_id];

            if (!$declaration->isVerified()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP must be verified before signing.',
                    ], 422);
                }

                session()->flash('message', 'OTP must be verified before signing.'); session()->flash('alert-type', 'error');
                return redirect()->route('workflow.meeting.conflict', $redirectParams);
            }

            $this->meetingService->signDeclaration($declaration, $request->signature);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Declaration signed successfully.',
                ]);
            }

            session()->flash('message', 'Conflict declaration signed successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.conflict', $redirectParams);
        } catch (\Exception $e) {
            Log::error('TWWDA Sign Declaration error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sign declaration.',
                ], 500);
            }

            session()->flash('message', 'Failed to sign declaration.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meeting.conflict', $meeting);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  EXPORT — Board Attendance Register
    //  Board members present = those who completed conflict signing
    // ═══════════════════════════════════════════════════════════════

    /**
     * Export board attendance register as PDF.
     */
    public function exportAttendancePdf(TwwdaMeeting $meeting)
    {
        $meeting->load(['boardCommittee', 'conflictDeclarations.boardMember']);

        // Board members present = signed conflict declarations
        $declarations = $meeting->conflictDeclarations
            ->filter(fn($d) => $d->otp_verified_at && $d->signed_at)
            ->sortBy(fn($d) => optional($d->boardMember)->names)
            ->values();

        $pdf = Pdf::loadView('workflow.live-meeting.exports.attendance-pdf', compact('meeting', 'declarations'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Board_Attendance_Register_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $meeting->title) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export board attendance register as CSV (opens in Excel).
     */
    public function exportAttendanceCsv(TwwdaMeeting $meeting): StreamedResponse
    {
        $meeting->load(['boardCommittee', 'conflictDeclarations.boardMember']);

        $declarations = $meeting->conflictDeclarations
            ->filter(fn($d) => $d->otp_verified_at && $d->signed_at)
            ->sortBy(fn($d) => optional($d->boardMember)->names)
            ->values();

        $filename = 'Board_Attendance_Register_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $meeting->title) . '.csv';

        return response()->streamDownload(function () use ($meeting, $declarations) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 recognition
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header info
            fputcsv($handle, ['TWWDA Board Meeting — Board Attendance Register']);
            fputcsv($handle, ['Meeting:', $meeting->title]);
            fputcsv($handle, ['Committee:', optional($meeting->boardCommittee)->name ?? '']);
            fputcsv($handle, ['Date:', $meeting->meeting_date ? Carbon::parse($meeting->meeting_date)->format('d M Y') : '']);
            fputcsv($handle, ['Venue:', $meeting->venue ?? '']);
            fputcsv($handle, []);

            // Table header
            fputcsv($handle, ['#', 'Board Member', 'Designation', 'Email', 'Has Conflict', 'Signed At']);

            foreach ($declarations as $i => $d) {
                $memberName = optional($d->boardMember)->names ?? '---';
                $designation = optional($d->boardMember)->designation ?? '';
                $email = optional($d->boardMember)->email ?? '';
                fputcsv($handle, [
                    $i + 1,
                    $memberName,
                    $designation,
                    $email,
                    $d->has_conflict ? 'Yes' : 'No',
                    $d->signed_at ? Carbon::parse($d->signed_at)->format('d M Y H:i') : '',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Total Present:', $declarations->count()]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    //  EXPORT — Conflict of Interest Register
    // ═══════════════════════════════════════════════════════════════

    /**
     * Export conflict register as PDF.
     */
    public function exportConflictPdf(TwwdaMeeting $meeting)
    {
        $meeting->load(['boardCommittee', 'conflictDeclarations.boardMember']);
        $declarations = $meeting->conflictDeclarations
            ->filter(fn($d) => $d->otp_verified_at && $d->signed_at)
            ->values();

        $pdf = Pdf::loadView('workflow.live-meeting.exports.conflict-pdf', compact('meeting', 'declarations'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Conflict_Register_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $meeting->title) . '.pdf';
        return $pdf->download($filename);
    }

    // ═══════════════════════════════════════════════════════════════
    //  MINUTES — Capture, Save, Export
    // ═══════════════════════════════════════════════════════════════

    public function saveMinutes(Request $request, TwwdaMeeting $meeting)
    {
        $request->validate([
            'minutes_title' => 'required|string|max:500',
            'in_attendance' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.agenda_item_id' => 'nullable|integer',
            'items.*.minute_number' => 'nullable|string|max:50',
            'items.*.agenda_title' => 'required|string|max:500',
            'items.*.discussion' => 'nullable|string',
            'items.*.comments' => 'nullable|string',
            'items.*.resolution' => 'nullable|string',
            'items.*.proposer' => 'nullable|string|max:255',
            'items.*.seconder' => 'nullable|string|max:255',
        ]);

        try {
            $meeting->load(['conflictDeclarations.boardMember']);

            $completedDeclarations = $meeting->conflictDeclarations
                ->filter(fn($d) => $d->otp_verified_at && $d->signed_at);

            $committeeMembers = BoardMember::whereHas('committees', function ($q) use ($meeting) {
                $q->where('board_committees.id', $meeting->board_committee_id);
            })->orderBy('names')->get();

            $signedMemberIds = $completedDeclarations->pluck('board_member_id')->toArray();

            $presentMembers = $completedDeclarations->map(fn($d) => [
                'id' => $d->board_member_id,
                'name' => optional($d->boardMember)->names,
                'designation' => optional($d->boardMember)->designation,
            ])->values()->toArray();

            $absentMembers = $committeeMembers->filter(fn($m) => !in_array($m->id, $signedMemberIds))
                ->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->names,
                    'designation' => $m->designation,
                ])->values()->toArray();

            $minutes = TwwdaMeetingMinute::updateOrCreate(
                ['meeting_id' => $meeting->id],
                [
                    'minutes_title' => $request->minutes_title,
                    'present_members' => $presentMembers,
                    'absent_members' => $absentMembers,
                    'in_attendance' => $request->in_attendance,
                    'status' => 'draft',
                    'created_by' => Auth::id(),
                ]
            );

            $minutes->items()->delete();

            foreach ($request->items as $index => $item) {
                $minutes->items()->create([
                    'agenda_item_id' => $item['agenda_item_id'] ?? null,
                    'minute_number' => $item['minute_number'] ?? ('MIN/' . ($index + 1)),
                    'agenda_title' => $item['agenda_title'],
                    'discussion' => $item['discussion'] ?? null,
                    'comments' => $item['comments'] ?? null,
                    'resolution' => $item['resolution'] ?? null,
                    'proposer' => $item['proposer'] ?? null,
                    'seconder' => $item['seconder'] ?? null,
                    'display_order' => $index,
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Minutes saved.',
                    'minutes_id' => $minutes->id,
                ]);
            }

            session()->flash('message', 'Minutes saved successfully.'); session()->flash('alert-type', 'success');
            return redirect()->route('workflow.meeting.live', $meeting);
        } catch (\Exception $e) {
            Log::error('TWWDA Save Minutes error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save: ' . $e->getMessage(),
                ], 500);
            }

            session()->flash('message', 'Failed to save minutes.'); session()->flash('alert-type', 'error');
            return back()->withInput();
        }
    }

    public function exportMinutesWord(TwwdaMeeting $meeting)
    {
        $meeting->load(['boardCommittee', 'minutes.items', 'conflictDeclarations.boardMember', 'attendance']);

        $minutes = $meeting->minutes;
        if (!$minutes) {
            session()->flash('message', 'No minutes have been captured yet.'); session()->flash('alert-type', 'error');
            return redirect()->route('workflow.meeting.live', $meeting);
        }

        $institutionName = e(setting('institution_name', 'eBoard System'));
        $committeeName = e($meeting->boardCommittee->name ?? 'N/A');
        $meetingDate = $meeting->meeting_date ? Carbon::parse($meeting->meeting_date)->format('l, jS F Y') : 'N/A';
        $meetingTime = $meeting->meeting_time ? Carbon::parse($meeting->meeting_time)->format('g:i A') : 'N/A';
        $venue = e($meeting->venue ?? 'N/A');
        $minutesTitle = e($minutes->minutes_title ?? 'Board Meeting Minutes');
        $ceoTitle = e(strtoupper(setting('signatory_title_ceo', 'Chief Executive Officer')));
        $chairTitle = e(strtoupper(setting('signatory_title_chairperson', 'Chairperson')));

        $presentMembers = $minutes->present_members ?? [];
        $absentMembers = $minutes->absent_members ?? [];
        $inAttendance = e($minutes->in_attendance ?? '');

        $presentHtml = '';
        foreach ($presentMembers as $m) {
            $n = e($m['name'] ?? 'N/A');
            $d = e($m['designation'] ?? '');
            $presentHtml .= '<li>' . $n . ($d ? ' &mdash; ' . $d : '') . '</li>';
        }
        if (!$presentHtml) $presentHtml = '<li>None</li>';

        $absentHtml = '';
        foreach ($absentMembers as $m) {
            $n = e($m['name'] ?? 'N/A');
            $d = e($m['designation'] ?? '');
            $absentHtml .= '<li>' . $n . ($d ? ' &mdash; ' . $d : '') . '</li>';
        }
        if (!$absentHtml) $absentHtml = '<li>None</li>';

        $itemsHtml = '';
        foreach ($minutes->items as $item) {
            $disc = e(strip_tags($item->discussion ?? ''));
            $comm = e(strip_tags($item->comments ?? ''));
            $res = e(strip_tags($item->resolution ?? ''));
            $prop = e(strip_tags($item->proposer ?? ''));
            $sec = e(strip_tags($item->seconder ?? ''));

            $discBlock = nl2br($disc);
            if ($comm) {
                $discBlock .= '<br><i>Comments: ' . nl2br($comm) . '</i>';
            }

            $propSec = '';
            if ($prop) $propSec .= 'P: ' . $prop;
            if ($sec) $propSec .= ($propSec ? '<br>' : '') . 'S: ' . $sec;

            $itemsHtml .= '<tr>'
                . '<td style="text-align:center;">' . e(strip_tags($item->minute_number ?? '')) . '</td>'
                . '<td><b>' . e(strip_tags($item->agenda_title ?? '')) . '</b></td>'
                . '<td>' . $discBlock . '</td>'
                . '<td>' . nl2br($res) . '</td>'
                . '<td style="font-size:10pt;">' . $propSec . '</td>'
                . '</tr>';
        }

        $html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<meta name="ProgId" content="Word.Document">
<meta name="Generator" content="eBoard System">
<!--[if gte mso 9]>
<xml>
<w:WordDocument>
<w:View>Print</w:View>
<w:Zoom>100</w:Zoom>
<w:DoNotOptimizeForBrowser/>
</w:WordDocument>
</xml>
<![endif]-->
<style>
    @page { size: A4; margin: 1in; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; }
    h1 { font-size: 16pt; text-align: center; margin-bottom: 5px; }
    h2 { font-size: 13pt; text-align: center; margin-top: 0; margin-bottom: 15px; }
    h3 { font-size: 12pt; text-decoration: underline; margin-bottom: 5px; margin-top: 15px; }
    .meta { margin-bottom: 3px; }
    ul { margin-top: 5px; }
    table.minutes { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.minutes th, table.minutes td { border: 1px solid #000; padding: 5px; vertical-align: top; }
    table.minutes th { background-color: #1d2b5c; color: #fff; font-size: 11pt; }
    table.sign { width: 100%; margin-top: 40px; }
    table.sign td { width: 50%; vertical-align: top; padding: 10px 0; }
    .sign-line { margin-bottom: 5px; }
</style>
</head>
<body>

<h1>{$institutionName}</h1>
<h2>{$minutesTitle}</h2>

<p class="meta"><b>Committee:</b> {$committeeName}</p>
<p class="meta"><b>Date:</b> {$meetingDate}</p>
<p class="meta"><b>Time:</b> {$meetingTime}</p>
<p class="meta"><b>Venue:</b> {$venue}</p>

<h3>MEMBERS PRESENT</h3>
<ol>{$presentHtml}</ol>

<h3>MEMBERS ABSENT WITH APOLOGY</h3>
<ol>{$absentHtml}</ol>
HTML;

        if ($inAttendance) {
            $html .= '<h3>IN ATTENDANCE</h3><p>' . nl2br($inAttendance) . '</p>';
        }

        $html .= <<<HTML

<h3>MINUTES</h3>
<table class="minutes">
<tr>
    <th style="width:10%;">Minute No.</th>
    <th style="width:22%;">Agenda Item</th>
    <th style="width:30%;">Discussion</th>
    <th style="width:25%;">Resolution</th>
    <th style="width:13%;">Proposed / Seconded</th>
</tr>
{$itemsHtml}
</table>

<table class="sign">
<tr>
    <td>
        <p class="sign-line">Signed: …………………………………………..</p>
        <p class="sign-line">Date: ……………………………</p>
        <p><b>{$ceoTitle} / Secretary to the Board</b></p>
    </td>
    <td>
        <p class="sign-line">Signed: …………………………………………..</p>
        <p class="sign-line">Date: ……………………………</p>
        <p><b>{$chairTitle}</b></p>
    </td>
</tr>
</table>

</body>
</html>
HTML;

        $filename = 'Minutes_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $meeting->title) . '.doc';

        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Export conflict register as CSV (opens in Excel).
     */
    public function exportConflictCsv(TwwdaMeeting $meeting): StreamedResponse
    {
        $meeting->load(['boardCommittee', 'conflictDeclarations.boardMember']);
        $declarations = $meeting->conflictDeclarations
            ->filter(fn($d) => $d->otp_verified_at && $d->signed_at)
            ->values();

        $filename = 'Conflict_Register_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $meeting->title) . '.csv';

        return response()->streamDownload(function () use ($meeting, $declarations) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['TWWDA Board Meeting — Conflict of Interest Register']);
            fputcsv($handle, ['Meeting:', $meeting->title]);
            fputcsv($handle, ['Committee:', optional($meeting->boardCommittee)->name ?? '']);
            fputcsv($handle, ['Date:', $meeting->meeting_date ? Carbon::parse($meeting->meeting_date)->format('d M Y') : '']);
            fputcsv($handle, []);

            fputcsv($handle, [
                '#', 'Member Name', 'Designation', 'Has Conflict',
                'Person Name', 'Person Address', 'Nature of Interest',
                'Value (KES)', 'Interest Ceased On', 'Remarks',
                'OTP Verified At', 'Signed At',
            ]);

            foreach ($declarations as $i => $d) {
                $memberName = optional($d->boardMember)->names ?? '---';
                $designation = optional($d->boardMember)->designation ?? '';
                fputcsv($handle, [
                    $i + 1,
                    $memberName,
                    $designation,
                    $d->has_conflict ? 'Yes' : 'No',
                    $d->person_name ?? '',
                    $d->person_address ?? '',
                    $d->interest_nature ?? '',
                    $d->interest_value ?? '',
                    $d->interest_ceased_on ? Carbon::parse($d->interest_ceased_on)->format('d M Y') : '',
                    $d->remarks ?? '',
                    $d->otp_verified_at ? Carbon::parse($d->otp_verified_at)->format('d M Y H:i') : '',
                    $d->signed_at ? Carbon::parse($d->signed_at)->format('d M Y H:i') : '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
