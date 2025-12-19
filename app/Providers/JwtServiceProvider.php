<?php

namespace App\Providers;

use App\Services\JwtServices\JwtService;
use Illuminate\Support\ServiceProvider;

class JwtServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(JwtService::class, function ($app) {
            return new JwtService();
        });
    }

    public function boot(): void
    {
    }
}
