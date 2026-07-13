<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResendInterval extends Model
{
    protected $fillable = [
        'label',
        'minutes',
    ];
}
