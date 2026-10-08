<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoggedInController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->password_reset) {
            return redirect()->route('change.password');
        }

        if ($user->hasAnyRole(['Superadmin', 'admin', 'Corporation Secretary', 'CEO'])) {
            return redirect()->route('workflow.dashboard');
        }

        if ($user->hasRole('Board Member')) {
            return redirect()->route('board.portal.dashboard');
        }

        return view('default_index');
    }
}
