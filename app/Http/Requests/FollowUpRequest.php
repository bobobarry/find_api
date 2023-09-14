<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class FollowUpRequest extends APIRequest
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
            'patient_attended_chc' => [
                'required',
                'string',
            ],
            'referal_type' => [
                new RequiredIf($this->patient_attended_chc == 'Yes'),
                'nullable',
            ],
            'why_did_not_attd' => [
                new RequiredIf($this->patient_attended_chc == 'No'),
                'nullable',
                'max:255'
            ],
            'patient_receiv_care_chc' => [
                new RequiredIf($this->patient_attended_chc == 'Yes'),
                'nullable',
                'string',
            ],
            'why_didnot_receiv_care_chc' => [
                new RequiredIf($this->patient_receiv_care_chc == 'No'),
                'nullable',
                'max:255'
            ],
            
        ];
    }
}
