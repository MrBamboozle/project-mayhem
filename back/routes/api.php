<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Authenticate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [Authenticate::class, 'login']);
Route::post('/register', [Authenticate::class, 'register']);

Route::middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
    Route::post('/logout', [Authenticate::class, 'logout']);
    Route::get('/me', [Authenticate::class, 'loggedInUser']);
});

Route::get('/refresh-token',[Authenticate::class, 'refreshAccessToken'])
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value
    ]);
