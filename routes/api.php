<?php

use App\Http\Controllers\API\UserGroupController;
use Illuminate\Support\Facades\Route;

// Route::get('/user-groups', [UserGroupController::class, 'index']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user-groups', [UserGroupController::class, 'index']);
});