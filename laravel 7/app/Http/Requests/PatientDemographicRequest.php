<?php

namespace App\Http\Requests;

class PatientDemographicRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date_of_registration' => [
                'required',
                'date_format:Y-m-d H:i:s',
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'gender' => [
                'required',
                'string',
                'max:255'
            ],
            'pregnant' => [
                'nullable',
            ],
            'do_you_know_date_of_birth' => [
                'required',
                'string',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],

            'town' => [
                'required',
                'string'
            ],
            'quartier' => [
                'required',
                'string',
            ],
            'sector' => [
                'required',
                'string',
                'max:255'
            ],
            'do_you_have_access_to_telephone' => [
                'required',
            ],
            'phone_number' => [
                'nullable',
            ],
            'phone_type' => [
                'nullable',
            ],
            "daily_expenditure"=> [
                'required',
                'string'
            ],
            'level_of_education' => [
                'required',
                'string',
            ],
            'profession' => [
                'required',
                'string',
                'max:255'
            ],
            'matrimonial_status' => [
                'required',
                'string',
                'max:255'
            ],
            'access_to_drinking_water' => [
                'required',
                'string',
                'max:255'
            ],
            'access_to_toilet' => [
                'required',
                'string',
                'max:255'
            ],
            'rubbish_collection_services' => [
                'required',
                'string',
                'max:255'
            ],

            'time_to_nearest_health_facility' => [
                'required',
                'string',
                'max:255'
            ],
            'last_visit_to_doctor' => [
                'required',
                'string',
                'max:255'
            ],
            'hmd_visits_in_last_year' => [
                'required',
                'string',
                'max:255'
            ],
            'would_you_be_willing_to_subscribe' => [
                'required',
                'string',
            ],
            'would_you_like_medical_card' => [
                'required',
                'string',
            ],
            'testing_services_and_medical_for_free' => [
                'required',
                'string',
            ],
        ];
    }
}
