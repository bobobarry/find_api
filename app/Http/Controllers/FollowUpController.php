<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowUpRequest;
use App\Models\FollowsUp;
use App\Models\PatientDemographic;
use App\Models\PatientFollowedOk;
use App\Models\Referals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //  $patients = [];
        return FollowsUp::all();
        //  return $mustBeFollowed;
        //  $hasBeenFollowed = $this->hasBeenFollowedIDs();
        //  foreach($mustBeFollowed as $patient) {
        //     if(in_array($patient->patient_id, $hasBeenFollowed)) {
        //        $patient['is_done'] = 1;
        //     }else {
        //         $patient['is_done'] = 0;
        //     }
        //     $patients = $patient;
        //  }

        //  return $patients;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FollowUpRequest $request)
    {
        $followUp = FollowsUp::updateOrCreate(['id' => $request['id']], $request->validated());
        if($followUp->id !== null) {
            if($followUp->patient_attended_chc == 'No') {
                FollowsUp::where('patient_id', $followUp->patient_id)->update(array('is_active' => 0));
            } else {
                FollowsUp::is_active($followUp->patient_id, $followUp->referal_type);
            } 
            $followUp->is_active = true;
            $followUp->followed_by = Auth::user()->name;
            $followUp->save();

            $canBeDone = $this->canBeDone($followUp->patient_id);
            $hasBeenClose = $this->hasBeenClose($followUp->patient_id);
        }
        
        return $this->successResponse([$followUp, $canBeDone, $hasBeenClose] , 'Créée avec succès');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $followUp =  FollowsUp::where('patient_id', $id)->where('is_active', true)->get();
        $canBeDone = $this->canBeDone($id);
        $hasBeenClose = $this->hasBeenClose($id);
        return $this->successResponse([$followUp, $canBeDone, $hasBeenClose] , null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function canBeDone($patient_id) {
        $condReferal = Referals::where('patient_id', $patient_id)->where('is_active', true)->count();
        $followUpData = followsUp::where('patient_id', $patient_id)->where('is_active', true)->get();
        $followUpDataCounter = count($followUpData);
        if($followUpDataCounter > 0) {
            foreach($followUpData as $follow) {
                if($follow->patient_attended_chc == "No") {
                    return 1;
                } else if($followUpDataCounter == $condReferal) {
                    return 1;
                } else {
                    return  0;
                }
            }
        } else {
            return 0;
        }

    }

    public function closeFollowing(Request $request) {
        $followedUpOK = PatientFollowedOk::create($request->all());
            if($followedUpOK->id !== null) {
                PatientFollowedOk::is_active($followedUpOK->patient_id);
                $followedUpOK->is_active = 1;
                $followedUpOK->follow_up_by = Auth::user()->name;
                $followedUpOK->save();
                $patient = PatientDemographic::find($followedUpOK->patient_id);
        }
        return $this->successResponse($patient, "patient_followed_submit_msg");
    }

    public function hasBeenFollowedIDs() {
        $data = PatientFollowedOk::where('is_active', true)->select('patient_id')->get();
        $ids = [];
        foreach($data as $ele) {
            $ids[] = $ele->patient_id;
        }

        return $ids;
    }

    public function hasBeenClose($patient_id) {
        $hasBeenFollowed = $this->hasBeenFollowedIDs();
        if(!is_null($hasBeenFollowed)) {
            if(in_array($patient_id, $hasBeenFollowed)) {
                return  1;
             }else {
                return 0;
             }
        } else {
            return 0;
        }
    }
    
    public function followUpDataSheets()
    {
        $patientFollow = FollowsUp::all();

        return $patientFollow;
    }

    public function followConfirmSheets()
    {
        $patientConfirmFollow = PatientFollowedOk::all();

        return $patientConfirmFollow;
    }
}
