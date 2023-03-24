<?php

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



// Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function (): void {
//     // public routes
//     Route::post('login', [UserController::class, 'login'])
//         ->name('login');
//     Route::post('register', [UserController::class, 'register'])
//         ->name('register');

//         Route::get("agent", [UserController::class, 'agents']);
//         Route::get("admin", [UserController::class, 'admins']);


//     // protected routes
//     Route::middleware('auth:sanctum')->group(function (): void {
//         // Route::get("users", [UserController::class, 'index']);
//     });
// });

// Route::apiResource('patients', PatientDemographicController::class);
// Route::post('upload', [PatientDemographicController::class, 'uploadPhoto']);
// Route::apiResource('vitals', VitalParameterController::class);
// Route::apiResource('symptoms', PatientSympthomController::class);
// Route::post('rdt/started', [PatientSympthomController::class, 'started']);
// Route::apiResource('medical', MedicalBackgroundController::class);
// Route::apiResource('rdt', RDTController::class);

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function (): void {
    // public routes
    Route::post('login', 'UserController@login')
        ->name('login');
    Route::post('register','UserController@register')
        ->name('register');

        Route::get("agent", 'UserController@agents');

        Route::get("admin", 'UserController@admins');


    // protected routes
    Route::middleware('auth:sanctum')->group(function (): void {
        // Route::get("users", [UserController::class, 'index']);
    });
});

Route::apiResource('patients','PatientDemographicController');
Route::post('upload', 'PatientDemographicController@uploadPhoto');
Route::post('print', 'PatientDemographicController@print');
Route::apiResource('vitals', 'VitalParameterController');
Route::apiResource('symptoms', 'PatientSympthomController');
Route::post('rdt/started', 'PatientSympthomController@started');
Route::apiResource('medical', 'MedicalBackgroundController');
Route::apiResource('rdt', 'RDTController');
