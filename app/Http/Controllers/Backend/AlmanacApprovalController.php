<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Almanac;
use App\Models\AlmanacSchedule;
use App\Models\ReviewComment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlmanacApprovalController extends Controller
{
  public function pendingceoapproval(){

      $almanacs=Almanac::where('status','Awaiting Approval')->latest()->get();
      return view('backend.approvals.pending',compact('almanacs'));
  }

    public function ceoapproved(){

        $almanacs=Almanac::where('status','approved')->latest()->get();
        return view('backend.approvals.approved',compact('almanacs'));
    }




  public function showpending($id){
      $allshcedules=AlmanacSchedule::where('almanac_id',$id)->get();
      $almanac = Almanac::findOrFail($id);
      $initialBudget = $almanac->budget;
      $totalScheduleBudget = AlmanacSchedule::where('almanac_id', $id)->sum('budget');

      $remainingBudget = $initialBudget - $totalScheduleBudget;

      $rejectedcomment = ReviewComment::where('almanac_id', $id)
          ->orderBy('id', 'desc')
          ->pluck('comment');

      return view('backend.approvals.pendingshow',compact('allshcedules', 'almanac', 'remainingBudget', 'totalScheduleBudget','rejectedcomment'));

  }

    public function approve(Request $request, $id)
    {
        $almanac = Almanac::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|string|max:500',
        ]);


        $almanac->status = $request->status;
        $almanac->ceo_approval_date = Carbon::now();
        $almanac->save();

        // If rejected, store the comment in reviewcomments table
        if ($request->status === 'Rejected' && $request->comment) {
            ReviewComment::create([
                'almanac_id' => $almanac->id,
                'comment' => $request->comment,
                'comment_date' => now(),
                'status' => 'Rejected',
            ]);
        }

        return redirect()->back()->with('success', 'Almanac status updated successfully.');
    }
}
