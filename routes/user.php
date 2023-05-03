<?php

use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\FindController;
use App\Http\Controllers\User\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USER API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(UserAuthController::class)->prefix('auth')->group(function() {
    Route::post('/login', 'login');
    Route::post('/logout','logout')->middleware('auth:users');
    Route::post('/register', 'register');
    Route::get('/me' ,'getMe')->middleware('auth:users');
});

Route::controller(FindController::class)->prefix('find')->group(function() {
    Route::get('/rooms','rooms');
    Route::get('/room/{room}','showRoom');
    Route::get('/room/{room}/timeslots','roomTimeslots');
});

Route::controller(BookingController::class)->prefix('booking')->middleware('auth:users')->group(function() {
    Route::get('/all','all');
    Route::post('/create','create');
    Route::get('/{booking}','show');
    Route::post('/{booking}/cancel','cancel');
});

