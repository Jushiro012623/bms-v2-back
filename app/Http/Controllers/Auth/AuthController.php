<?php

namespace App\Http\Controllers\Auth;

use App\Facade\Jwt;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $field = filter_var($validated['username'], FILTER_VALIDATE_EMAIL) ? "email" : "username";

        $credentials = [$field => $validated['username'], 'password' => $validated['password']];

        if (RateLimiter::tooManyAttempts('login:' . $request->ip(), 5)) {
            throw new TooManyRequestsHttpException('Too many login attempts. Try again later.');
        }

        if(! Auth::attempt($credentials)){
            throw new UnauthorizedException('Invalid credentials');
        }

        $accessToken = Jwt::issue(Auth::id());

        return response()->success("Logged In Successfully" ,[
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);

    }
}
