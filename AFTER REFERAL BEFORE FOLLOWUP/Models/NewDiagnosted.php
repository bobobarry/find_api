<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewDiagnosted extends Model
{
    use HasFactory;
    protected $table = 'newly_diagnosed';
    protected $guarded = [];
}
