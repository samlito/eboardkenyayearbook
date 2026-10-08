<?php

namespace App\Http\Services;
use App\Models\BoardMember;
use Illuminate\Http\Request;
use App\Http\Services\AuditLogService;
class BoardMemberService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request)
    {
        // Ensure attachments folder exists
        $attachmentsFolder = public_path("images/attachments");
        if (!file_exists($attachmentsFolder)) {
            mkdir($attachmentsFolder, 0777, true);
        }

        // Handle file uploads
        $uploadFile = function ($file, $folder, $prefix) {
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$prefix}_" . time() . ".{$extension}";
                $file->move($folder, $fileName);
                return "images/attachments/{$fileName}";
            }
            return null;
        };

        // Uploads
        $uploadId          = $uploadFile($request->file('upload_id'), $attachmentsFolder, 'ID');
        $uploadCv          = $uploadFile($request->file('upload_cv'), $attachmentsFolder, 'CV');
        $appointmentLetter = $uploadFile($request->file('appointment_letter'), $attachmentsFolder, 'Appointment_Letter');
        $gazetteeNotice    = $uploadFile($request->file('gazettee_notice'), $attachmentsFolder, 'Gazettee_Notice');
        $acceptanceLetter  = $uploadFile($request->file('acceptance_letter'), $attachmentsFolder, 'Acceptance_Letter');
        $photo             = $uploadFile($request->file('photo'), $attachmentsFolder, 'Photo');

        // Save to DB
        $boardMember = BoardMember::create([
            'salutation'         => $request->salutation,
            'names'              => $request->names,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'idno'               => $request->idno,
            'designation'        => $request->designation,
            'place_of_residence' => $request->place_of_residence,
            'appointment_date'   => $request->appointment_date,
            'upload_id'          => $uploadId,
            'upload_cv'          => $uploadCv,
            'appointment_letter' => $appointmentLetter,
            'gazettee_notice'    => $gazetteeNotice,
            'acceptance_letter'  => $acceptanceLetter,
            'photo'              => $photo,
            'status'             => 'Active',
            'addedby'            => auth()->user()->name ?? 'System',

            // Newly added fields
            'county_id'          => $request->county_id,
            'postal_address'     => $request->postal_address,
            'qualifications'     => $request->qualifications,
            'experience_summary' => $request->experience_summary,
            'date_of_birth'      => $request->date_of_birth,
            'end_date'           => $request->end_date,
            'director_type'      => $request->director_type,
            'role_type'          => $request->role_type,
        ]);

        // Log board member creation
        AuditLogService::log(
            $request,
            'INSERT',
            'board_members',
            $boardMember->id,
            null,
            $boardMember->toArray()
        );

        // Create associated user
        $userData = [
            'name'         => $request->names,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'staff_number' => $request->idno,
            'role'         => 'Board Member',
        ];

        $user = $this->userService->create($userData);
// ✅ Update board member with user_id
        $boardMember->update(['user_id' => $user->id]);
        // Log user creation
        AuditLogService::log(
            $request,
            'INSERT',
            'users',
            $user->id,
            null,
            $user->toArray()
        );

        return $boardMember;
    }
}
