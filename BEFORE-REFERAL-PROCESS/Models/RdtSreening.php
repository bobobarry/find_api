<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdtSreening extends Model
{

    const COVID = 'covid';
    const MALARIA = 'malaria';

    use HasFactory;
    protected $table = 'rdt_sreening';
    protected $guarded = [];

    public function symptom() {
        return $this->belongsTo(PatientSympthoms::class,'symptome_id');
    }

    public function patient() {
        return $this->belongsTo(PatientDemographic::class,"patient_id");
    }
}
