<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LogoutCheck
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
        if (!Sentinel::check()) :
            return $next($request);
        endif;
        if(Sentinel::getUser()->user_type == 'merchant'):
            return redirect()->route('merchant.dashboard');
        elseif(Sentinel::getUser()->user_type == 'merchant_staff'):
            return redirect()->route('merchant.staff.dashboard');
        endif;
        return redirect()->route('dashboard');
    }
}
