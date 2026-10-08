<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\AuditLogService;
use App\Http\Services\BoardNoticeService;
use Illuminate\Http\Request;

class BoardNoticeController extends Controller
{
    //

    public function index(Request $request)
    {
        $notices = BoardNoticeService::fetchForLoggedInMember();
        AuditLogService::log($request, 'VIEW', 'board_notices', null, null, [
            'action' => 'Viewed list of board meeting notices'
        ]);

        return view('board.notices.index', compact('notices'));
    }

    public function show($id, Request $request)
    {
        $notice = BoardNoticeService::fetchByIdForLoggedInMember($id);
        AuditLogService::log($request, 'VIEW', 'board_notices', $id, null, [
            'action' => 'Viewed details of board meeting notice'
        ]);

        return view('board.notices.show', compact('notice'));
    }
}
