<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('unauthenticated');
})->name('login');

Route::get('/forgot-password/form', function (Request $request) {
    if (!$request->hasValidSignature()) {
        return view('linkExpired');
    }

    return view('forgotPasswordForm', ['token' => $request->query->get('signature')]);
})->name('passwordForm');
