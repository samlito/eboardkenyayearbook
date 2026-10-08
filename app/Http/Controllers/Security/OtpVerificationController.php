<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Services\AuditLogService;
use App\Http\Services\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function showVerifyForm()
    {
        if (!session('otp_pending_user')) {
            return redirect()->route('login');
        }

        return view('auth.verify_otp');
    }

    public function verify(Request $request, AuthService $authService)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = session('otp_pending_user');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget('otp_pending_user');
            return redirect()->route('login')->withErrors(['otp' => 'User not found.']);
        }

        if ($authService->validateOtp($user, $request->otp)) {
            session()->forget('otp_pending_user');

            Auth::login($user);
            $request->session()->regenerate();

            AuditLogService::log($request, 'LOGIN_SUCCESS', 'users', $user->id);

            return redirect()->route('dashboard')->with('message', 'Login successful.');
        }

        AuditLogService::log($request, 'OTP_FAILED', 'users', $user->id, null, ['otp' => $request->otp]);

        return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again or resend.']);
    }

    public function resend(Request $request, AuthService $authService)
    {
        $userId = session('otp_pending_user');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget('otp_pending_user');
            return redirect()->route('login');
        }

        $otp = $authService->generateOtp($user);
        $authService->sendOtp($user, $otp);

        AuditLogService::log($request, 'OTP_RESENT', 'users', $user->id);

        $redirect = redirect()->route('otp.verify.form')->with('message', 'A new OTP has been sent to your email.');

        if (env('SHOW_OTP_ON_SCREEN', false)) {
            $redirect->with('dev_otp', $otp->otp);
        }

        return $redirect;
    }
}
