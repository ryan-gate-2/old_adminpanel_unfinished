<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPhone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */

        public function handle($request, Closure $next)
        {

            if ( Auth::user())
            {
                if(Auth::user()->phonenumber_state === 1) {
                    if(Auth::user()->phonenumber_wrong_tries > 12) {
                        abort(403, 'You have tried too many invalid attempts.');                 
                    }
                return redirect()->route('verification-phone');
                }

            }  
       return $next($request);



        }
}
