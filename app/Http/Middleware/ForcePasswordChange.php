<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->password_reset) {
            $allowed = [
                'change.password',
                'update.password',
                'admin.logout',
                'admin.logout.page',
                'logout',
            ];

            if (!in_array($request->route()?->getName(), $allowed)) {
                return redirect()->route('change.password')
                    ->with('warning', 'Please change your password before proceeding.');
            }
        }

        return $next($request);
    }
}
