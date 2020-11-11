<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteOrder extends Model
{
    protected $fillable = ['status_id', 'order_id', 'website_id'];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, "id", "website_id");
    }

    public function getOrderConfirmationTemplate()
    {
        $category = \App\MailinglistTemplateCategory::where('title','Order Confirmation')->first();
        if($category) {
            // get the template for that cateogry and store website 
            return \App\MailinglistTemplate::where('store_website_id',$this->website_id)->where('category_id', $category->id)->first();
            
        }

        return false;

    }
}
