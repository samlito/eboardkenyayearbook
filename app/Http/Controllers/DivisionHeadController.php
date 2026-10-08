<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use App\Models\BoardCommittee;
use App\Models\BoardMeeting;
use App\Models\BoardMeetingAssignment;
use App\Models\BoardPaper;
use App\Models\BoardPaperAnnexure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DivisionHeadController extends Controller
{
    //
    public function index2()
    {
        $assignments = BoardMeetingAssignment::with('meeting')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $submitted = BoardPaper::where('user_id', Auth::id())->pluck('meeting_id')->toArray();



        return view('backend.division_heads.my_meetings', compact('assignments', 'submitted'));
    }
    public function index0()
    {
        $userId = Auth::id();

        $assignments = BoardMeetingAssignment::with(['meeting' => function ($q) {
            $q->select('id', 'title', 'date');
        }])
            ->where('user_id', $userId)
            ->whereHas('meeting')
            ->latest()
            ->get();

        // meeting_id => count
        $submittedCounts = BoardPaper::where('user_id', $userId)
            ->select('meeting_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('meeting_id')
            ->pluck('cnt', 'meeting_id');

        return view('backend.division_heads.my_meetings', compact('assignments', 'submittedCounts'));
    }
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;

        $isCS = $user->hasRole('Corporation Secretary'); // Spatie

        // If CS, pull all user IDs in the Legal division; otherwise just current user.
//        $targetUserIds = $isCS
//            ? User::whereHas('division', function ($q) {
//                $q->where('name', 'Legal');
//            })->pluck('id')
//            : collect([$userId]);
        $targetUserIds = $isCS
            ? User::whereHas('division', fn($q) => $q->where('name', 'Legal'))
                ->pluck('id')
                ->push($userId)
                ->unique()
            : collect([$userId]);


        // Assignments for those target users (only meetings that exist)
        $assignments = BoardMeetingAssignment::with(['meeting' => function ($q) {
            $q->select('id', 'title', 'date');
        }])
            ->whereIn('user_id', $targetUserIds)
            ->whereHas('meeting')
            ->latest()
            ->get();

        // meeting_id => count of submitted papers by those users
        $submittedCounts = BoardPaper::whereIn('user_id', $targetUserIds)
            ->select('meeting_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('meeting_id')
            ->pluck('cnt', 'meeting_id');

        return view('backend.division_heads.my_meetings', compact('assignments', 'submittedCounts'));
    }
    public function indexAudit()
    {
        $assignments = BoardMeetingAssignment::with(['meeting', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('division_id', 1); // Internal Audit division
            })
            ->latest()
            ->get();


        $submitted = BoardPaper::where('user_id', Auth::id())->pluck('meeting_id')->toArray();



        return view('backend.division_heads.audit_meetings', compact('assignments', 'submitted'));
    }

    public function create0($meeting_id)
    {
        $meeting = BoardMeeting::findOrFail($meeting_id);

        // Check if assigned
        $assigned = BoardMeetingAssignment::where('user_id', Auth::id())
            ->where('meeting_id', $meeting_id)
            ->exists();

        if (!$assigned) {
            abort(403, 'You are not assigned to this meeting.');
        }

        return view('backend.division_heads.submit_form', compact('meeting'));
    }

    public function create($meeting_id)
    {
        $meeting = BoardMeeting::findOrFail($meeting_id);
        $user = Auth::user();

        $isCS = $user->hasRole('Corporation Secretary');

        if ($isCS) {
            // Allow if any assignee for this meeting belongs to division "Legal"
            $legalAssigned = BoardMeetingAssignment::where('meeting_id', $meeting_id)
                ->whereIn('user_id', function ($q) {
                    $q->select('id')->from('users')
                        ->whereIn('division_id', function ($q2) {
                            $q2->select('id')->from('divisions')->where('name', 'Legal');
                        });
                })
                ->exists();

            if ($legalAssigned) {
                return view('backend.division_heads.submit_form', compact('meeting'));
            }

            abort(403, 'No Legal division assignees for this meeting.');
        }

        // Non-CS: must be assigned
        $assigned = BoardMeetingAssignment::where('user_id', $user->id)
            ->where('meeting_id', $meeting_id)
            ->exists();

        if (!$assigned) {
            abort(403, 'You are not assigned to this meeting.');
        }

        return view('backend.division_heads.submit_form', compact('meeting'));
    }
    public function store(Request $request, $meeting_id)
    {

        $request->validate([
            'title' => 'required|string',
            'file' => 'required|file|mimes:pdf',
            'annexures' => 'nullable|array',
            'annexures.*.title' => 'nullable|required_with:annexures.*.file|string',
            'annexures.*.file'  => 'nullable|required_with:annexures.*.title|file|mimes:pdf,xls,xlsx,doc,docx',

        ]);


        // Save main paper
        $mainFile = $request->file('file')->store('board_papers', 'public');
        $meeting = BoardMeeting::findOrFail($meeting_id);

        // Status depends on committee
        $auditCommitteeIds = BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
        $status = in_array($meeting->board_committee_id, $auditCommitteeIds) ? 'Approved' : 'Pending';

        $boardPaper = BoardPaper::create([
            'meeting_id' => $meeting_id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'file' => $mainFile,
//            'status' => 'Pending',
            'status'     => $status,
        ]);

        // ✅ Save annexures
        if ($request->has('annexures')) {
            foreach ($request->annexures as $index => $annexure) {
                if (isset($annexure['title']) && $request->file("annexures.$index.file")) {
                    $filePath = $request->file("annexures.$index.file")->store('board_annexures', 'public');

                    BoardPaperAnnexure::create([
                        'board_paper_id' => $boardPaper->id,
                        'title' => $annexure['title'],
                        'description' => $annexure['description'] ?? null,
                        'file' => $filePath,
                    ]);
                }
            }
        }
// ✅ Log the submission
        AuditLogService::log(
            $request,
            'INSERT',
            'board_papers',
            $boardPaper->id,
            null,
            [
                'action'     => 'Board paper submitted',
                'meeting'    => $meeting->title,
                'title'      => $boardPaper->title,
                'submitted_by' => Auth::user()->name ?? 'System',
                'submitted_at' => now()->toDateTimeString()
            ]
        );
        return redirect()->route('division.head.meetings')
            ->with('success', 'Board paper and annexures submitted successfully.');
    }


    public function submissions0($meeting_id)
    {

        $meeting = BoardMeeting::findOrFail($meeting_id);

        // Get board papers + annexures for this user & meeting
        $papers = BoardPaper::with('annexures')
            ->where('meeting_id', $meeting_id)
            ->where('user_id', Auth::id())
            ->get();

        return view('backend.division_heads.submissions', compact('meeting', 'papers'));
    }
    public function submissions($meeting_id)
    {
        $meeting = BoardMeeting::findOrFail($meeting_id);

        $user = Auth::user();
        $isCS = $user->hasRole('Corporation Secretary');

        // Build target user IDs:
        // - CS: self + all users in division "Legal"
        // - Others: self only
        $targetUserIds = $isCS
            ? User::whereHas('division', fn($q) => $q->where('name', 'Legal'))
                ->pluck('id')
                ->push($user->id)
                ->unique()
            : collect([$user->id]);

        // Fetch papers (meeting + target users), include annexures and (optionally) authors
        $papers = BoardPaper::with(['annexures', 'user.division']) // 'user' relation helps in the view
        ->where('meeting_id', $meeting_id)
            ->whereIn('user_id', $targetUserIds)
            ->latest()
            ->get();

        return view('backend.division_heads.submissions', compact('meeting', 'papers'));
    }
    public function submissionsAudit($meeting_id)
    {
        $meeting = BoardMeeting::findOrFail($meeting_id);

        // Get board papers + annexures for this user & meeting
        $papers = BoardPaper::with('annexures')
            ->where('meeting_id', $meeting_id)
//            ->where('user_id', Auth::id())
            ->get();

        return view('backend.division_heads.submissions_audit', compact('meeting', 'papers'));
    }






    public function editPaper(BoardPaper $paper)
    {
        // Authorization: only owner can edit
        abort_unless($paper->user_id === Auth::id(), 403, 'Not allowed');

        $paper->load('annexures');
        return view('backend.division_heads.partials.edit_paper_modal', compact('paper'));
    }

    public function updatePaper0(Request $request, BoardPaper $paper)
    {
        abort_unless($paper->user_id === Auth::id(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:20480', // 20MB
        ]);

        DB::transaction(function () use ($paper, $data, $request) {
            $old = $paper->replicate();

            $paper->title       = $data['title'];
//            $paper->description = $data['description'] ?? $paper->description;

            if ($request->hasFile('file')) {
                // delete old file
                if ($paper->file && Storage::disk('public')->exists($paper->file)) {
                    Storage::disk('public')->delete($paper->file);
                }
                $path = $request->file('file')->store('board_papers', 'public');
                $paper->file = $path;
            }

            // When editing after submission, you may want to reset status to "Resubmitted" or keep as is.
            // $paper->status = 'Resubmitted';

            $paper->save();

            // Audit (optional)
            // app(AuditLogService::class)->log('board_papers', $paper->id, 'UPDATE', $old->toArray(), $paper->toArray());
        });

        return back()->with('success', 'Board paper updated.');
    }

    public function storeAnnexure0(Request $request, BoardPaper $paper)
    {
        abort_unless($paper->user_id === Auth::id(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file'        => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        $path = $request->file('file')->store('board_annexures', 'public');

        $annexure = $paper->annexures()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'file'        => $path,
        ]);

        // Audit (optional)
        // app(AuditLogService::class)->log('board_annexures', $annexure->id, 'INSERT', null, $annexure->toArray());

        return back()->with('success', 'Annexure added.');
    }
    public function updatePaper(Request $request, BoardPaper $paper)
    {
        abort_unless($paper->user_id === Auth::id(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:20480',
        ]);

        DB::transaction(function () use ($request, $paper, $data) {
            // snapshot BEFORE changes (only the columns you care to audit)
            $oldValues = $paper->only([
                'meeting_id','user_id','title','file','supporting_docs','status','ceo_comments','ceo_notified_at'
            ]);

            // apply changes
            $paper->title = $data['title'];
            if (array_key_exists('description', $data)) {
                // uncomment if you actually have a `description` column
                // $paper->description = $data['description'];
            }

            if ($request->hasFile('file')) {
                if ($paper->file && Storage::disk('public')->exists($paper->file)) {
                    Storage::disk('public')->delete($paper->file);
                }
                $paper->file = $request->file('file')->store('board_papers', 'public');
            }

            // optional: auto status transition on edit
            // if ($paper->status === 'Approved') $paper->status = 'Resubmitted';

            $paper->save();

            // snapshot AFTER changes
            $newValues = $paper->only([
                'meeting_id','user_id','title','file','supporting_docs','status','ceo_comments','ceo_notified_at'
            ]);

            // AUDIT
            AuditLogService::log(
                $request,
                action:   'UPDATE',
                table:    'board_papers',
                recordId: $paper->id,
                oldValues: $oldValues,
                newValues: $newValues
            );
        });

        return back()->with('success', 'Board paper updated.');
    }

    public function storeAnnexure(Request $request, BoardPaper $paper)
    {
        abort_unless($paper->user_id === Auth::id(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file'        => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        $annexure = null;

        DB::transaction(function () use ($request, $paper, $data, &$annexure) {
            $path = $request->file('file')->store('board_annexures', 'public');

            $annexure = $paper->annexures()->create([
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'file'        => $path,
            ]);

            // AUDIT (INSERT: no oldValues)
            AuditLogService::log(
                $request,
                action:   'INSERT',
                table:    'board_paper_annexures',
                recordId: $annexure->id,
                oldValues: null,
                newValues: $annexure->only(['board_paper_id','title','description','file'])
            );
        });

        return back()->with('success', 'Annexure added.');
    }

    public function updateAnnexure(Request $request, BoardPaperAnnexure $annexure)
    {
        // Authorization: only owner of the parent paper can edit
        abort_unless(optional($annexure->boardPaper)->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        DB::transaction(function () use ($request, $annexure, $validated) {
            // Capture old values for audit
            $oldValues = $annexure->only(['board_paper_id', 'title', 'description', 'file']);

            // Apply simple fields
            $annexure->title       = $validated['title'];
            $annexure->description = $validated['description'] ?? $annexure->description;

            // Handle file replacement (optional)
            if ($request->hasFile('file')) {
                // delete old file if present
                if ($annexure->file && Storage::disk('public')->exists($annexure->file)) {
                    Storage::disk('public')->delete($annexure->file);
                }
                $annexure->file = $request->file('file')->store('board_annexures', 'public');
            }

            $annexure->save();

            // Audit
            AuditLogService::log(
                $request,
                action:    'UPDATE',
                table:     'board_paper_annexures',
                recordId:  $annexure->id,
                oldValues: $oldValues,
                newValues: $annexure->only(['board_paper_id', 'title', 'description', 'file'])
            );

            // (Optional) if editing implies resubmission, you can update parent status here:
            // $paper = $annexure->boardPaper;
            // if ($paper && $paper->status === 'Approved') {
            //     $paper->status = 'Resubmitted';
            //     $paper->save();
            // }
        });

        return back()->with('success', 'Annexure updated.');
    }

    public function destroyAnnexure(Request $request, BoardPaperAnnexure $annexure)
    {
        // Authorization: only owner of the parent paper can delete
        abort_unless(optional($annexure->boardPaper)->user_id === Auth::id(), 403);

        DB::transaction(function () use ($request, $annexure) {
            $oldValues = $annexure->only(['board_paper_id', 'title', 'description', 'file']);

            // If not using SoftDeletes and you want to remove the file physically:
            if ($annexure->file && Storage::disk('public')->exists($annexure->file)) {
                Storage::disk('public')->delete($annexure->file);
            }

            $annexure->delete();

            // Audit
            AuditLogService::log(
                $request,
                action:    'DELETE',
                table:     'board_paper_annexures',
                recordId:  $annexure->id,
                oldValues: $oldValues,
                newValues: null
            );
        });

        return back()->with('success', 'Annexure removed.');
    }
}
