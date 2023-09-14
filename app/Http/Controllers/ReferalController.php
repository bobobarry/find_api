<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferalsRequest;
use App\Models\PatientDemographic;
use App\Models\PatientReferalsOk;
use App\Models\Referals;
use App\Traits\TraitMakeReferals;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Reference\Reference;

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

    public function statsReferal() {

        $referalType = ['glucose','malnutrition','bloodPressure','malaria', 'covid'];
        $genders = ['Male','Female'];
        $results = array();
        $resultsG = [];
        $total = 0;
        $counter = 0;

        for($j=0; $j < count($genders); $j++) {
            $results[] =  (object) ['label' => $genders[$j],'children' => []] ;
                for($i=0; $i < count($referalType); $i++) {
                    $counter =  Referals::join('patient_demographic', 'patient_demographic.id', '=','patient_referals.patient_id')
                                    ->where('is_active', true)
                                    ->where('referal_type', $referalType[$i])
                                    ->where('gender', $genders[$j])
                                    ->count();
                                    $total = $total + $counter;
                                    $results[$j]->label = $genders[$j].' : '.$total;
                    $results[$j]->children[] = (object) ['label' => $referalType[$i] . ' : ' . $counter] ; 
                }
                $total = 0;
                
        }
        for($b=0; $b < count($results); $b++) {
            $resultsG[] = array($results[$b]);
        }
        return $this->successResponse(array('tree' => $resultsG, 'total' => $total));
    }

    public function findstatsReferal(Request $request) {

        $referalType = ['glucose','malnutrition','bloodPressure','malaria', 'covid'];
        $genders = ['Male','Female'];
        $results = array();
        $resultsG = [];
        $total = 0;
        $counter = 0;

        for($j=0; $j < count($genders); $j++) {
            $results[] =  (object) ['label' => $genders[$j],'children' => []] ;
                for($i=0; $i < count($referalType); $i++) {
                    $counter =  Referals::when($request->has('dateRange'), function ($q) use ($request)  {
                                                $q->where('patient_referals.created_at','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d 00:00:00'))
                                                    ->where('patient_referals.created_at','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d 23:59:59'));
                                    })
                                    ->join('patient_demographic', 'patient_demographic.id', '=','patient_referals.patient_id')
                                    ->where('is_active', true)
                                    ->where('referal_type', $referalType[$i])
                                    ->where('gender', $genders[$j])
                                    ->count();
                                    $total = $total + $counter;
                                    $results[$j]->label = $genders[$j].' : '.$total;
                    $results[$j]->children[] = (object) ['label' => $referalType[$i] . ' : ' . $counter] ; 
                }
                $total = 0;
                
        }
        for($b=0; $b < count($results); $b++) {
            $resultsG[] = array($results[$b]);
        }
        return $this->successResponse(array('tree' => $resultsG, 'total' => $total));
    }

    public function patientFollowedUp() {
        $patientRefer = PatientDemographic::with(['referal','follow'])
                                            ->withCount(['referal'])
                                            ->has('referal', '>', 0)
                                            ->orderBy('id', 'DESC')
                                            ->select('id','patient_uid','name','phone_number','town','quartier')
                                            ->get();

        return $this->successResponse($patientRefer);
    }
    
    public function referalDataSheets()
    {
        $patientRefer = Referals::all();

        return $patientRefer;
    }

    public function referalConfirmSheets()
    {
        $patientRefer = PatientReferalsOk::all();

        return $patientRefer;
    }
}
