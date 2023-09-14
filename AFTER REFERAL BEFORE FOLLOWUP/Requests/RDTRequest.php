<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RDTRequest extends APIRequest
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
            'rdt_type' => [
                'required',
                'string',
            ],
            'rdt_result' => [
                'required',
                'string',
            ],
            'rdt_image' => 'required|image|mimes:jpeg,png,jpg|
            max:500000'
        ];
    }
}
