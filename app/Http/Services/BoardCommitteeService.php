<?php

namespace App\Http\Services;

use App\Models\BoardCommittee;
use Illuminate\Http\Request;

class BoardCommitteeService
{
    public function getAll()
    {
        return BoardCommittee::withCount('members')->with('members')->get();
    }

    public function find($id)
    {
        return BoardCommittee::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_audit_committee' => 'nullable|boolean',
        ]);
        $validated['is_audit_committee'] = $request->boolean('is_audit_committee');

        $committee = BoardCommittee::create($validated);

        AuditLogService::log($request, 'INSERT', 'board_committees', $committee->id, null, [
            'action' => 'Created new board committee'
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_audit_committee' => 'nullable|boolean',
        ]);
        $validated['is_audit_committee'] = $request->boolean('is_audit_committee');

        $committee = BoardCommittee::findOrFail($id);
        $old = $committee->toArray();

        $committee->update($validated);

        AuditLogService::log($request, 'UPDATE', 'board_committees', $committee->id, $old, [
            'new' => $committee->toArray(),
            'action' => 'Updated board committee'
        ]);
    }
}
