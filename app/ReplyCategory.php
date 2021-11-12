<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class ReplyCategory extends Model
{

    public $fillable = ['name', 'parent_id', 'pushed_to_watson', 'dialog_id', 'intent_id'];

    public function approval_leads()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Approval Lead')->orderby('reply');
    }

    public function internal_leads()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Internal Lead');
    }

    public function approval_orders()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Approval Order');
    }

    public function internal_orders()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Internal Order');
    }

    public function approval_purchases()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Approval Purchase');
    }

    public function internal_purchases()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Internal Purchase');
    }

    public function product_dispatch()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Product Dispatch');
    }

    public function vendor()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Vendor');
    }

    public function supplier()
    {
        return $this->hasMany('App\Reply', 'category_id')->where('model', 'Supplier');
    }

    public function parent()
    {
        return $this->hasOne('App\ReplyCategory', 'id', 'parent_id');
    }

    public function parentList()
    {
        $parent = $this->parent;
        $arr    = [];
        if ($parent) {
            $arr[]  = $parent->name;
            $parent = $parent->parent;
            if ($parent) {
                $arr[]  = $parent->name;
                $parent = $parent->parent;
                if ($parent) {
                    $arr[]  = $parent->name;
                    $parent = $parent->parent;
                }
            }
        }

        return implode(" > ", array_reverse($arr));
    }



}
