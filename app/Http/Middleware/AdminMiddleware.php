<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }

        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Access denied. Admin only.');
        }

        // User is admin, continue to the controller
        return $next($request);
    }
}

