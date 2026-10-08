<?php

namespace App\Http\Services;

use App\Models\BoardMeeting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BoardMeetingService
{

    public static function upcomingMeetingsForLoggedInMember()
    {

        $userId = Auth::id();

        return BoardMeeting::whereHas('boardCommittee.boardMembers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
//            ->whereDate('date', '>=', now())
            ->whereNotNull('board_notified_at') // Only show meetings where board was notified
            ->with([
                'committee',
                'boardPapers.annexures', // ✅ eager load annexures
                'boardPapers.user'       // optional: also load the division head who submitted
            ])
            ->orderBy('date')
            ->get();


    }
    public static function meetingsForCorporationSecretary()
    {
        // Check once if the ordering column exists
        $hasAgendaPosition = Schema::hasColumn('board_papers', 'agenda_position');

        return BoardMeeting::whereNotNull('board_notified_at')
            ->with([
                'committee',
                'boardPapers' => function ($q) use ($hasAgendaPosition) {
                    $q->with(['annexures', 'user.division']);

                    // Order as presented if the column exists; otherwise fall back
                    if ($hasAgendaPosition) {
                        $q->orderBy('agenda_position');
                    } else {
                        $q->orderBy('id'); // or 'created_at'
                    }
                },
            ])
            ->orderBy('date')
            ->get();
    }
}

