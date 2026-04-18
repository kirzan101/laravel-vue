<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user-groups', [UserGroupController::class, 'index']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user-groups', [\App\Http\Controllers\API\UserGroupApiController::class, 'index']);
    Route::get('/roles', [\App\Http\Controllers\API\RoleApiController::class, 'index']);
    Route::get('/profiles', [\App\Http\Controllers\API\ProfileApiController::class, 'index']);

    // search
    Route::get('/user-groups/search', [\App\Http\Controllers\API\UserGroupApiController::class, 'searchIndex']);
    Route::get('/roles/search', [\App\Http\Controllers\API\RoleApiController::class, 'searchIndex']);
});
