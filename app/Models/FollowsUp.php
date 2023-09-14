<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowsUp extends Model
{
    //use HasFactory;

    protected $table = 'followUpData';
    protected $guarded = [];

    static function is_active($patient_id, $referalType) {
        return self::where('referal_type', $referalType)->where('patient_id', $patient_id)->update(array("is_active" => 0) );
    }
}
