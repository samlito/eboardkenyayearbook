<?php

namespace App\Http\Services;

use App\Models\BoardMeeting;
use App\Models\BoardMeetingNotice;
use Illuminate\Http\Request;

class ActualMeetingService
{
    public function getMeetingsWithNotices()
    {
        return BoardMeeting::whereNotNull('board_notified_at')
            ->with(['committee', 'almanacSchedule'])
            ->latest()
            ->get();
    }

    public function getMeetingWithPapers($id)
    {
        return BoardMeeting::with(['boardPapers', 'committee'])->findOrFail($id);
    }

    public function saveOfficialNoticeAndAgenda($id, $data, $request)
    {
        $meeting = BoardMeeting::findOrFail($id);

        // Save official notice (you may want to store this in a BoardNotice model)
        $notice = \App\Models\BoardNotice::updateOrCreate(
            ['meeting_id' => $id],
            ['content' => $data['official_notice'], 'signed_by' => 'CEO'] // Add signature field if needed
        );

        // Reorder agenda items
        foreach ($data['agenda_order'] as $index => $paperId) {
            \App\Models\BoardPaper::where('id', $paperId)->update(['presentation_order' => $index + 1]);
        }

        // Mark official notice timestamp
        $meeting->update(['official_notice_sent_at' => now()]);

        AuditLogService::log($request, 'UPDATE', 'board_meetings', $id, null, [
            'action' => 'Official meeting notice prepared and agenda reordered',
        ]);
    }
    public static function saveNotice(BoardMeeting $meeting, array $data, Request $request)
    {
        $notice = BoardMeetingNotice::updateOrCreate(
            ['meeting_id' => $meeting->id],
            $data
        );

        AuditLogService::log($request, 'INSERT', 'board_meeting_notices', $notice->id, null, $data);
    }
}
