<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use App\Mail\ConflictOfInterestOtpMail;
use App\Models\BoardMeeting;
use App\Models\ConflictOfInterestDeclaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ConflictOfInterestController extends Controller
{
    //
    public function store(Request $request, BoardMeeting $meeting)
    {
        $request->validate([
            'conflict_status'    => 'required|in:Yes,No',
//            'person_name'        => 'required_if:conflict_status,Yes|string|max:255',
//            'person_address'     => 'required_if:conflict_status,Yes|string|max:255',
//            'interest_nature'    => 'required_if:conflict_status,Yes|string|max:255',
//            'interest_value'     => 'required_if:conflict_status,Yes|numeric',
//            'interest_ceased_on' => 'required_if:conflict_status,Yes|date',
//            'remarks'            => 'required_if:conflict_status,Yes|string',
        ]);

        $otp = rand(100000, 999999); // numeric OTP

        // Fetch existing declaration if any (for audit old values)
        $existing = ConflictOfInterestDeclaration::where('meeting_id', $meeting->id)
            ->where('board_member_id', Auth::user()->boardMember->id)
            ->first();
        $oldValues = $existing ? $existing->toArray() : null;
        $declaration = ConflictOfInterestDeclaration::updateOrCreate(
            [
                'meeting_id'      => $meeting->id,
                'board_member_id' => Auth::user()->boardMember->id,
            ],
            array_merge($request->only([
                'conflict_status',
                'person_name',
                'person_address',
                'interest_nature',
                'interest_value',
                'interest_ceased_on',
                'remarks',
            ]), [
                'otp_code'       => $otp,
                'otp_verified_at'=> null,
            ])
        );
        // Remove sensitive data from audit log (like OTP)
        $newValues = $declaration->toArray();
        unset($newValues['otp_code']);

        // Log audit
        AuditLogService::log(
            $request,
            $existing ? 'DECLARATION_UPDATED' : 'DECLARATION_SUBMITTED',
            'conflict_of_interest_declarations',
            $declaration->id,
            $oldValues,
            $newValues
        );

        // Send OTP Mail
        Mail::to(Auth::user()->email)->queue(new ConflictOfInterestOtpMail($declaration, $meeting, $otp));

        // Redirect to OTP instruction page
        return redirect()->route('conflict.pending', $declaration->id)
            ->with('info', 'Your declaration has been submitted. Please confirm using the OTP sent to your email.');
    }






    public function pending(ConflictOfInterestDeclaration $declaration)
    {
        return view('board.conflict.pending', compact('declaration'));
    }

    public function verifyForm(ConflictOfInterestDeclaration $declaration)
    {
        return view('board.conflict.verify', compact('declaration'));
    }

    public function verify(Request $request, ConflictOfInterestDeclaration $declaration)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if ($declaration->otp_code === $request->otp) {
            $declaration->update([
                'otp_verified_at' => now(),
                'otp_code' => null, // clear it
            ]);
            AuditLogService::log(
                $request,
                'DECLARATION_CONFIRMED',
                'conflict_of_interest_declarations',
                $declaration->id,
                null,
                ['otp_verified_at' => now()]
            );
            return redirect()->route('meetings.active')->with('success', 'Declaration confirmed successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
    }
    public function resendOtp(Request $request, ConflictOfInterestDeclaration $declaration)
    {
        $otp = rand(100000, 999999);

        // Keep old values for audit (without OTP)
        $oldValues = $declaration->toArray();
        unset($oldValues['otp_code']);

        $declaration->update([
            'otp_code'       => $otp,
            'otp_verified_at'=> null,
        ]);

        // Remove OTP before logging
        $newValues = $declaration->toArray();
        unset($newValues['otp_code']);

        // Audit log
        AuditLogService::log(
            $request,
            'OTP_RESENT',
            'conflict_of_interest_declarations',
            $declaration->id,
            $oldValues,
            $newValues
        );

        // Send OTP Mail
        Mail::to(Auth::user()->email)->queue(
            new ConflictOfInterestOtpMail($declaration, $declaration->meeting, $otp)
        );

        return back()->with('info', 'A new OTP has been sent to your email.');
    }


}
