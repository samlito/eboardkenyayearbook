<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    //
    public function showVerificationForm()
    {
        return view('auth.verify');
    }

    public function verifyCode0(Request $request)
    {
//        $request->validate([
//            'verification_code' => 'required|numeric|digits:5',
//        ]);
        $request->validate([
            'verification_code' => 'required|digits:5|numeric',
        ]);



        $user = Auth::user();

        // Check if the provided code is valid and not expired
        if ($user->two_factor_token === $request->verification_code && Carbon::now()->lt($user->two_factor_expires_at)) {
            // Clear the token and expiration time
            $user->two_factor_token = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // Redirect to the dashboard or home page
            return redirect()->route('dashboard');
        }

        // If the code is invalid or expired
        return back()->withErrors(['verification_code' => 'The code is invalid or expired.']);
    }
    public function verifyCode1(Request $request)
    {
        // Concatenate the input fields into a single string
        $verificationCode = implode('', $request->only(['verification_code_0', 'verification_code_1', 'verification_code_2', 'verification_code_3', 'verification_code_4']));

        // Validate the concatenated string
        $request->validate([
            'verification_code' => 'required|digits:5|numeric',
        ]);

        // Check if the code is valid and not expired
        $user = Auth::user();
        if ($user->two_factor_token === $verificationCode && Carbon::now()->lt($user->two_factor_expires_at)) {
            // Clear the token and expiration time
            $user->two_factor_token = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // Redirect to the dashboard or home page
            return redirect()->route('dashboard');
        }

        // If the code is invalid or expired
        return back()->withErrors(['verification_code' => 'The code is invalid or expired.']);
    }
    public function verifyCode2(Request $request)
    {
        // Concatenate the input fields into a single string
        $verificationCode = implode('', $request->only(['verification_code_0', 'verification_code_1', 'verification_code_2', 'verification_code_3', 'verification_code_4']));

        // Validate the concatenated string
        $request->validate([
            'verification_code' => 'required|digits:5|numeric',
        ]);

        // Check if the code is valid and not expired
        $user = Auth::user();
        if ($user->two_factor_token === $verificationCode && Carbon::now()->lt($user->two_factor_expires_at)) {
            // Clear the token and expiration time
            $user->two_factor_token = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // Redirect to the dashboard or home page
            return redirect()->route('dashboard');
        }

        // If the code is invalid or expired
        return back()->withErrors(['verification_code' => 'The code is invalid or expired.']);
    }
    public function verifyCode(Request $request)
    {
        // Concatenate the input fields into a single string
        $verificationCode = implode('', $request->only([
            'verification_code_0', 'verification_code_1', 'verification_code_2', 'verification_code_3', 'verification_code_4'
        ]));

        // Validate the concatenated string
        $request->validate([
            'verification_code' => 'required|digits:5|numeric',
        ]);

        // Check if the code is valid and not expired
        $user = Auth::user();
        if ($user->two_factor_token === $verificationCode && Carbon::now()->lt($user->two_factor_expires_at)) {
            // Clear the token and expiration time
            $user->two_factor_token = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // Redirect to the dashboard or home page
            return redirect()->route('dashboard');
        }

        // If the code is invalid or expired
        return back()->withErrors(['verification_code' => 'The code is invalid or expired.']);
    }


}
