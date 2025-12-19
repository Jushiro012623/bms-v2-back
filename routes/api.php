<?php

use App\Http\Controllers\Client\DocumentRequestController;
use App\Http\Controllers\Shared\DocumentTypesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(\App\Http\Controllers\Auth\AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});

Route::middleware('jwt')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = Auth::user();
        $user->with('roles');

        $user = array_merge(
            $user->toArray(),
            [
                'role' => $user->role->name,
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'name' => fake()->name()
            ]
        );

        return response()->success('success', $user);
    });
    Route::apiResource('document-requests', DocumentRequestController::class)
        ->names('document-requests');
    Route::apiResource('document-types', DocumentTypesController::class)
        ->names('document-types');
});
