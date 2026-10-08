<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\BoardCommitteeMemberService;
use App\Models\BoardCommittee;
use Illuminate\Http\Request;

class BoardCommitteeMemberController extends Controller
{
    //

    protected $service;

    public function __construct(BoardCommitteeMemberService $service)
    {
        $this->service = $service;
    }

    public function index($committeeId, Request $request)
    {
        $committee = BoardCommittee::withCount('members')->findOrFail($committeeId);
        $members = $this->service->getAssignableMembers();
        $assigned = $committee->boardMembers()->pluck('board_member_id')->toArray();
        $assignedMembers = $committee->boardMembers()->orderBy('names')->get();
        $totalBoardMembers = \App\Models\BoardMember::where('status', 'Active')->count();

        return view('backend.board_committees.assign_members', compact(
            'committee', 'members', 'assigned', 'assignedMembers', 'totalBoardMembers'
        ));
    }

    public function store($committeeId, Request $request)
    {
        $request->validate([
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:board_members,id',
        ]);

        $this->service->assignMembers($committeeId, $request->member_ids ?? []);

        return back()->with('success', 'Committee members updated successfully.');
    }
}
