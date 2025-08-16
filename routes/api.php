<?php

use App\Http\Controllers\API\UserGroupApiController;
use Illuminate\Support\Facades\Route;

// Route::get('/user-groups', [UserGroupController::class, 'index']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user-groups', [UserGroupApiController::class, 'index']);
});
