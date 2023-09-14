<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PatientDemographic extends Model
{

    // use HasFactory;

    protected $table = 'patient_demographic';
    protected $guarded = [];

    public function rdts() {
        return $this->hasMany(RdtSreening::class,'patient_id')->where('is_active','=', 1)->orderBy('id', 'DESC');
    }

    public function vitals() {
        return $this->hasMany(VitalParameter::class,'patient_id')->where('is_active','=', 1)->orderBy('id', 'DESC');
    }
        public function oxygens(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'oxygen')->orderBy('id', 'DESC');
    }
    public function malnutritions(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'malnutrition')->orderBy('id', 'DESC');
    }
    public function temperatures(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'temperature')->orderBy('id', 'DESC');
    }
    public function temperature(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'temperature')->where('is_active', '=' ,true)->orderBy('id', 'DESC');
    }
    public function oxygen(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'oxygen')->where('is_active', '=' ,true)->orderBy('id', 'DESC');
    }
    public function glucoses(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'glucose')->orderBy('id', 'DESC');
    }
    public function bloodPressures(){
        return $this->hasMany(VitalParameter::class,'patient_id')->where('vital_type', '=' ,'bloodPressure')->orderBy('id', 'DESC');
    }
    public function malarias() {
        return $this->hasMany(RdtSreening::class,'patient_id')->where('rdt_type','=', 'malaria')->orderBy('id', 'DESC');
    }
    public function covids() {
        return $this->hasMany(RdtSreening::class,'patient_id')->where('rdt_type','=', 'covid')->orderBy('id', 'DESC');
    }
    public function user() {
        return $this->belongsTo(User::class);
    }


}
