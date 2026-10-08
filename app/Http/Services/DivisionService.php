<?php

namespace App\Http\Services;

use App\Models\Division;
use Illuminate\Http\Request;
class DivisionService
{
    public function getAll()
    {
        return Division::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $division = Division::create($request->only(['name', 'directorate_id', 'requires_ceo_approval']));

        AuditLogService::log(
            $request,
            'INSERT',
            'divisions',
            $division->id,
            null,
            $division->toArray()
        );

        return $division;
    }

    public function update(Request $request, Division $division)
    {
        $old = $division->toArray();
        $division->update($request->only(['name', 'directorate_id', 'requires_ceo_approval']));

        AuditLogService::log(
            $request,
            'UPDATE',
            'divisions',
            $division->id,
            $old,
            $division->toArray()
        );

        return $division;
    }

    public function delete(Request $request, Division $division)
    {
        $old = $division->toArray();
        $division->delete();

        AuditLogService::log(
            $request,
            'DELETE',
            'divisions',
            $division->id,
            $old,
            null
        );
    }
}
