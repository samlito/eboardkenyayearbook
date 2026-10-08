<?php

namespace App\Http\Controllers;

use App\Http\Services\BoardPaperService;
use App\Models\BoardPaper;
use Illuminate\Http\Request;

class BoardPaperController extends Controller
{
    //

    public function revisions()
    {
        $papers = BoardPaper::where('status', 'Needs Revision')
            ->where('user_id', auth()->id()) // or division head role user_id
            ->with('meeting')
            ->latest()
            ->get();

        return view('backend.revisions.index', compact('papers'));
    }

    public function resubmitRevision(Request $request, $id, BoardPaperService $boardPaperService)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:5120',
            'supporting_docs' => 'nullable|mimes:pdf,doc,docx,zip|max:5120',
        ]);

        $boardPaperService->handleRevisionResubmission($request, $id);

        return back()->with('success', 'Revised board paper submitted successfully.');
    }
}
