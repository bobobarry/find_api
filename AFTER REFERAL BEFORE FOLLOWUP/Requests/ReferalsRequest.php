<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class ReferalsRequest extends APIRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => [
                'nullable',
                'integer',
            ],
            'patient_id' => [
                'required',
                'integer',
                'exists:patient_demographic,id',
            ],
            'chc_refered' => [
                new RequiredIf($this->referal_type != 'malaria' and $this->chc_refered != 'bp'),
                'string',
            ],
            'raison_not_refered' => [
                new RequiredIf($this->referal_type != 'malaria' and $this->chc_refered == 'No'),
                'string',
                'nullable',
            ],
            'counselling_completed' => [
                new RequiredIf($this->referal_type != 'malaria'  and $this->chc_refered != 'bp'),
                'string',
            ],
            'patient_intends_to_refer' => [
                new RequiredIf($this->referal_type != 'malaria' and $this->chc_refered != 'bp'),
                'string',
                'max:255'
            ],
            'reason_if_no_attend' => [
                new RequiredIf($this->referal_type != 'malaria' and $this->patient_intends_to_refer == 'No'),
                'string',
                'nullable',
                'max:255'
            ],
            'specialist_name' => [
                new RequiredIf($this->referal_type == 'bloodPressure' and $this->bp_level > 2),
                'string',
                'nullable',
                'max:255'
            ],
            'specialist_number' => [
                new RequiredIf($this->referal_type == 'bloodPressure' and $this->bp_level > 2),
                'string',
                'nullable',
                'max:255'
            ],
            'patient_call_specialist' => [
                new RequiredIf($this->referal_type == 'bloodPressure' and $this->bp_level > 2),
                'string',
                'nullable',
                'max:255'
            ],
            'medication_recomdt' => [
                new RequiredIf($this->referal_type == 'malaria' and $this->chc_refered != 'bp'),
                'string',
                'nullable',
                'max:255'
            ],
            'posology' => [
                new RequiredIf($this->referal_type == 'malaria' and $this->chc_refered != 'bp'),
                'string',
                'nullable',
                'max:255'
            ],
            'received_medic' => [
                new RequiredIf($this->referal_type == 'malaria' and $this->chc_refered != 'bp'),
                'nullable',
                'string',
            ],
            'reason_not_received_medic' => [
                new RequiredIf($this->referal_type == 'malaria' and $this->received_medic == 'No'),
                'nullable',
                'string',
            ],
            'referal_type' => [
                'required',
                'string',
            ],
            // 'is_active' => [
            //     'required',
            //     'integer',
            // ],
        ];
    }
}
