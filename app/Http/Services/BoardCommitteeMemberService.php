<?php

namespace App\Http\Services;
use App\Models\BoardMember;
use App\Models\BoardCommittee;

class BoardCommitteeMemberService
{
    public function getAssignableMembers()
    {
        return BoardMember::where('status', 'Active')->get();
    }

    public function assignMembers($committeeId, array $memberIds)
    {
        $committee = BoardCommittee::findOrFail($committeeId);
        $old = $committee->boardMembers()->pluck('board_member_id')->toArray();

        // Sync the relationship
        $committee->boardMembers()->sync($memberIds);

        // Audit
        AuditLogService::log(request(), 'UPDATE', 'board_committee_members', $committeeId, [
            'previous_members' => $old,
        ], [
            'new_members' => $memberIds,
            'action' => 'Updated committee members',
        ]);
    }
}
