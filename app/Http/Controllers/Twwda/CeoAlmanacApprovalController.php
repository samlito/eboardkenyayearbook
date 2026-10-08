<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use App\Models\Almanac;
use App\Models\AlmanacSchedule;
use App\Models\ReviewComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CeoAlmanacApprovalController extends Controller
{
    /**
     * List all pending almanacs for CEO approval (same data as V1).
     */
    public function index()
    {
        try {
            $pendingAlmanacs = Almanac::where('status', 'Awaiting Approval')
                ->with('financialyear')
                ->latest()
                ->get();

            $approvedAlmanacs = Almanac::where('status', 'Approved')
                ->with('financialyear')
                ->latest()
                ->get();

            $rejectedAlmanacs = Almanac::where('status', 'Rejected')
                ->with('financialyear')
                ->latest()
                ->get();

            $stats = [
                'pending' => $pendingAlmanacs->count(),
                'approved' => $approvedAlmanacs->count(),
                'rejected' => $rejectedAlmanacs->count(),
                'total' => $pendingAlmanacs->count() + $approvedAlmanacs->count() + $rejectedAlmanacs->count(),
            ];

            return view('workflow.almanac-approval.index', compact(
                'pendingAlmanacs',
                'approvedAlmanacs',
                'rejectedAlmanacs',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Almanac Approval index error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load almanac approvals.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }

    /**
     * Show almanac detail with all schedules and approval form (same as V1 showpending).
     */
    public function show(Almanac $almanac)
    {
        try {
            $almanac->load(['financialyear', 'reviewComments']);

            $schedules = AlmanacSchedule::where('almanac_id', $almanac->id)
                ->with('boardCommittee')
                ->orderBy('meeting_date')
                ->get();

            $totalScheduleBudget = $schedules->sum('budget');
            $remainingBudget = $almanac->budget - $totalScheduleBudget;

            $rejectedComments = ReviewComment::where('almanac_id', $almanac->id)
                ->orderBy('id', 'desc')
                ->get();

            // Group schedules by committee for display
            $groupedSchedules = $schedules->groupBy(function ($schedule) {
                return $schedule->boardCommittee->name ?? 'Unknown';
            });

            return view('workflow.almanac-approval.show', compact(
                'almanac',
                'schedules',
                'groupedSchedules',
                'totalScheduleBudget',
                'remainingBudget',
                'rejectedComments'
            ));
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Almanac show error: ' . $e->getMessage());
            session()->flash('message', 'Failed to load almanac details.');
            session()->flash('alert-type', 'error');
            return redirect()->route('workflow.ceo.almanac');
        }
    }

    /**
     * CEO approve or reject almanac (same logic as V1).
     */
    public function decide(Request $request, Almanac $almanac)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|required_if:status,Rejected|string|max:1000',
        ]);

        try {
            $almanac->status = $request->status;
            $almanac->ceo_approval_date = Carbon::now();
            $almanac->save();

            if ($request->status === 'Rejected' && $request->comment) {
                ReviewComment::create([
                    'almanac_id' => $almanac->id,
                    'comment' => $request->comment,
                    'comment_date' => now(),
                    'status' => 'Rejected',
                ]);
            }

            $msg = $request->status === 'Approved'
                ? 'Almanac approved successfully.'
                : 'Almanac rejected. Review comment has been recorded.';

            session()->flash('message', $msg);
            session()->flash('alert-type', 'success');
            return redirect()->route('workflow.ceo.almanac');
        } catch (\Exception $e) {
            Log::error('TWWDA CEO Almanac decision error: ' . $e->getMessage());
            session()->flash('message', 'Failed to process almanac decision.');
            session()->flash('alert-type', 'error');
            return back();
        }
    }
}
