<?php

namespace App\Facade;

use App\Services\JwtServices\JwtService;
use Illuminate\Support\Facades\Facade;

class Jwt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JwtService::class;
    }
}
