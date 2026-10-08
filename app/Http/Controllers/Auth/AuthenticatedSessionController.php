<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Services\AuditLogService;
use App\Http\Services\AuthService;
use App\Http\Services\EmailService;
use App\Models\VerificationCode;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(LoginRequest $request, AuthService $authService)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->locked) {
            return back()->withErrors(['email' => 'Your account is locked. Contact admin.']);
        }

        $authenticatedUser = $authService->attemptLogin($request);

        if (!$authenticatedUser) {
            if ($user) {
                $user->increment('failed_logins');

                if ($user->failed_logins >= 5) {
                    $user->update(['locked' => true]);
                    AuditLogService::log($request, 'ACCOUNT_LOCKED', 'users', $user->id);
                    return back()->withErrors(['email' => 'Your account has been locked after 5 failed attempts. Contact admin.']);
                }

                $remaining = 5 - $user->failed_logins;
                $warning = $remaining <= 2
                    ? "Invalid credentials. Warning: Your account will be locked after {$remaining} more attempt(s)."
                    : "Invalid credentials.";

                AuditLogService::log($request, 'LOGIN_FAILED', 'users', $user->id, null, [
                    'email' => $request->email,
                    'attempts' => $user->failed_logins
                ]);

                return back()->withErrors(['email' => $warning]);
            }

            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if (in_array($authenticatedUser->status, ['inactive', 'suspended', 'disabled'])) {
            return redirect()->route('account.restricted');
        }

        $authenticatedUser->update(['failed_logins' => 0]);

        $otp = $authService->generateOtp($authenticatedUser);
        $authService->sendOtp($authenticatedUser, $otp);

        AuditLogService::log($request, 'LOGIN_ATTEMPT', 'users', $authenticatedUser->id, null, [
            'otp_sent' => true
        ]);

        session(['otp_pending_user' => $authenticatedUser->id]);

        $redirect = redirect()->route('otp.verify.form')
            ->with('info', 'An OTP has been sent to your email.');

        if (env('SHOW_OTP_ON_SCREEN', false)) {
            $redirect->with('dev_otp', $otp->otp);
        }

        return $redirect;
    }

    public function store3(LoginRequest $request, AuthService $authService)
    {
        $user = $authService->attemptLogin($request);

        if (!$user) {
            AuditLogService::log($request, 'LOGIN_FAILED', 'users', null, null, [
                'email' => $request->email
            ]);
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if ($user && $user->failed_logins >= 5) {
            $user->update(['locked' => true]);
            AuditLogService::log($request, 'ACCOUNT_LOCKED', 'users', $user->id);
            return back()->withErrors(['email' => 'Your account is locked. Contact admin.']);
        }

        // Generate OTP
        $otp = $authService->generateOtp($user);
        $authService->sendOtp($user, $otp);

        AuditLogService::log($request, 'LOGIN_ATTEMPT', 'users', $user->id, null, [
            'otp_sent' => true
        ]);

        // Store OTP session marker
        session(['otp_pending_user' => $user->id]);

        $redirect = redirect()->route('otp.verify.form')
            ->with('info', 'An OTP has been sent to your email. Enter it to complete login.');

        if (env('SHOW_OTP_ON_SCREEN', false)) {
            $redirect->with('dev_otp', $otp->otp);
        }

        return $redirect;
    }

    public function store2(LoginRequest $request)
    {

        $request->authenticate();

        $request->session()->regenerate();

        $notification = array(
            'message' => 'Admin Login Successfully',
            'alert-type' => 'info'
        );

        return redirect()->intended(RouteServiceProvider::HOME)->with($notification);
    }

    public function store1(LoginRequest $request): RedirectResponse
    {
        // Authenticate the email and password
        $request->authenticate();

        // Ensure the user is authenticated
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User authentication failed. Please try again.']);
        }

        // Check user status before proceeding
        if (in_array($user->status, ['inactive', 'suspended', 'disabled'])) {
            Log::warning("User Restricted - Logging Out Immediately: ID {$user->id}, Status: {$user->status}");

            Auth::logout(); // Force logout
            return redirect()->route('account.restricted')->with('error', 'Your account is restricted. Contact the system administrator.');
        }

        // Generate a 6-digit verification code
        $verificationCode = mt_rand(100000, 999999);

        // Save the verification code in the database
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(90), // 1-minute expiration
        ]);

        // Send the verification code via email
        $subject = 'Your Verification Code';
        $body = "Hello,\n\nYour verification code is: $verificationCode.\n\nPlease note that it expires in 60 seconds.\n\nThank you.";
        EmailService::sendEmail($user->email, $subject, $body);

        // Log the user out temporarily to enforce verification step
        auth()->logout();

        // Redirect to the verification page with the user's ID in the session
        return redirect()->route('verify.code')->with('user_id', $user->id);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
