<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use App\Models\County;
use App\Models\Salutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class BoardMemberProfileController extends Controller
{
    //

    public function show($memberId)
    {
        $member = BoardMember::with(['committees', 'assignsalutation', 'county'])->findOrFail($memberId);

        // Optional: prevent peeking into someone else's profile
        if ($member->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('board.members.profile.show', compact('member'));
    }

    public function edit($memberId)
    {
        $member = BoardMember::with(['assignsalutation','county'])->findOrFail($memberId);

        if ($member->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $salutations = Salutation::orderBy('name')->get(['id','name']);
        $counties    = County::orderBy('county_name')->get(['id','county_name']);

        return view('board/members/profile/edit', compact('member','salutations','counties'));
    }

    public function update(Request $request, $memberId)
    {
        $member = BoardMember::findOrFail($memberId);

        if ($member->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            // Core
            'salutation'         => ['nullable','integer','exists:salutations,id'],
            'names'              => ['required','string','max:255'],
            'designation'        => ['nullable','string','max:255'],
            'director_type'      => ['nullable', Rule::in(['Executive','Non-Executive','Chairperson','Member'])],
            'role_type'          => ['nullable', Rule::in(['independent','representative'])],
            'date_of_birth'      => ['nullable','date','before:today'],
            'place_of_residence' => ['nullable','string','max:255'],
            'postal_address'     => ['nullable','string','max:255'],
            'county_id'          => ['nullable','integer','exists:county,id'],
            'idno'               => ['nullable','string','max:255'],
            'signature_image'     => ['nullable','image','mimes:png,jpg,jpeg','max:4096'],


            // Contact (email is unique; keep as display-only if you prefer)
            'email'              => ['required','email','max:255', Rule::unique('board_members','email')->ignore($member->id)],
            'phone'              => ['required','string','max:255'],

            // Appointment
            'appointment_date'   => ['required','date'],
            'end_date'           => ['nullable','date','after_or_equal:appointment_date'],
            'exit_date'          => ['nullable','date','after_or_equal:appointment_date'],
            'status'             => ['nullable','string','max:255'],

            // Long text
            'qualifications'     => ['nullable','string'],
            'experience_summary' => ['nullable','string'],

            // Files
            'photo'              => ['nullable','image','mimes:jpg,jpeg,png','max:5120'],
            'upload_id'          => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:8192'],
            'upload_cv'          => ['nullable','file','mimes:pdf,doc,docx','max:8192'],
            'appointment_letter' => ['nullable','file','mimes:pdf','max:8192'],
            'gazettee_notice'    => ['nullable','file','mimes:pdf','max:8192'],
            'acceptance_letter'  => ['nullable','file','mimes:pdf','max:8192'],
        ]);

        // Assign scalars
        $member->fill($validated);

        // Handle uploads (stored under public/board_members/{id}/)
        $dir = "board_members/{$member->id}";

        foreach ([
                     'photo',
                     'upload_id',
                     'upload_cv',
                     'appointment_letter',
                     'gazettee_notice',
                     'acceptance_letter',
                     'signature_image',
                 ] as $field) {

            if ($request->hasFile($field)) {
                // Delete old if present
                if (!empty($member->{$field})) {
                    $this->safeDelete($member->{$field});
                }

                $file     = $request->file($field);
                $ext      = $file->getClientOriginalExtension();
                $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $basename = $basename ?: $field;
                $path     = $file->storeAs($dir, $basename.'-'.Str::random(6).'.'.$ext, 'public');

                $member->{$field} = $path; // store relative path under public disk
            }
        }

        $member->save();

        return redirect()
            ->route('board.profile.show', $member->id)
            ->with('success', 'Profile updated successfully.');
    }

    private function safeDelete(?string $path): void
    {
        if (!$path) return;

        // If it's a public-disk relative path or "storage/..." URL
        if (Str::startsWith($path, ['http://','https://'])) {
            // external file – skip delete
            return;
        }

        // Normalize "storage/..." -> "public/..."
        $relative = Str::startsWith($path, 'storage/')
            ? Str::replaceFirst('storage/', '', $path)
            : $path;

        if (Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
