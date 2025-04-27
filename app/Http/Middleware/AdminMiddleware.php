<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access the admin panel.');
        }
        
        $user = Auth::user();
        
        // Support for both new role system and legacy is_admin flag
        if (!$user->isAdmin() && $user->role !== User::ROLE_MANAGER) {
            return redirect()->route('admin.login')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
} 