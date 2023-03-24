<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MedicalBackground extends Model
{
    // use HasFactory;
    protected $table = 'medical_background';
    protected $guarded = [];

    static function is_active($patient_id) {
        return self::where('patient_id', $patient_id)->update(array("is_active" => 0) );
    }
}
