<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        // Check if the user is an admin
        if (Auth::user()->type === 'visitor') {
            return $next($request); // Allow admin to proceed
        }

        // Redirect or return a forbidden response for non-admins
        Auth::logout();
        return redirect()->route('login')->with('error', 'You do not have admin access.');
        // abort(403);
    }

}
