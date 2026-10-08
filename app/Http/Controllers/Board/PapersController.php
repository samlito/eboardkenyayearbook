<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\BoardMeetingService;
use Illuminate\Http\Request;

class PapersController extends Controller
{
    //

    public function index()
    {
        $meetings = BoardMeetingService::upcomingMeetingsForLoggedInMember();

        // Flatten papers for convenience (you can paginate later)
        $papers = $meetings->flatMap->boardPapers;

        return view('board.papers.index', compact('papers', 'meetings'));
    }
}
