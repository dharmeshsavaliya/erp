<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushBrandsLog extends Model
{
    protected $table = "push_brands_logs";
    protected $fillable = [
        'store_webite_id', 
        'user_id', 
        'error_type',
        'error',
        'created_at'
	];
}
