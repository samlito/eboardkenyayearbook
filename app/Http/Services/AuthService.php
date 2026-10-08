<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthService
{
    public function attemptLogin(Request $request): ?User
    {
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }

    public function generateOtp(User $user): UserOtp
    {
        UserOtp::where('user_id', $user->id)->delete();

        $otp = rand(100000, 999999);

        return UserOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    public function sendOtp(User $user, UserOtp $otp)
    {
        $institutionName = setting('institution_name', 'eBoard System');
        $prefix = setting('email_subject_prefix', setting('institution_acronym', 'ORG'));
        $subject = "{$prefix} — Your Login OTP Code";

        $body = view('emails.otp', [
            'name' => $user->name,
            'otp'  => $otp->otp,
        ])->render();

        try {
            EmailService::sendEmail($user->email, $subject, $body);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("OTP email failed for {$user->email}: {$e->getMessage()}");
            session()->flash('dev_otp', $otp->otp);
        }
    }

    public function validateOtp(User $user, string $code): bool
    {
        $record = UserOtp::where('user_id', $user->id)
            ->where('otp', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($record) {
            $record->delete();

            UserOtp::where('user_id', $user->id)
                ->where(function ($q) {
                    $q->where('used', true)
                      ->orWhere('expires_at', '<=', now());
                })
                ->delete();

            return true;
        }

        return false;
    }
}
