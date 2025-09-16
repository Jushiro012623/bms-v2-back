<?php

use App\Http\Controllers\Client\DocumentRequestController;
use App\Http\Controllers\Shared\DocumentTypesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('document-requests', DocumentRequestController::class)
    ->names('document-requests');
Route::apiResource('document-types', DocumentTypesController::class)
    ->names('document-types');
