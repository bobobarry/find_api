<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\MedicalBackgroundController;
use App\Http\Controllers\PatientDemographicController;
use App\Http\Controllers\PatientSympthomController;
use App\Http\Controllers\RDTController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VitalParameterController;
use App\Models\PatientDemographic;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('/', function () {
//     echo "hello word !!!"
// });


Route::middleware(['cors'])->group(function () {
    Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function (): void {
        // public routes
        Route::post('login', [UserController::class, 'login'])
            ->name('login');
        Route::post('register', [UserController::class, 'register'])
            ->name('register');
        // Route::get("agent", [UserController::class, 'agents']);
        // Route::get("admin", [UserController::class, 'admins']);
    
    
        // protected routes
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get("users", [UserController::class, 'users']);
            Route::get("agent", [UserController::class, 'agents']);
            Route::get("admin", [UserController::class, 'admins']);
    
        });
    });
});



Route::middleware('auth:sanctum')->group(function (): void {
    Route::middleware(['cors'])->group(function () {
        Route::resource('patients', PatientDemographicController::class);
        Route::resource('consultations', ConsultationController::class);
        Route::post('upload', [PatientDemographicController::class, 'uploadPhoto']);
        Route::get('qrcode', [PatientDemographicController::class, 'qrCode']);
        Route::post('print', [PatientDemographicController::class, 'print']);
        Route::get('stats', [PatientDemographicController::class, 'stats']);
        Route::get('count', [PatientDemographicController::class, 'getStatisticToday']);
        Route::post('find', [PatientDemographicController::class, 'accounts']);
        Route::resource('vitals', VitalParameterController::class);
        Route::apiResource('symptoms', PatientSympthomController::class);
        Route::post('rdt/started', [PatientSympthomController::class, 'started']);
        Route::apiResource('medical', MedicalBackgroundController::class);
        Route::apiResource('rdt', RDTController::class);
        Route::post('result', [RDTController::class, 'getLastAdd']);
        
    });
    });
