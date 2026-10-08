<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Services\AuditLogService;
use App\Http\Services\BoardMemberService;
use App\Http\Services\UserService;
use App\Models\Salutation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BoardMember;
use App\Models\County;

class BoardMemberController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService, BoardMemberService $boardMemberService)
    {
        $this->userService = $userService;
        $this->boardMemberService = $boardMemberService;
    }
    public function index(){

        $boardmembers = BoardMember::with(['assignsalutation', 'county', 'user'])->latest()->get();

        return view('backend.boardmembers.index',compact('boardmembers'));
    }

    public function create()
    {
        $salutations = Salutation::all();
        $counties=County::all();
        return view('backend.boardmembers.create',compact('salutations','counties'));
    }

    public function store2(Request $request)
    {
        // Validate input fields
        $request->validate([
            'salutation' => 'required|integer',
            'names' => 'required|string|max:255',
            'email' => 'required|email|unique:board_members,email',
            'phone' => 'required|string|max:15',
            'idno' => 'required|string|min:5|max:15',
            'designation' => 'required|string',
            'place_of_residence' => 'required|string',
            'appointment_date' => 'required|date',

            'county_id' => 'required|integer',
            'postal_address' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'experience_summary' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'director_type' => 'nullable|in:Executive,Non-Executive,Chairperson,Member',
            'role_type' => 'nullable|in:independent,representative',

            // Files
            'upload_id' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'upload_cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'appointment_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'gazettee_notice' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'acceptance_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Define the base attachments folder
        $attachmentsFolder = public_path("images/attachments");

        if (!file_exists($attachmentsFolder)) {
            mkdir($attachmentsFolder, 0777, true);
        }

        // Handle file uploads
        function uploadFile($file, $folder, $prefix)
        {
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$prefix}_" . time() . ".{$extension}";
                $file->move($folder, $fileName);
                return "images/attachments/{$fileName}";
            }
            return null;
        }

        // Upload files
        $uploadId = uploadFile($request->file('upload_id'), $attachmentsFolder, 'ID');
        $uploadCv = uploadFile($request->file('upload_cv'), $attachmentsFolder, 'CV');
        $appointmentLetter = uploadFile($request->file('appointment_letter'), $attachmentsFolder, 'Appointment_Letter');
        $gazetteeNotice = uploadFile($request->file('gazettee_notice'), $attachmentsFolder, 'Gazettee_Notice');
        $acceptanceLetter = uploadFile($request->file('acceptance_letter'), $attachmentsFolder, 'Acceptance_Letter');
        $photo = uploadFile($request->file('photo'), $attachmentsFolder, 'Photo');

        // Save to DB
        BoardMember::create([
            'salutation' => $request->salutation,
            'names' => $request->names,
            'email' => $request->email,
            'phone' => $request->phone,
            'idno' => $request->idno,
            'designation' => $request->designation,
            'place_of_residence' => $request->place_of_residence,
            'appointment_date' => $request->appointment_date,
            'upload_id' => $uploadId,
            'upload_cv' => $uploadCv,
            'appointment_letter' => $appointmentLetter,
            'gazettee_notice' => $gazetteeNotice,
            'acceptance_letter' => $acceptanceLetter,
            'photo' => $photo,
            'status' => 'Active',
            'addedby' => auth()->user()->name ?? 'System',

            // Newly added fields
            'county_id' => $request->county_id,
            'postal_address' => $request->postal_address,
            'qualifications' => $request->qualifications,
            'experience_summary' => $request->experience_summary,
            'date_of_birth' => $request->date_of_birth,
            'director_type' => $request->director_type,
            'role_type' => $request->role_type,
        ]);

        // Create associated user
        $userData = [
            'name' => $request->names,
            'email' => $request->email,
            'phone' => $request->phone,
            'staff_number' => $request->idno,
            'role' => 'Board Member',
        ];

        $user = $this->userService->create($userData);

        return redirect()->route('all.boardmembers')->with('success', 'Board Member added successfully!');
    }
    // Show a single board member's details
    public function store(Request $request)
    {
        $request->validate([
            'salutation' => 'required|integer',
            'names' => 'required|string|max:255',
            'email' => 'required|email|unique:board_members,email',
            'phone' => 'required|string|max:15',
            'idno' => 'required|string|min:5|max:15',
            'designation' => 'required|string',
            'place_of_residence' => 'required|string',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'county_id' => 'required|integer',
            'postal_address' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'experience_summary' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'director_type' => 'nullable|in:Executive,Non-Executive,Chairperson,Member',
            'role_type' => 'nullable|in:independent,representative',
            'upload_id' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'upload_cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'appointment_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'gazettee_notice' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'acceptance_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);

        $this->boardMemberService->store($request);

        return redirect()->route('all.boardmembers')
            ->with('success', 'Board Member added successfully!');
    }

    public function show($id)
    {
        $boardMember = BoardMember::with(['assignsalutation', 'county'])->findOrFail($id);
        return view('backend.boardmembers.show', compact('boardMember'));
    }

    // Show edit form for a board member
    public function edit($id)
    {
        $boardMember = BoardMember::findOrFail($id);
        $salutations = Salutation::all();
        $counties=County::all();
        return view('backend.boardmembers.edit', compact('boardMember', 'salutations','counties'));
    }

// Update a board member
    public function update(Request $request, $id)
    {
        $boardMember = BoardMember::findOrFail($id);
        // ✅ Capture old values before update
        $oldValues = $boardMember->toArray();
        $request->validate([
            'salutation' => 'required',
            'names' => 'required|string|max:255',
            'email' => 'required|email|unique:board_members,email,' . $id,
            'phone' => 'required|string|max:15',
            'idno' => 'required|string|min:5|max:15',
            'designation' => 'required|string',
            'place_of_residence' => 'required|string',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'exit_date' => 'nullable|date',
            'county_id' => 'required|integer',
            'postal_address' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'qualifications' => 'nullable|string',
            'experience_summary' => 'nullable|string',
            'director_type' => 'nullable|in:Executive,Non-Executive,Chairperson,Member',
            'role_type' => 'nullable|in:independent,representative',

            // Files
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'upload_id' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'upload_cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'appointment_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'gazettee_notice' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'acceptance_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file uploads helper
        $attachmentsFolder = public_path("images/attachments");
        if (!file_exists($attachmentsFolder)) {
            mkdir($attachmentsFolder, 0777, true);
        }

        $uploadFile = function ($file, $folder, $prefix, $oldFile = null) {
            if ($file) {
                // delete old file if exists
                if ($oldFile && file_exists(public_path($oldFile))) {
                    unlink(public_path($oldFile));
                }
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$prefix}_" . time() . ".{$extension}";
                $file->move($folder, $fileName);
                return "images/attachments/{$fileName}";
            }
            return $oldFile; // keep old file if no new one uploaded
        };

        // Update file fields
        $boardMember->photo = $uploadFile($request->file('photo'), $attachmentsFolder, 'Photo', $boardMember->photo);
        $boardMember->upload_id = $uploadFile($request->file('upload_id'), $attachmentsFolder, 'ID', $boardMember->upload_id);
        $boardMember->upload_cv = $uploadFile($request->file('upload_cv'), $attachmentsFolder, 'CV', $boardMember->upload_cv);
        $boardMember->appointment_letter = $uploadFile($request->file('appointment_letter'), $attachmentsFolder, 'Appointment_Letter', $boardMember->appointment_letter);
        $boardMember->gazettee_notice = $uploadFile($request->file('gazettee_notice'), $attachmentsFolder, 'Gazettee_Notice', $boardMember->gazettee_notice);
        $boardMember->acceptance_letter = $uploadFile($request->file('acceptance_letter'), $attachmentsFolder, 'Acceptance_Letter', $boardMember->acceptance_letter);

        // Update all fields
        $boardMember->salutation = $request->salutation;
        $boardMember->names = $request->names;
        $boardMember->email = $request->email;
        $boardMember->phone = $request->phone;
        $boardMember->idno = $request->idno;
        $boardMember->designation = $request->designation;
        $boardMember->place_of_residence = $request->place_of_residence;
        $boardMember->appointment_date = $request->appointment_date;

        $boardMember->county_id = $request->county_id;
        $boardMember->postal_address = $request->postal_address;
        $boardMember->date_of_birth = $request->date_of_birth;
        $boardMember->end_date = $request->end_date;
        $boardMember->exit_date = $request->exit_date;
        $boardMember->qualifications = $request->qualifications;
        $boardMember->experience_summary = $request->experience_summary;
        $boardMember->director_type = $request->director_type;
        $boardMember->role_type = $request->role_type;

        $boardMember->save();
        // ✅ Log update activity
       AuditLogService::log(
            $request,
            'UPDATE',
            'board_members',
            $boardMember->id,
            $oldValues,
            $boardMember->toArray()
        );
        return redirect()->route('all.boardmembers')->with('success', 'Board Member updated successfully!');
    }

    // Delete a board member
    public function destroy($id)
    {
        $boardmember = BoardMember::findOrFail($id);
        $boardmember->delete();

        $notification = array(
            'message' => 'Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.boardmembers')->with($notification);
    }


    public function toggleStatus(Request $request, $id)
    {
        $boardmember = BoardMember::findOrFail($id);
        $wasActive = $boardmember->status === 'Active';
        $boardmember->status = $wasActive ? 'Inactive' : 'Active';
        $boardmember->save();

        // Sync linked user account status
        $user = $boardmember->user_id ? User::find($boardmember->user_id) : User::where('email', $boardmember->email)->first();

        if ($user) {
            if ($wasActive) {
                $user->update(['status' => 'inactive']);
                AuditLogService::log($request, 'USER_DEACTIVATED', 'users', $user->id, ['status' => 'active'], ['status' => 'inactive']);
            } else {
                $user->update(['status' => 'active']);
                AuditLogService::log($request, 'USER_ACTIVATED', 'users', $user->id, ['status' => 'inactive'], ['status' => 'active']);
            }
        }

        $msg = $wasActive
            ? 'Board member deactivated. User account has been disabled.'
            : 'Board member activated. User account has been enabled.';

        return redirect()->route('all.boardmembers')->with('success', $msg);
    }

    public function createUser(Request $request, $id)
    {
        $boardmember = BoardMember::findOrFail($id);

        // Check if user already exists
        $existingUser = $boardmember->user_id ? User::find($boardmember->user_id) : User::where('email', $boardmember->email)->first();

        if ($existingUser) {
            // Link if not already linked
            if (!$boardmember->user_id) {
                $boardmember->update(['user_id' => $existingUser->id]);
            }
            return redirect()->back()->with('info', 'A user account already exists for this email address.');
        }

        // Create user account
        $userData = [
            'name'         => $boardmember->names,
            'email'        => $boardmember->email,
            'phone'        => $boardmember->phone,
            'staff_number' => $boardmember->idno,
            'role'         => 'Board Member',
        ];

        $user = $this->userService->create($userData);

        // Link user to board member
        $boardmember->update(['user_id' => $user->id]);

        AuditLogService::log($request, 'BOARD_MEMBER_USER_CREATED', 'users', $user->id, null, $user->toArray());

        return redirect()->back()->with('success', 'User account created and welcome email sent to ' . $boardmember->email);
    }
}
