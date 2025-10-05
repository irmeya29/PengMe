<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
      public function handle(Request $request, Closure $next)
    {
        $company = auth('web')->user();
        if (!$company || !$company->is_active) {
            auth('web')->logout();
            return redirect()->route('login')->withErrors('Votre entreprise est suspendue.');
        }
        return $next($request);
    }
}
