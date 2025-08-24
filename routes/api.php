<?php

use App\Http\Controllers\API\ProfileApiController;
use App\Http\Controllers\API\UserGroupApiController;
use Illuminate\Support\Facades\Route;

// Route::get('/user-groups', [UserGroupController::class, 'index']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user-groups', [UserGroupApiController::class, 'index']);
    Route::get('/profiles', [ProfileApiController::class, 'index']);

    // search
    Route::get('/user-groups/search', [UserGroupApiController::class, 'searchIndex']);
});
