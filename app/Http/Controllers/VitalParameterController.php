<?php

namespace App\Http\Controllers;

use App\Http\Requests\VitalParameterRequest;
use App\Models\PatientDemographic;
use App\Models\VitalParameter;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VitalParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VitalParameter::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VitalParameterRequest $request)
    {
        $vital_data = $this->setFlag($request->validated());
        $VitalParameter = VitalParameter::updateOrCreate(['id' => $request['id']], $vital_data);
        return $this->successResponse($VitalParameter, "Paramettre vital créée avec succès");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VitalParameter  $VitalParameter
     * @return \Illuminate\Http\Response
     */
    public function show(VitalParameter $vital)
    {
        return $this->successResponse($vital);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VitalParameter  $VitalParameter
     * @return \Illuminate\Http\Response
     */
    public function update(VitalParameterRequest $request, VitalParameter $vital)
    {
        $vital->create($request->validated());
        $this->successResponse($vital, "Paramettre vital modifié avec succès");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VitalParameter  $VitalParameter
     * @return \Illuminate\Http\Response
     */
    public function destroy(VitalParameter $VitalParameter)
    {
        //
    }

    public function setFlag($request_data) {
        $dt = Carbon::now();
        $time = $dt->toTimeString();
        switch ($request_data['vital_type'])
        {
            case VitalParameter::OXYGEN:
                if($request_data['oxygen_saturation'] == '95% or + (Normal)') {
                    $status = VitalParameter::FLAG_NORMAL;
                } elseif($request_data['oxygen_saturation'] == '93-94% (Low)') {
                    $status = VitalParameter::FLAG_MID_BAD;
                }else{
                    $status = VitalParameter::FLAG_VER_BAD;
                }
                $data = [
                'patient_id' => $request_data['patient_id'],
                'date_of_checking' => $dt->format('Y-m-d'),
                'time_of_checking' => $time,
                'oxygen_saturation' => $request_data['oxygen_saturation'],
                'vital_type' => $request_data['vital_type'],
                'vital_flag' => $status,
                ];

                break;
            case VitalParameter::TEMPERATURE:
                if($request_data['temperature'] == '35.9 > Hypotermy') {
                    $status = VitalParameter::FLAG_MID_BAD;
                } elseif($request_data['temperature'] == '35.9 - 37.5 Normal') {
                    $status = VitalParameter::FLAG_NORMAL;
                }else{
                    $status = VitalParameter::FLAG_VER_BAD;
                }
                $data = [
                    'patient_id' => $request_data['patient_id'],
                    'date_of_checking' => $dt->format('Y-m-d'),
                    'time_of_checking' => $time,
                    'temperature' => $request_data['temperature'],
                    'vital_type' => $request_data['vital_type'],
                    'vital_flag' => $status,
                ];
                break;
            case VitalParameter::GLUCOSE:
                $numberAsString = $request_data['glucose_level'];
                if ($numberAsString <= 0.7 OR $numberAsString <= 1.20) {
                    $status = VitalParameter::FLAG_NORMAL;
                } elseif ($numberAsString <= 1.21 OR $numberAsString <= 1.26) {
                    $status = VitalParameter::FLAG_MID_BAD;
                } elseif ($numberAsString > 1.26) {
                    $status = VitalParameter::FLAG_VER_BAD;
                }
                $data = [
                    'patient_id' => $request_data['patient_id'],
                    'date_of_checking' => $dt->format('Y-m-d'),
                    'time_of_checking' => $time,
                    'glucose_level' => $request_data['glucose_level'],
                    'vital_type' => $request_data['vital_type'],
                    'vital_flag' => $status,

                ];	
                break;
            case VitalParameter::BLOODPRESSURE:
                $sys_avarage = ($request_data['bp_sys_right'] + $request_data['bp_sys_left']) / 2;
                $dias_avarage = ($request_data['bp_dias_right'] + $request_data['bp_dias_left']) / 2;
                if($sys_avarage >= 140 OR $dias_avarage >= 90) {
                        $status = VitalParameter::FLAG_DANGER;
                }elseif($sys_avarage < 90 OR $dias_avarage < 60) {
                        $status = VitalParameter::FLAG_MID_BAD;
                }else {
                        $status = VitalParameter::FLAG_NORMAL;
                }
                $data = [
                    'patient_id' => $request_data['patient_id'],
                    'bp_sys_right' => $request_data['bp_sys_right'],
                    'bp_sys_left' => $request_data['bp_sys_left'],
                    'bp_dias_right' => $request_data['bp_dias_right'],
                    'bp_dias_left' => $request_data['bp_dias_left'],
                    'bp_sys_avarage' => $sys_avarage,
                    'bp_dias_avarage' => $dias_avarage,
                    'date_of_checking' => $dt->format('Y-m-d'),
                    'time_of_checking' => $time,
                    'vital_type' => $request_data['vital_type'],
                    'vital_flag' => $status,
                ];
                break;
            case VitalParameter::MALNUTRITION:
                $patient = PatientDemographic::find($request_data['patient_id']);
                $age = Carbon::parse($patient->date_of_birth)->age;
                $status = 0;
                if($age < 5) {
                    if($request_data['arm_circumference'] < 125 ) {
                        $status = VitalParameter::FLAG_DANGER;
                    } else {
                        $status = VitalParameter::FLAG_NORMAL;
                    }
                }
                if($patient->pregnant == "Yes") {

                    if($request_data['arm_circumference'] < 230 ) {
                        $status = VitalParameter::FLAG_DANGER;
                    } else {
                        $status = VitalParameter::FLAG_NORMAL;
                    }
                }

                $data = [
                    'patient_id' => $request_data['patient_id'],
                    'date_of_checking' => $dt->format('Y-m-d'),
                    'time_of_checking' => $time,
                    'arm_circumference' => $request_data['arm_circumference'],
                    'vital_type' => $request_data['vital_type'],
                    'vital_flag' => $status,
                    ];
                break;
        }
        if(!isset($request_data['id'])) {
            VitalParameter::is_active($request_data['patient_id'],  $request_data['vital_type']);
            $data['is_active'] = 1;
            $data['created_by'] = Auth::user()->id;
        }
        return $data;
    }

}