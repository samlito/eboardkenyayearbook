<?php

namespace App\Http\Services;

use App\Models\Directorate;
use Illuminate\Http\Request;

class DirectorateService
{
    public function getAll()
    {
        return Directorate::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $directorate = Directorate::create($request->only(['name']));

        AuditLogService::log(
            $request,
            'INSERT',
            'directorates',
            $directorate->id,
            null,
            $directorate->toArray()
        );

        return $directorate;
    }

    public function update(Request $request, Directorate $directorate)
    {
        $old = $directorate->toArray();
        $directorate->update($request->only(['name']));

        AuditLogService::log(
            $request,
            'UPDATE',
            'directorates',
            $directorate->id,
            $old,
            $directorate->toArray()
        );

        return $directorate;
    }

    public function delete(Request $request, Directorate $directorate)
    {
        $old = $directorate->toArray();
        $directorate->delete();

        AuditLogService::log(
            $request,
            'DELETE',
            'directorates',
            $directorate->id,
            $old,
            null
        );
    }
}
