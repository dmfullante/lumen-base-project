<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $table ='sequences';
    protected $fillable = [
        'sequence_name',
        'sequence_value'
    ];
}
