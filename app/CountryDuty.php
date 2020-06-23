<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryDuty extends Model
{
    protected $fillable = [
        'hs_code',
        'origin',
        'destination',
        'currency',
        'price',
        'duty',
        'vat',
        'duty_percentage',
        'vat_percentage',
        'created_at',
        'updated_at'
    ];
}
