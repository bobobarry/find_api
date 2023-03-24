<?php

namespace App\Http\Requests;

use App\Models\PatientDemographic;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class MBRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $patient = PatientDemographic::find($this->patient_id);
        $age = Carbon::parse($patient->date_of_birth)->age;
        return [
            'patient_id' => [
                'required',
                'integer',
                'exists:patient_demographic,id',
            ],
            // background 1
            'con_tabacco' => [
                'nullable',
                new RequiredIf($age > 18 ),
                'string',
            ],
            'con_alcohol' => [
                 'nullable',
                new RequiredIf($age > 18  ),
                'string',
            ],

            // background 2 under 18

            'which_vaccination' => [
                new RequiredIf($age < 18  ),
                'string',
            ],
            'do_you_have_any_diagn_cond' => [
                new RequiredIf($age < 18  ),
                'string',
            ],
            'for_diagn_cond' => [
                new RequiredIf($age < 18 and $this->do_you_have_any_diagn_cond == "yes"),
                'string',
            ],
            'where_diagn_cond' => [
                new RequiredIf($age < 18  and $this->do_you_have_any_diagn_cond == "yes"),
                'string',
            ],
            'are_on_treatment_diagn_cond' => [
                new RequiredIf($age < 18  and $this->do_you_have_any_diagn_cond == "yes"),
                'string',
            ],
            'do_you_take_any_vitamins' => [
                new RequiredIf($age < 18  ),
                'string',
            ],
            'vitamins' => [
                new RequiredIf($age < 18 and $this->do_you_take_any_vitamins == "yes" ),
                'string',
            ],
            'is_physical_activity' => [
                new RequiredIf($age < 18),
                'string',
            ],
            
        ];
    }
}
