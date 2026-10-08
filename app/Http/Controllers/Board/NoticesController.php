<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\BoardNoticeService;
use Illuminate\Http\Request;

class NoticesController extends Controller
{
    //
    public function index()
    {
        $notices = BoardNoticeService::fetchForLoggedInMember();
        return view('board.notices.index', compact('notices'));
    }

    public function show($meetingId)
    {
        $meeting = BoardNoticeService::fetchByIdForLoggedInMember($meetingId);
        return view('board.notices.show', compact('meeting'));
    }
}
