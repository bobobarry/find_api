<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferalsRequest;
use App\Models\PatientDemographic;
use App\Models\PatientReferalsOk;
use App\Models\Referals;
use App\Traits\TraitMakeReferals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferalController extends Controller
{
    use TraitMakeReferals;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patientRefer = PatientDemographic::with(['referal','referals'])
                                            ->withCount(['referals'])
                                            ->has('referals', '>', 0)
                                            ->orderBy('id', 'DESC')
                                            ->get('id','patient_uid','name','phone_number','quartier');

        return $this->successResponse($patientRefer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReferalsRequest $request)
    {
        
        $validatedData = $request->validated();
        
        $query = Referals::where('patient_id', $validatedData['patient_id'])
            ->where('referal_type', $validatedData['referal_type']);
        
        if($validatedData['referal_type'] == 'malaria') {
            $query->update([
                'medication_recomdt' => $validatedData['medication_recomdt'] ,
                'received_medic' =>   $validatedData['received_medic'] ,
                'posology' =>  $validatedData['posology'] ,
                'reason_not_received_medic' => $validatedData['reason_not_received_medic'] ,
            
                'refered_by' => Auth::user()->name,
                
            ]);
        } 
        if($validatedData['referal_type'] == 'glucose' or $validatedData['referal_type'] == 'malnutrition' or $validatedData['referal_type'] == 'covid' or  $validatedData['referal_type'] == 'bloodPressure' ) {
            
            if($validatedData['chc_refered'] != 'bp') {
                $query->update([
                    'counselling_completed' => $validatedData['counselling_completed'] ,
                    'chc_refered' => $validatedData['chc_refered'] ,
                    'raison_not_refered' => $validatedData['raison_not_refered'] ,
                    'patient_intends_to_refer' => $validatedData['patient_intends_to_refer'] ,
                    'reason_if_no_attend' => $validatedData['patient_intends_to_refer'] == 'No' ? $validatedData['reason_if_no_attend'] : NULL,
                
                    'refered_by' => Auth::user()->name,
                    
                ]);
            } else {
                $query->update([
                    'specialist_name' => $validatedData['specialist_name'] ,
                    'specialist_number' =>  $validatedData['specialist_number'] ,
                    'patient_call_specialist' => $validatedData['patient_call_specialist'] ,
                
                    'refered_by' => Auth::user()->name,
                    
                ]);
            }
            
        } 
            
        return $this->successResponse($validatedData, "Referal créée avec succès");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Referals $referal)
    {
        return $this->successResponse($referal);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReferalsRequest $request, Referals $referal)
    {
        $referal->update($request->validated());
        return $this->successResponse($referal, "Referal modifiée avec succès");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function globalReferral(Request $request) {
        $globalRefer = PatientReferalsOk::create($request->all());
        if($globalRefer->id !== null) {
            PatientReferalsOk::is_active($globalRefer->patient_id);
            $globalRefer->is_active = 1;
            $globalRefer->referal_by = Auth::user()->name;
            $globalRefer->save();
            $patient = PatientDemographic::find($globalRefer->patient_id);
        }
        return $this->successResponse($patient, "patient_refer_submit_msg");
    }


    public function rsheets() {
        
        $patientRefer = PatientDemographic::with(['referal','referals'])
                                            ->withCount(['referals'])
                                            ->has('referals', '>', 0)
                                            ->orderBy('id', 'DESC')
                                            ->get('id','patient_uid','name','phone_number','quartier');

        return $this->successResponse($patientRefer);
    }
}
