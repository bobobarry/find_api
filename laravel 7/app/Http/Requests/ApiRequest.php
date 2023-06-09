<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * If validator fails, return the exception in json form
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw  new HttpResponseException(
            response([
                "error" => true,
                'status' => 'warning',
                "message" => $validator->errors()->first(),
                "errors" => $validator->errors()
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();
}
