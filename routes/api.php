<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\MedicalBackgroundController;
use App\Http\Controllers\PatientDemographicController;
use App\Http\Controllers\PatientSympthomController;
use App\Http\Controllers\RDTController;
use App\Http\Controllers\ReferalController;
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

 Route::get('amigoRdtCovidSheets', [PatientDemographicController::class, 'amigoRdtCovidSheets']);
 Route::get('amigoRdtMalariaSheets', [PatientDemographicController::class, 'amigoRdtMalariaSheets']);
  
// Route::get('endtoendsheets', [PatientDemographicController::class, 'endToEndsheets']);
// Route::get('r-sheets', [ReferalController::class, 'rsheets']);

Route::get('sheets', [PatientDemographicController::class, 'sheets']);
Route::get('newDiagnostedSheets', [PatientDemographicController::class, 'NewDiagnostedSheets']);


Route::get('r-sheets', [ReferalController::class, 'rsheets']);
Route::get('vitalSheets', [VitalParameterController::class, 'vitalSheets']);
Route::get('rdtSheets', [RDTController::class, 'rdtSheets']);
Route::get('symptomsSheets', [PatientSympthomController::class, 'symptomsSheets']);
Route::get('bgmSheet', [MedicalBackgroundController::class, 'bgmSheet']);
Route::get('followsSheets', [ReferalController::class, 'patientFollowedUp']);

Route::get('referalConfirmSheets', [ReferalController::class, 'referalConfirmSheets']);
Route::get('referalDataSheets', [ReferalController::class, 'referalDataSheets']);

Route::get('followUpDataSheets', [FollowUpController::class, 'followUpDataSheets']);
Route::get('followConfirmSheets', [FollowUpController::class, 'followConfirmSheets']);

 



Route::middleware('auth:sanctum')->group(function (): void {
    Route::middleware(['cors'])->group(function () {
        Route::resource('patients', PatientDemographicController::class);
    Route::get('conditions/{patient}', [PatientDemographicController::class, 'allConditionsPatient']);
    Route::get('mbr/{patient}', [PatientDemographicController::class, 'mustBeReferal']);
    Route::get('refer/{patient}', [PatientDemographicController::class, 'referalByPatient']);
    Route::get('referOK/{patient}', [PatientDemographicController::class, 'referalByPatientOK']);
    Route::resource('consultations', ConsultationController::class);
    Route::post('upload', [PatientDemographicController::class, 'uploadPhoto']);
    Route::get('qrcode', [PatientDemographicController::class, 'qrCode']);
    Route::post('print', [PatientDemographicController::class, 'print']);
    Route::get('stats', [PatientDemographicController::class, 'stats']);
    Route::get('count', [PatientDemographicController::class, 'getStatisticToday']);
    Route::post('find', [PatientDemographicController::class, 'accounts']);
    Route::post('findReferal', [ReferalController::class, 'findstatsReferal']);
    Route::post('filter', [PatientDemographicController::class, 'index']);
    Route::resource('vitals', VitalParameterController::class);
    Route::apiResource('symptoms', PatientSympthomController::class);
    Route::post('rdt/started', [PatientSympthomController::class, 'started']);
    Route::apiResource('medical', MedicalBackgroundController::class);
    Route::apiResource('rdt', RDTController::class);
    Route::apiResource('referal', ReferalController::class);
    Route::post('confirm', [ReferalController::class, 'globalReferral']);
    Route::get('referStats', [ReferalController::class, 'statsReferal']);
    Route::get('follows', [ReferalController::class, 'patientFollowedUp']);
    Route::post('result', [RDTController::class, 'getLastAdd']);
    Route::apiResource('follow', FollowUpController::class);
    Route::post('confirmfollow', [FollowUpController::class, 'closeFollowing']);
    Route::post('followUpEnd', [FollowUpController::class, 'end']);
        
    });
    });