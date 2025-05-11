<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMenuAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set a global view variable to indicate admin menu access
        $isAdmin = Auth::check() && Auth::user()->canAccessAdminPanel();
        view()->share('show_admin_menus', $isAdmin);
        
        return $next($request);
    }
}
