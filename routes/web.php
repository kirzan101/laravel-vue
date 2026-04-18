<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'index'])->name('login');
});

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::put('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('change-password');
    Route::put('/reset-password/{userId}', [\App\Http\Controllers\AuthController::class, 'resetPassword'])->name('reset-password');
    Route::put('/set-user-status/{userId}', [\App\Http\Controllers\AuthController::class, 'setUserStatus'])->name('set-user-status');

    Route::get('/errors', function () {
        return Inertia::render('Error', [
            'code' => 500,
            'message' => 'Page not found'
        ]);
    });


    Route::get('/', function () {
        return Inertia::render('Home');
    });


    // Route::get('user-groups', [UserGroupController::class, 'index']);
    Route::resource('user-groups', \App\Http\Controllers\UserGroupController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('profiles', \App\Http\Controllers\ProfileController::class)->only(['index', 'store', 'update']);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'store', 'update', 'destroy']);
});
