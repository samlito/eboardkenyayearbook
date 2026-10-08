<?php

namespace App\Http\Services;
use App\Models\BoardPaper;
use App\Models\BoardPaperVersion;
use App\Mail\BoardPaperStatusUpdatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BoardPaperService
{
    public function handleCeoDecision($paperId, $status, $ceo_comments, $request)
    {
        $paper = BoardPaper::with('user', 'meeting')->findOrFail($paperId);
        $oldValues = $paper->only(['status', 'ceo_comments']);

        // Update main paper
        $paper->status = $status;
        $paper->ceo_comments = $ceo_comments;
        $paper->save();

        // Save version record
        BoardPaperVersion::create([
            'board_paper_id' => $paper->id,
            'status' => $status,
            'ceo_comments' => $ceo_comments,
            'file_path' => $paper->file, // assuming file column is named 'file'
            'supporting_docs_path' => $paper->supporting_docs,
            'acted_by' => auth()->id(),
        ]);

        // Audit log
        AuditLogService::log($request, 'UPDATE', 'board_papers', $paper->id, $oldValues, [
            'new' => $paper->only(['status', 'ceo_comments']),
            'action' => 'CEO updated board paper status',
        ]);

        // Notify owner
        Mail::to($paper->user->email)->queue(new BoardPaperStatusUpdatedMail($paper));
    }



    public function handleRevisionResubmission(Request $request, $id)
    {
        $paper = BoardPaper::findOrFail($id);

        $oldValues = $paper->only(['file', 'supporting_docs', 'status']);

        // Upload new files
//        $filePath = $request->file('file')->store('board_papers');
//        $supportingPath = $request->hasFile('supporting_docs')
//            ? $request->file('supporting_docs')->store('board_support_docs')
//            : null;
        $filePath = $request->file('file')->store('board_papers', 'public');
        $supportingPath = $request->hasFile('supporting_docs')
            ? $request->file('supporting_docs')->store('board_support_docs', 'public')
            : null;


        // Save version before overwriting
        BoardPaperVersion::create([
            'board_paper_id' => $paper->id,
            'status' => 'Pending',
            'file_path' => $filePath,
            'supporting_docs_path' => $supportingPath,
            'acted_by' => auth()->id(),
        ]);

        // Update current paper
        $paper->file = $filePath;
        $paper->supporting_docs = $supportingPath;
        $paper->status = 'Pending';
        $paper->ceo_comments = null;
        $paper->save();

        // Audit log
        AuditLogService::log($request, 'UPDATE', 'board_papers', $paper->id, $oldValues, [
            'new' => $paper->only(['file', 'supporting_docs', 'status']),
            'action' => 'Division head resubmitted revised paper',
        ]);
    }


}
