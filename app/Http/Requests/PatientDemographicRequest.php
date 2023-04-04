<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Rules\RequiredIf;

class PatientDemographicRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $age = Carbon::parse($this->date_of_birth)->age;
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
                new RequiredIf($age > 18),
                'nullable'
            ],
            'level_of_education' => [
                'required',
                'string',
            ],
            'profession' => [
                new RequiredIf($age > 18),
                'nullable',
                'max:255'
            ],
            'matrimonial_status' => [
                new RequiredIf($age > 18),
                'nullable',
                'max:255'
            ],
            'type_of_consultation' => [
                'required',
                'string',
                'max:255'
            ],
            'access_to_drinking_water' => [
                'required',
                'string',
                'max:255'
            ],
            'access_to_toilet' =>[
                new RequiredIf($age > 18),
                'nullable',
                'max:255'
            ],
            'rubbish_collection_services' => [
                new RequiredIf($age > 18),
                'nullable',
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
                new RequiredIf($age > 18),
                'nullable',
            ],
            'would_you_like_medical_card' => [
                new RequiredIf($age > 18),
                'nullable',
            ],
            'testing_services_and_medical_for_free' => [
                'required',
                'nullable',
            ],
        ];
    }
}