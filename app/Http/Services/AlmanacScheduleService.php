<?php

namespace App\Http\Services;

use App\Models\AlmanacSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AlmanacScheduleService
{
    public function store(Request $request, $almanacId): AlmanacSchedule
    {
        $request->validate([
//            'meeting_id' => 'required|exists:meetings,id',
            'board_committee_id' => 'required|exists:board_committees,id',
            'meeting_date' => 'required|date',
            'purpose' => 'required|string',
            'budget' => 'required|numeric|min:0',
        ]);

        $schedule = new AlmanacSchedule();
        $schedule->almanac_id = $almanacId;
        $schedule->meeting_id =1;
        $schedule->board_committee_id = $request->board_committee_id;
        $schedule->meeting_date = $request->meeting_date;
        $schedule->purpose = $request->purpose;
        $schedule->budget = $request->budget;
        $schedule->status = 'Pending';
        $schedule->created_by = Auth::user()->name;
        $schedule->save();

        AuditLogService::log($request, 'INSERT', 'almanac_schedules', $schedule->id, null, $schedule->toArray());

        return $schedule;
    }

    public function storeAp(Request $request, $almanacId): AlmanacSchedule
    {
        $validated = $request->validate([
            // 'meeting_id' => 'required|exists:meetings,id',
            'board_committee_id' => 'required|exists:board_committees,id',
            'meeting_date' => 'required|date',
            'purpose' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'supporting_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $schedule = new AlmanacSchedule();
        $schedule->almanac_id = $almanacId;
        $schedule->meeting_id = 1; // or $request->meeting_id if you enable it
        $schedule->board_committee_id = $validated['board_committee_id'];
        $schedule->meeting_date = $validated['meeting_date'];
        $schedule->purpose = $validated['purpose'];
        $schedule->budget = $validated['budget'];
        $schedule->status = 'Pending';
        $schedule->created_by = Auth::user()->name;
        $schedule->addition_to_schedule=1;

        // Handle file upload (optional field)
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            // Build a unique, traceable filename
            $filename = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store to the "public" disk so it can be served via /storage symlink
            $path = $file->storeAs('almanac/supporting_documents', $filename, 'public');

            // Save path (and optional metadata) on the model
            // Make sure these columns exist in your almanac_schedules table.
            $schedule->supporting_document = $path; // e.g. "almanac/supporting_documents/20250829_...uuid....pdf"

        }

        $schedule->save();

        AuditLogService::log(
            $request,
            'INSERT',
            'almanac_schedules',
            $schedule->id,
            null,
            $schedule->toArray()
        );

        return $schedule;
    }


}
