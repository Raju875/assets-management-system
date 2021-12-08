<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class checkAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user_role = Auth::user()->role_id;
        $uri = $request->segment(1);

        switch (true) {
            case ($uri == 'dashboard' and (in_array($user_role, [1, 2]))):
                return $next($request); // allowed admin & employee to access dashboard module
                break;
            case ($uri == 'department' and (in_array($user_role, [1]))):
                return $next($request); // allowed admin to access department module
                break;
            case ($uri == 'user' and (in_array($user_role, [1, 2]))):
                return $next($request); // allowed admin & employee to access user module
                break;
            case ($uri == 'asset' and (in_array($user_role, [1]))):
                return $next($request); // allowed admin to access asset module
                break;
            case ($uri == 'allocate' and (in_array($user_role, [1]))):
                return $next($request); // allowed admin to access allocate module
                break;
            default:
                Session::flash('error', 'Invalid URL ! error code(' . $uri . '-' . $user_role . ')    ');
                return redirect('dashboard');
        }

        // return $next($request);
    }
}
