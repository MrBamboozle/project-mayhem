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
use App\Http\Controllers\UserNotificationController;
use App\Models\Event as EventModel;
use App\Models\User;
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

Route::get(RouteEnum::EVENTS->path(), [EventController::class, 'index'])->middleware('parse.query');
Route::get(RouteEnum::EVENTS->path() . '/{event}', [EventController::class, 'show']);

// **** Attempt Login middleware ****
// Attempt to log in user if sanctum Auth token is present
// If auth failed, continue to the route but Auth::user() is null, means that user is not logged in
// Checking to see what user data to return
Route::middleware('attempt.login')->group(function () {
    Route::get(RouteEnum::USERS->path(), [UserController::class, 'index'])->middleware('parse.query');
    Route::get(RouteEnum::USERS->path() . '/{user}', [UserController::class, 'show']);
});

// TODO Remove Policy references
//  Users and Events
Route::group(
    ['middleware' => ['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value]],
    function () {
        Route::post(RouteEnum::LOGOUT->path(), [Authenticate::class, 'logout']);
        Route::get(RouteEnum::ME->path(), [Authenticate::class, 'loggedInUser']);

        //User api
        Route::post(
            RouteEnum::USERS->path(),
            [UserController::class, 'store']
        )->can('create', User::class
        );
        Route::patch(
            RouteEnum::USERS->path() . '/password-change/{user}',
            [UserController::class, 'changePassword']
        )->can('resetPassword', 'user');
        Route::patch(
            RouteEnum::USERS->path() . '/{user}',
            [UserController::class, 'update']
        )->can('update', 'user');
        Route::delete(
            RouteEnum::USERS->path() . '/{user}',
            [UserController::class, 'destroy']
        )->can('delete', 'user');
        Route::post(
            RouteEnum::USERS->path() . '/{user}' . RouteEnum::AVATARS->path() . '/{avatar?}',
            [UserController::class, 'addAvatar']
        )->can('addAvatar', 'user');


        //Avatar api
        Route::get(RouteEnum::AVATARS->path(), AvatarController::class);
        //Location api
        Route::post(RouteEnum::LOCATION->path(), LocationController::class);

        //Event api
        Route::post(
            RouteEnum::EVENTS->path(),
            [EventController::class, 'store']
        )->can('create', EventModel::class);
        Route::post(
            RouteEnum::EVENTS_PRIVATE->path(),
            [EventController::class, 'store']
        )->can('create', EventModel::class);
        Route::patch(
            RouteEnum::EVENTS->path() . '/{event}',
            [EventController::class, 'update']
        )->can('update', 'event');
        Route::delete(
            RouteEnum::EVENTS->path() . '/{event}',
            [EventController::class, 'destroy']
        )->can('delete', 'event');
        Route::post(
            RouteEnum::EVENTS_ENGAGE->value . '/{event}',
            [EventController::class, 'engageEvent']
        );

        // UserNotification api
        Route::get(
            RouteEnum::USER_NOTIFICATIONS->path(),
            [UserNotificationController::class, 'index']
        );
        Route::patch(
            RouteEnum::USER_NOTIFICATIONS_ALL->path(),
            [UserNotificationController::class, 'updateAll']
        );
        Route::patch(
            RouteEnum::USER_NOTIFICATIONS->path() . '/{userNotification}',
            [UserNotificationController::class, 'update']
        );
        Route::delete(
            RouteEnum::USER_NOTIFICATIONS_ALL->path(),
            [UserNotificationController::class, 'deleteAll']
        );
        Route::delete(
            RouteEnum::USER_NOTIFICATIONS->path() . '/{userNotification}',
            [UserNotificationController::class, 'destroy']
        );
    }
);

Route::middleware(['auth:sanctum','ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])->group(function () {
    // get new access_token|refresh_token pair
    Route::get(RouteEnum::REFRESH->path(),[Authenticate::class, 'refreshAccessToken']);
});
