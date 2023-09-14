<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalParameter extends Model
{
    use HasFactory;

    const OXYGEN = 'oxygen';
    const TEMPERATURE = 'temperature';
    const GLUCOSE = 'glucose';
    const BLOODPRESSURE = 'bloodPressure';
    const MALNUTRITION   = 'malnutrition';

    const FLAG_NORMAL = '0';
    const FLAG_MID_BAD = '1';
    const FLAG_VER_BAD = '2';
    const FLAG_DANGER = '3';
    
    protected $table='vital_parameters';
    protected $guarded=[];

    static function is_active($patient_id, $vital) {
        return self::where('patient_id', $patient_id)->where('vital_type', $vital)->update(array("is_active" => 0) );
    }

    public function patient() {
        return $this->belongsTo(PatientDemographic::class,"patient_id");
    }

    public function symptom() {
        return $this->belongsTo(PatientSympthoms::class);
    }
}
