<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    protected $table ='api_calls';
    protected $fillable = [
        'service_from',
        'service_to',
        'request',
        'response',
        'status',
    ];
}
