<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientSympthomRequest;
use App\Models\PatientSympthoms;
use App\Models\RdtSreening;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientSympthomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PatientSympthoms::with(['rdts'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PatientSympthomRequest $request)
    {
        $symptom = PatientSympthoms::create($request->validated());
        if($symptom->id !== null) {
            PatientSympthoms::is_active($symptom->patient_id);
            $symptom->is_active = 1;
            $symptom->created_by = Auth::user()->id;
            $symptom->save();
            foreach($request['rdts'] as $rdt) {
                $this->agree($request['patient_id'], $symptom->id, $rdt['rdt_type']);
            }

        }
        return $this->successResponse($symptom, "Symptomes créée avec succès");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientSympthoms  $patientSympthoms
     * @return \Illuminate\Http\Response
     */
    public function show(PatientSympthoms $symptom)
    {
        $symptom->load(['rdts']);
        return $this->successResponse($symptom, null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientSympthoms  $patientSympthoms
     * @return \Illuminate\Http\Response
     */
    public function update(PatientSympthomRequest $request, PatientSympthoms $symptom)
    {
        $symptom->update($request->validated());
        return $this->successResponse($symptom, "Symptome modifiés avec sucèss");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientSympthoms  $patientSympthoms
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientSympthoms $patientSympthoms)
    {
        //
    }

    public function agree($patient_id,$symptome_id,$rdt_type) {
        RdtSreening::where('patient_id',$patient_id)->where('rdt_type', $rdt_type)->update(array('is_active' => 0));
        return RdtSreening::create([
            'patient_id' => $patient_id,
            'symptome_id' => $symptome_id,  
            'rdt_type'    => $rdt_type,
            'is_active'   => true
        ]);
    }
    
    public function started(Request $request) {
        RdtSreening::where('patient_id', $request['patient_id'])
                    ->where('rdt_type', $request['rdt_type'])
                    ->where('is_active', 1)
                    ->update([
                        'rdt_started' => "1",
                        'rdt_start_at' => Carbon::now()
                    ]);
        return $this->successResponse("Test effectué avec succès");
    }

    public function available(Request $request) {
        RdtSreening::where('patient_id', $request['patient_id'])
                    ->where('rdt_type', $request['rdt_type'])
                    ->where('is_active', 1)
                    ->update([
                        'rdt_result_available' => "1",
                        'result_available_at' => Carbon::now()
                    ]);
        return $this->successResponse("Resultat disponible");
    }

}
