<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommitteesController extends Controller
{
    //
    public function index()
    {
        $member = BoardMember::with('committees')
            ->where('user_id', Auth::id())
            ->orWhere('email', optional(Auth::user())->email)
            ->firstOrFail();

        $committees = $member->committees;

        return view('board.committees.index', compact('member', 'committees'));
    }
}
