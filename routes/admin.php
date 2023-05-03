<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\RoomManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AdminAuthController::class)->prefix('auth')->group(function() {
    Route::post('/login','login');
    Route::post('/logout','logout')->middleware('auth:admins');;
});

Route::controller(RoomManagementController::class)
    ->middleware('auth:admins')
    ->prefix('room-management')
    ->group(function() {
        Route::get('/all','index');
        Route::post('/create','create');
        Route::get('/{room}','show');
        Route::get('/{room}/timeslots','timeslots');
        Route::post('/{room}/add-timeslot','addTimeSlot');
        Route::post('/{room}/add-timeslot-date','addTimeSlotDate');
        Route::post('/{room}/delete-timeslot','deleteTimeSlot');
        Route::post('/{room}/delete-all-timeslot','deleteAllTimeSlot');
        Route::post('/{room}/destroy','destroy');
});