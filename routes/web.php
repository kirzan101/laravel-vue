<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

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
    Route::resource('user-groups', UserGroupController::class)->only(['index', 'store', 'update', 'destroy']);
});
