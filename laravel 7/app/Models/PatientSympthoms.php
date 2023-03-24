<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PatientSympthoms extends Model
{
    // use HasFactory;
    protected $table = 'patient_sympthoms';
    protected $guarded = [];

    static function is_active($patient_id) {
        return self::where('patient_id', $patient_id)->update(array("is_active" => 0) );
    }

    public function rdts() {
        return $this->hasMany(RdtSreening::class,'symptome_id');
    }
}
