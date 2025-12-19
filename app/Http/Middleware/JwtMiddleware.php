<?php

namespace App\Http\Middleware;

use App\Facade\Jwt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $token = Jwt::parseToken($request->bearerToken());

        JWT::validate($token);

        Auth::setUser(JWT::user($token));

        return $next($request);
    }
}
