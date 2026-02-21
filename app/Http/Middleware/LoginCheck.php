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
        if (Sentinel::check()) :
            if(Sentinel::getUser()->user_type == 'staff'):
                return $next($request);
            endif;
            return redirect()->route('merchant.dashboard');
        endif;
        return redirect()->route('login');

    }
}
