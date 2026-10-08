<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VerificationCode;
use App\Http\Services\EmailService;
use Carbon\Carbon;


class VerificationController extends Controller
{
    //
    public function verifyCode1(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        $verification = VerificationCode::where('user_id', $request->user_id)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verification) {
            // Log the user in
            auth()->loginUsingId($request->user_id);

            // Delete the verification code
            $verification->delete();

            // Redirect based on role
            $user = auth()->user();
            $url = match($user->role) {
                'admin', 'secretary', 'evaluator' => '/backend/dashboard',
                'vendor' => '/frontend/dashboard',
                'user' => '/technical/dashboard',
                default => '/dashboard',
            };

            return redirect()->intended($url)->with([
                'message' => 'Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }
    public function verifyCode(Request $request)
    {
        // Combine digits into a single code
        $code = implode('', [
            $request->input('digit1'),
            $request->input('digit2'),
            $request->input('digit3'),
            $request->input('digit4'),
            $request->input('digit5'),
            $request->input('digit6'),
        ]);

        $request->merge(['code' => $code]); // Add the combined code to the request

        $request->validate([
            'code' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        $verification = VerificationCode::where('user_id', $request->user_id)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verification) {

            auth()->loginUsingId($request->user_id);
            session()->regenerate();


            $verification->delete();

            $user = auth()->user();
//            $url = match($user->role) {
//                'admin', 'secretary', 'evaluator' => '/backend/dashboard',
//                'vendor' => '/frontend/dashboard',
//                'user' => '/technical/dashboard',
//                default => '/dashboard',
//            };
            $url='/dashboard';

            return redirect()->intended($url)->with([
                'message' => 'Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }
    public function showVerificationForm1()
    {
        // Retrieve user_id from the session
        $userId = session('user_id');

        // If no user_id is present, redirect to login page
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        // Retrieve the most recent verification code for the user
        $verification = VerificationCode::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if the verification code is still valid
        if ($verification && $verification->expires_at > Carbon::now()) {
            // Verification code is still valid, pass data to the view
            return view('auth.verify-code', [
                'user_id' => $userId,
                'expires_in' => $verification->expires_at->diffInSeconds(Carbon::now()) // Time left for expiration
            ]);
        } else {
            // Verification code has expired, redirect to login
            return redirect()->route('login')->withErrors(['error' => 'Verification code expired. Please try again.']);
        }
    }
    public function showVerificationForm()
    {
        // Retrieve user_id from the session
        $userId = session('user_id');

        // If no user_id is present, redirect to login page
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        // Retrieve the most recent verification code for the user
        $verification = VerificationCode::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if the verification code is still valid
        if ($verification && $verification->expires_at > Carbon::now()->timestamp) {
            // Convert expires_at timestamp to Carbon instance
            $expiresAt = Carbon::createFromTimestamp($verification->expires_at);
            $c=$expiresAt->diffInSeconds(Carbon::now());

            $timestamp1 = Carbon::createFromFormat('Y-m-d H:i:s', $verification->expires_at);
            $timestamp2 = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());

// Calculate the difference in seconds
            $secondsDifference = $timestamp1->diffInSeconds($timestamp2);
//           dd($expiresAt);
            // Verification code is still valid, pass data to the view
            return view('auth.verify-code', [
                'user_id' => $userId,
//                'expires_in' => $expiresAt->diffInSeconds(Carbon::now()) // Time left for expiration
                'expires_in' => $timestamp1->diffInSeconds($timestamp2)
            ]);
        } else {
            // Verification code has expired, redirect to login
            return redirect()->route('login')->withErrors(['error' => 'Verification code expired. Please try again.']);
        }
    }


    public function showVerificationForm2()
    {
        // Retrieve user_id from the session
        $userId = session('user_id');

        // If no user_id is present, redirect to login page
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        // Retrieve the most recent verification code for the user
        $verification = VerificationCode::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if the verification code exists and if it's still valid
        if ($verification) {
            // Calculate the time remaining before expiration
            $expiresAt = Carbon::parse($verification->expires_at); // Convert the expires_at timestamp to a Carbon instance
            $expiresIn = $expiresAt->diffInSeconds(Carbon::now(), false); // Get the difference in seconds, passing 'false' to handle negative values if expired

            if ($expiresIn > 0) {
                // Verification code is still valid, pass data to the view
                return view('auth.verify-code', [
                    'user_id' => $userId,
                    'expires_in' => $expiresIn, // Time left for expiration in seconds
                ]);
            }
        }

        // Verification code has expired or is not found, redirect to login
        return redirect()->route('login')->withErrors(['error' => 'Verification code expired. Please try again.']);
    }



    public function resendVerificationCode(Request $request)
    {
        $userId = session('user_id');

        // If no user_id, redirect to login page
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        // Regenerate the verification code and resend it
        $user = User::find($userId);
        if ($user) {
            $verificationCode = mt_rand(100000, 999999);

            VerificationCode::create([
                'user_id' => $user->id,
                'code' => $verificationCode,
                'expires_at' => Carbon::now()->addMinute(),
            ]);

            $subject = 'Your Verification Code';
            $body = "Hello,\n\nYour verification code is: $verificationCode.\n\nPlease note that it expires in 60 seconds.\n\nThank you.";
            EmailService:: sendEmail($user->email,$subject, $body);

            return back()->with('success', 'Verification code has been resent.');
        }

        return redirect()->route('login')->withErrors(['error' => 'Unable to resend verification code.']);
    }
    public function resendCode1(Request $request)
    {
        $user = User::findOrFail($request->user_id);  // Find the user based on the user_id

        // Generate a new verification code
        $verificationCode = mt_rand(100000, 999999);

        // Save the new verification code in the database
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinute(),  // 1-minute expiration
        ]);

        // Send the new verification code to the user's email
        $subject = 'Your New Verification Code';
        $body = "Hello,\n\nYour new verification code is: $verificationCode.\n\nIt expires in 60 seconds.\n\nThank you.";
        $recipient = $user->email;

        app()->make(EmailService::class)->sendEmail($subject, $body, $recipient);

        return redirect()->route('verify.code')->with('user_id', $user->id)
            ->with('message', 'A new verification code has been sent to your email.');
    }
    public function resendCode(Request $request)
    {
        // Retrieve user based on user_id passed in the form
        $user = User::findOrFail($request->user_id);

        // Generate a new verification code
        $verificationCode = mt_rand(100000, 999999);

        // Save the new verification code in the database
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinute(),  // 1-minute expiration
        ]);

        // Send the new verification code to the user's email
        $subject = 'Your New Verification Code';
        $body = "Hello,\n\nYour new verification code is: $verificationCode.\n\nIt expires in 60 seconds.\n\nThank you.";
        $recipient = $user->email;

        app()->make(EmailService::class)->sendEmail($subject, $body, $recipient);

        // Redirect back to the verification page with success message
        return redirect()->route('verify.code')->with('user_id', $user->id)
            ->with('message', 'A new verification code has been sent to your email.');
    }


}
