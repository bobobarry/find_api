<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\RequiredIf;

class ConsultationRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date_of_consult' => [
                'required',
                'date',
            ],

            'location_of_consult' => [
                'required',
                'string',
            ],
            'type_consult' => [
                'required',
                'string',
            ],
            'people_attending' => [
                'required',
                'integer',
            ],
            'home_attending' => [
                new RequiredIf($this->type_consult == 'Home visit'),
                'nullable',
            ],
            'participants' => [
                'required',
                'string',
            ],
            'ahead' => [
                'required',
                'string',
            ],
            
        ];
    }
}
