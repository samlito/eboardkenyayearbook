<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
//        Log::info('CheckUserStatus middleware is being executed.');

        if (Auth::check()) {
            $user = Auth::user();
//            Log::info("User ID: {$user->id}, Status: {$user->status}");

            if (in_array($user->status, ['inactive', 'suspended', 'disabled'])) {
//                Log::warning("User Restricted - Logging Out: ID {$user->id}, Status: {$user->status}");
                Auth::logout();
                return redirect()->route('account.restricted')->with('error', 'Access Denied. Contact the system administrator.');
            }
        } else {
//            Log::warning("User not authenticated.");
        }

        return $next($request);
    }

}
