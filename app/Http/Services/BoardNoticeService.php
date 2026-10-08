<?php

namespace App\Http\Services;

use App\Models\BoardMeeting;
use Illuminate\Support\Facades\Auth;

class BoardNoticeService
{
    public static function fetchForLoggedInMember()
    {
        $userId = Auth::id();

        return BoardMeeting::whereHas('boardCommittee.boardMembers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with(['officialNotice10', 'committee','officialNotice'])
            ->where('status', 'Notice Sent')
            ->latest()
            ->get();

    }

    public static function fetchByIdForLoggedInMember($id)
    {
        $userId = Auth::id();

        return BoardMeeting::where('id', $id)
            ->whereHas('boardCommittee.boardMembers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
//            'notice.attachments',
            ->with([ 'committee'])
            ->firstOrFail();
    }
}
