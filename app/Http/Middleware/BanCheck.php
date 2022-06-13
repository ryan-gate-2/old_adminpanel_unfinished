<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanCheck
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

            if ( Auth::user() && Auth::user()->active === 0)
            {
                //return redirect('home');
                abort(403, 'Your account is non-active - please contact support.');
            }
       return $next($request);


        }
}
