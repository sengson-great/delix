<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoginCheck
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
    if (Sentinel::check()) {
        $user = Sentinel::getUser();
        if ($user->user_type == 'staff') {
            // PHYSICALLY ATTACH THE ID TO THE REQUEST
            $request->attributes->add(['verified_user_id' => $user->id]);
            
            \Log::info('LoginCheck: Verified Staff ID ' . $user->id);
            return $next($request);
        }
    }

    if ($request->ajax()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
    return redirect()->route('login');
}
}
