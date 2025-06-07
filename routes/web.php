<?php

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
        'code' => 404,
        'message' => 'Page not found'
    ]);
});