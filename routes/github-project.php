<?php

/*
|--------------------------------------------------------------------------
| Core Routes
|--------------------------------------------------------------------------
|
| Here is where you can register core routes for your application. The RouteServiceProvider and all of they load these | routes will
| be assigned to the "core" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

$routePrefix = config('github-project.route_prefix');

Route::prefix($routePrefix)->name("$routePrefix.")->group(function () {
    Route::get('/', \CSlant\GitHubProject\Actions\NotificationAction::class);
});
