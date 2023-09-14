<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientSympthomRequest extends APIRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'patient_id' => [
                'required',
                'integer',
                'exists:patient_demographic,id',
            ],
            'temperature_id' => [
                'required',
                'integer',
                'exists:vital_parameters,id',
            ],
            'oxygen_id' => [
                'required',
                'integer',
                'exists:vital_parameters,id',
            ],
            'chills' => [
                'required',
                'string',
            ],
            'nausea_and_vomiting' => [
                'required',
                'string',
            ],
            'headaches' => [
                'required',
                'string',
            ],

            'muscle_or_join_pain' => [
                'required',
                'string',
            ],
            'sore_throa' => [
                'required',
                'string',
            ],
            'cough' => [
                'required',
                'string',
            ],
            'fatigue' => [
                'required',
                'string',
            ],
            'loss_of_sense_of_smell' => [
                'required',
                'string',
            ],
            'difficulty_breathing' => [
                'required',
                'string',
            ],
            'diarrhoea' => [
                'required',
                'string',
            ]
        ];
    }
}
