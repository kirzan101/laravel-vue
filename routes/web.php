<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/errors', function () {
    return Inertia::render('Error', [
        'code' => 500,
        'message' => 'Page not found'
    ]);
});

Route::get('/user-groups', [UserGroupController::class, 'index']);