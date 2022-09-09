<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Verifikasi
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->is_email_verified) {
            auth()->logout();
            Session::flash('alert_type', 'error');
            Session::flash('alert_message', 'Your account has not been activated, please check your email !');
            return \redirect()->route('login');
        }
   
        return $next($request);
    }
}
