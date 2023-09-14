<?php


namespace App\Traits;

use App\Models\PatientReferalsOk;
use App\Models\Referals;
use Illuminate\Support\Facades\Auth;

trait TraitMakeReferals
{
    public function createReferals($condition_id, $patient_id,$referal_type,$systolAverage ='', $diastolAverage = '') {
        $this->disabledReferals($patient_id,$referal_type);
        Referals::where('patient_id', $patient_id)->where('condition_id',$condition_id)->where('referal_type',$referal_type)->delete();
        $referalData = array(
                                'condition_id' => $condition_id,
                                'diagnosted_by' => Auth::user()->id ,
                                'patient_id' => $patient_id,
                                'referal_type' =>$referal_type, 'is_active' => 1);
        if($referal_type == 'bloodPressure') {
            if(($systolAverage > 140 or $systolAverage < 159) and ($diastolAverage > 90 or $diastolAverage < 99)) {
                $referalData['bp_level'] = 1;
            }
            if(($systolAverage > 160 or $systolAverage < 179) and ($diastolAverage > 100 or $diastolAverage < 109)) {
                $referalData['bp_level'] = 2;
            }
            if(($systolAverage >= 180) and ($diastolAverage >= 110)) {
                $referalData['bp_level'] = 3;
            }
        }
        Referals::create($referalData);
    }

    public function canceledReferal($condition_id, $patient_id,$referal_type) {
        Referals::where('patient_id', $patient_id)
                            ->where('condition_id',$condition_id)
                            ->where('referal_type',$referal_type)
                            ->delete();
    }

    public function disabledReferals($patient_id,$referal_type) {
        Referals::where('patient_id', $patient_id)
                    //->where('condition_id',$condition_id)
                    ->where('referal_type',$referal_type)
                    ->update(array('is_active' => 0));
    }

    public function getPatientReferals($patient_id) {
        return Referals::where('patient_id', $patient_id)
                    ->where('is_active', 1)
                    ->get();
    }

    public function getPatientReferalsOK($patient_id) {
        return PatientReferalsOk::where('patient_id', $patient_id)
                    ->where('is_active', 1)
                    ->get();
    }
}