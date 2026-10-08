<?php

namespace App\Observers;

use App\Models\BoardPaper;

use App\Models\BoardMeeting;
use App\Models\User;
use App\Notifications\ReviewBoardPapersNotification;
class BoardPaperObserver
{
    /**
     * Handle the BoardPaper "created" event.
     *
     * @param  \App\Models\BoardPaper  $boardPaper
     * @return void
     */
    public function created(BoardPaper $paper)
    {
        $meeting = $paper->meeting;

        // Only notify if not already notified for this specific paper
        if (is_null($paper->ceo_notified_at)) {
            $ceo = User::role('CEO')->first();



            if ($ceo) {
                $ceo->notify(new ReviewBoardPapersNotification($meeting, auth()->user()));
                $paper->update(['ceo_notified_at' => now()]);
            }
        }
    }

    /**
     * Handle the BoardPaper "updated" event.
     *
     * @param  \App\Models\BoardPaper  $boardPaper
     * @return void
     */
    public function updated(BoardPaper $boardPaper)
    {
        //
    }

    /**
     * Handle the BoardPaper "deleted" event.
     *
     * @param  \App\Models\BoardPaper  $boardPaper
     * @return void
     */
    public function deleted(BoardPaper $boardPaper)
    {
        //
    }

    /**
     * Handle the BoardPaper "restored" event.
     *
     * @param  \App\Models\BoardPaper  $boardPaper
     * @return void
     */
    public function restored(BoardPaper $boardPaper)
    {
        //
    }

    /**
     * Handle the BoardPaper "force deleted" event.
     *
     * @param  \App\Models\BoardPaper  $boardPaper
     * @return void
     */
    public function forceDeleted(BoardPaper $boardPaper)
    {
        //
    }
}
