<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/admin', [AuthController::class, 'admin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('/services', ServiceController::class);
    Route::apiResource('/appointments', AppointmentController::class);
    Route::get('/users/{user}/appointments', [AuthController::class, 'userAppointments']);
    Route::get('/appointments', [AppointmentController::class, 'getByDate']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::get('/forgot-password/{token}', [ForgotPasswordController::class, 'verifyToken']);
Route::post('/forgot-password/{token}', [ForgotPasswordController::class, 'resetPassword']);