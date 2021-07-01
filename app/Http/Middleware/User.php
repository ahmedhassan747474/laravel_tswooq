<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'api')
    {
        if($guard == 'api')
        {
            if(Auth::guard($guard)->check())
            {
                if(Auth::guard($guard)->user()->status == 0)
                {
                    return response()->json(['message' => trans('common.suspend_account'), 'status_code' => 400], 400);
                }
            }    
        }
        else if($guard == 'web')
        {
            if(Auth::guard($guard)->user()->status == 0)
            {
                Auth::logout();
                return \Redirect('login');
            }
        }
        return $next($request);
    }
}
