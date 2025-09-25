<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ComptableMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('comptable')->check()){
            return $next($request);
        }else{
            return redirect()->route('comptable.login');
        }
    }
}