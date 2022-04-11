<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleCategory extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'category_name',
        'status'
    ];
}
