<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\LawyerAppointmentsController;
use App\Http\Controllers\LawyersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/lawyers/{lawyer}/appointments', [LawyerAppointmentsController::class, 'store']);
    Route::put('/lawyers/{lawyer}/appointments/{appointment}/reschedule', [LawyerAppointmentsController::class, 'reschedule']);
    Route::delete('/lawyers/{lawyer}/appointments/{appointment}', [LawyerAppointmentsController::class, 'delete']);

    Route::get('/appointments', [AppointmentsController::class, 'index']);
});

Route::get('/lawyers', [LawyersController::class, 'index']);

