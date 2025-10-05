<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImpersonationGuard
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('impersonate_admin_id')) {
            // TTL simple (2h)
            $startedAt = session('impersonate_started_at');
            if (!$startedAt) {
                session(['impersonate_started_at' => now()]);
            } elseif (now()->diffInMinutes($startedAt) > 120) {
                // Auto-stop
                $adminId = session('impersonate_admin_id');
                auth('web')->logout();
                session()->forget(['impersonate_admin_id','impersonate_started_at']);
                auth('admin')->loginUsingId($adminId);
                return redirect()->route('admin.companies.index')
                    ->with('ok','Impersonation expirée (2h).');
            }
        }
        return $next($request);
    }
}
