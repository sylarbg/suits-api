<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\CitizensController;
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

    Route::post('/lawyers/{lawyer}/appointments', [LawyerAppointmentsController::class, 'store'])->name('appointments.store');
    Route::put('/lawyers/{lawyer}/appointments/{appointment}', [LawyerAppointmentsController::class, 'update']);
    Route::put('/lawyers/{lawyer}/appointments/{appointment}/reschedule', [LawyerAppointmentsController::class, 'reschedule']);
    Route::put('/lawyers/{lawyer}/appointments/{appointment}/confirm', [LawyerAppointmentsController::class, 'confirm']);

    Route::delete('/lawyers/{lawyer}/appointments/{appointment}', [LawyerAppointmentsController::class, 'delete']);

    Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentsController::class, 'index']);
});

Route::get('/lawyers', [LawyersController::class, 'index'])->name('lawyers.index');
Route::get('/citizens', [CitizensController::class, 'index'])->middleware('can:search-citizen')->name('citizens.index');

