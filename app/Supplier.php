<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{

    use SoftDeletes;

    protected $casts = [
        'notes' => 'array'
    ];

    protected $fillable = [
        'is_updated', 'supplier', 'address', 'phone', 'default_phone', 'whatsapp_number', 'email', 'default_email', 'social_handle', 'instagram_handle', 'website', 'gst', 'status','supplier_category_id','supplier_status_id','is_blocked'
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
        self::saving(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
        self::creating(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
    }

    public function agents()
    {
        return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Supplier');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_suppliers', 'supplier_id', 'product_id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }

    public function emails()
    {
        return $this->hasMany('App\Email', 'model_id')->where(function($query) {
            $query->where('model_type', 'App\Purchase')->orWhere('model_type', 'App\Supplier');
        });
    }

    public function whatsapps()
    {
        return $this->hasMany('App\ChatMessage', 'supplier_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }

    public function category()
    {
        //return $this->belongsToMany('App\SupplierCategory', 'supplier_category', 'supplier_category_id', 'id');
        return $this->hasMany('App\SupplierCategory');
    }

    public function status()
    {
        return $this->belongsToMany('App\SupplierStatus', 'supplier_status', 'supplier_status_id', 'id');
        //return $this->hasMany('App\SupplierStatus');
    }

    public function whatsappAll()
    {
        return $this->hasMany('App\ChatMessage', 'supplier_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }

    public function whoDid()
    {
        return $this->hasOne('App\User',"id","updated_by");
    }

    public function scraperMadeBy()
    {
        return $this->hasOne('App\User',"id","scraper_madeby");
    }

    public function scraperParent()
    {
        return $this->hasOne('App\Supplier',"id","scraper_parent_id");
    }

    public function scraper()
    {
        return $this->hasOne('App\Scraper',"supplier_id","id");
    }

}