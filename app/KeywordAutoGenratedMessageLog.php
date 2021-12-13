<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Vendor;
use App\Supplier;
use seo2websites\ErpCustomer\ErpCustomer;

class KeywordAutoGenratedMessageLog extends Model
{
    protected $fillable = ['model','model_id','keyword','keyword_match','message_sent_id','comment'];

    protected $appends = ["typeName"];

    public function getTypeNameAttribute()
    {
        if ($this->model == 'App\Customer') {
            $typeName = @$this->customer->name;
        } elseif ($this->model == 'App\Vendor') {
            $typeName = @$this->vendor->name;
        } else {
            $typeName = @$this->supplier->supplier;
        }
        return $typeName;
    }

    public function customer()
    {
        return $this->belongsTo(ErpCustomer::class, 'model_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'model_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'model_id', 'id');
    }
}
