<?php

namespace App\Http\Requests;

class ConsultationRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

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
                'required',
                'date',
            ],
            'created_by' => [
                'required',
                'integer',
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
