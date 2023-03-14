<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\SocialMediaAuthentication\Http\Controllers\SocialMediaAuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::get('auth/{driver}', [SocialMediaAuthenticationController::class, 'redirectToProvider'])->name('auth.driver');
Route::get('auth/{driver}/callback', [SocialMediaAuthenticationController::class, 'handleProviderCallback'])
            ->name('auth.callback');
