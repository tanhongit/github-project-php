<?php

/*
|--------------------------------------------------------------------------
| Blog Core Routes
|--------------------------------------------------------------------------
|
| Here is where you can register core routes for your application. The RouteServiceProvider and all of they load these | routes will
| be assigned to the "core" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/index', function () {
    return redirect('https://cslant.com', 301);
})->name('blog-core.index');
