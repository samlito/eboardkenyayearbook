<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class EnsureSetupComplete
{
    protected $except = [
        'setup',
        'setup/*',
        'login',
        'logout',
        'register',
        'password/*',
        'forgot-password',
        'reset-password',
        'reset-password/*',
        'otp/*',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production') && env('SKIP_SETUP_WIZARD', false)) {
            return $next($request);
        }

        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        if (!$this->isSetupComplete()) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }

    protected function shouldPassThrough(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        if ($request->is('backend/*') || $request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            return true;
        }

        return false;
    }

    protected function isSetupComplete(): bool
    {
        return Cache::remember('setup_completed', 3600, function () {
            try {
                if (!Schema::hasTable('system_settings')) {
                    return false;
                }
                return SystemSetting::where('key', 'setup_completed')->where('value', '1')->exists();
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}
