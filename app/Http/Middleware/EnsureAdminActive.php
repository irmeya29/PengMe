<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admin')->user();

        if (!$admin || !($admin->active ?? true)) {
            auth('admin')->logout();
            return redirect()->route('admin.login')->withErrors('Compte admin inactif.');
        }
        return $next($request);
    }
}
