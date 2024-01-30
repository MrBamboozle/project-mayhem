<?php

use App\Enums\Route as RouteEnum;
use App\Enums\TokenAbility;
use App\Http\Controllers\Authenticate;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
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

Route::post(RouteEnum::LOGIN->path(), [Authenticate::class, 'login']);
Route::post(RouteEnum::REGISTER->path(), [Authenticate::class, 'register']);

Route::get(RouteEnum::CITIES->path(), CityController::class);

Route::get(RouteEnum::CATEGORIES->path(), CategoryController::class);

Route::get(RouteEnum::EVENTS->path() . '/{eventId}', [EventController::class, 'show']);
Route::get(RouteEnum::EVENTS->path(), [EventController::class, 'index']);

// **** Attempt Login middleware ****
// Attempt to log in user if sanctum Auth token is present
// If auth failed, continue to the route but Auth::user() is null, means that user is not logged in
// Checking to see what user data to return
Route::middleware('attempt.login')->group(function () {
    Route::get(RouteEnum::USERS->path(), [UserController::class, 'index']);
    Route::get(RouteEnum::USERS->path() . '/{id}', [UserController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
    Route::post(RouteEnum::LOGOUT->path(), [Authenticate::class, 'logout']);
    Route::get(RouteEnum::ME->path(), [Authenticate::class, 'loggedInUser']);

    //User api
    Route::post(RouteEnum::USERS->path(), [UserController::class, 'store']);
    Route::patch(RouteEnum::USERS->path() . '/password-change', [UserController::class, 'changePassword']);
    Route::patch(RouteEnum::USERS->path() . '/{id}', [UserController::class, 'update']);
    Route::delete(RouteEnum::USERS->path() . '/{id}', [UserController::class, 'destroy']);
    Route::post(
        RouteEnum::USERS->path() . '/{userId}' . RouteEnum::AVATARS->path() . '/{avatarId?}',
        [UserController::class, 'addAvatar']
    );


    //Avatar api
    Route::get(RouteEnum::AVATARS->path(), AvatarController::class);
    //Location api
    Route::post(RouteEnum::LOCATION->path(), LocationController::class);

    //Event api
    Route::post(RouteEnum::EVENTS->path(), [EventController::class, 'store']);
    Route::patch(RouteEnum::EVENTS->path() . '/{eventId}', [EventController::class, 'update']);
    Route::delete(RouteEnum::EVENTS->path() . '/{eventId}', [EventController::class, 'destroy']);
});

Route::middleware(['auth:sanctum','ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])->group(function () {
    // get new access_token|refresh_token pair
    Route::get(RouteEnum::REFRESH->path(),[Authenticate::class, 'refreshAccessToken']);
});
