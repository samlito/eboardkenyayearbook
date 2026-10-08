<?php

namespace App\Http\Controllers;

use App\Models\BoardResolution;
use App\Models\BoardMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardResolutionController extends Controller
{
    /**
     * Display all board resolutions
     */
    public function index()
    {
        $resolutions = BoardResolution::with(['boardMeeting', 'assignedOfficer', 'creator'])
            ->latest()
            ->get();

        return view('backend.resolutions.index', compact('resolutions'));
    }

    /**
     * Show form to create a new resolution
     */
    public function create()
    {
        $meetings = BoardMeeting::orderBy('id', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('backend.resolutions.create', compact('meetings', 'users'));
    }

    /**
     * Store a new resolution
     */
    public function store(Request $request)
    {
        $request->validate([
            'board_meeting_id' => 'required|exists:board_meetings,id',
            'resolution'       => 'required|string',
            'assigned_officer_id' => 'required|exists:users,id',
            'due_date'         => 'nullable|date',
            'remarks'          => 'nullable|string',
        ]);

        BoardResolution::create([
            'board_meeting_id'    => $request->board_meeting_id,
            'resolution'          => $request->resolution,
            'assigned_officer_id' => $request->assigned_officer_id,
            'due_date'            => $request->due_date,
            'remarks'             => $request->remarks,
            'status'              => 'pending',
            'created_by'          => Auth::id(),
        ]);

        $notification = [
            'message'    => 'Board Resolution recorded successfully!',
            'alert-type' => 'success',
        ];

        return redirect()->route('resolutions.index')->with($notification);
    }

    /**
     * Show a single resolution
     */
    public function show(BoardResolution $resolution)
    {
        $resolution->load(['boardMeeting', 'assignedOfficer', 'creator']);
        return view('backend.resolutions.show', compact('resolution'));
    }

    /**
     * Show form to edit a resolution
     */
    public function edit(BoardResolution $resolution)
    {
        $meetings = BoardMeeting::orderBy('id', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('backend.resolutions.edit', compact('resolution', 'meetings', 'users'));
    }

    /**
     * Update a resolution
     */
    public function update(Request $request, BoardResolution $resolution)
    {
        $request->validate([
            'board_meeting_id'    => 'required|exists:board_meetings,id',
            'resolution'          => 'required|string',
            'assigned_officer_id' => 'required|exists:users,id',
            'status'              => 'required|in:pending,in_progress,completed,overdue',
            'due_date'            => 'nullable|date',
            'remarks'             => 'nullable|string',
        ]);

        $resolution->update([
            'board_meeting_id'    => $request->board_meeting_id,
            'resolution'          => $request->resolution,
            'assigned_officer_id' => $request->assigned_officer_id,
            'status'              => $request->status,
            'due_date'            => $request->due_date,
            'remarks'             => $request->remarks,
        ]);

        $notification = [
            'message'    => 'Board Resolution updated successfully!',
            'alert-type' => 'success',
        ];

        return redirect()->route('resolutions.index')->with($notification);
    }

    /**
     * Update only the status of a resolution (for quick tracking)
     */
    public function updateStatus(Request $request, BoardResolution $resolution)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,overdue',
        ]);

        $resolution->update(['status' => $request->status]);

        $notification = [
            'message'    => 'Resolution status updated!',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * Delete a resolution
     */
    public function destroy(BoardResolution $resolution)
    {
        $resolution->delete();

        $notification = [
            'message'    => 'Board Resolution deleted!',
            'alert-type' => 'success',
        ];

        return redirect()->route('resolutions.index')->with($notification);
    }
}
