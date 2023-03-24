<?php

namespace App\Http\Requests;

use App\Models\VitalParameter;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class VitalParameterRequest extends ApiRequest
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
            'vital_type' => [
                'required',
                'string',
            ],

            'oxygen_saturation' => [
                new RequiredIf($this->vital_type == VitalParameter::OXYGEN),
                'string',
            ],
            'temperature' => [
                new RequiredIf($this->vital_type == VitalParameter::TEMPERATURE),
                'string',
                'max:255'
            ],
            'glucose_level' => [
                new RequiredIf($this->vital_type == VitalParameter::GLUCOSE),
                'string',
                'max:255'
            ],
            'bp_sys_right' => [
                new RequiredIf($this->vital_type == VitalParameter::BLOODPRESSURE),
                'string',
                'max:255'
            ],
            'bp_dias_right' => [
                new RequiredIf($this->vital_type == VitalParameter::BLOODPRESSURE),
                'string',
                'max:255'
            ],
            'bp_sys_left' => [
                new RequiredIf($this->vital_type == VitalParameter::BLOODPRESSURE),
                'string',
                'max:255'
            ],
            'bp_dias_left' => [
                new RequiredIf($this->vital_type == VitalParameter::BLOODPRESSURE),
                'string',
            ],
            'arm_circumference' => [
                new RequiredIf($this->vital_type == VitalParameter::MALNUTRITION),
                'numeric',
            ],
        ];
    }
}
