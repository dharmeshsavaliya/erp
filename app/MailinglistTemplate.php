<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    protected $fillable = ['name', 'mail_class', 'mail_tpl', 'image_count', 'text_count', 'example_image', 'subject', 'static_template', 'category_id', 'store_website_id'];

    public function file()
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

    public function category()
    {
        return $this->hasOne(MailinglistTemplateCategory::class, 'id', 'category_id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public static function getIssueCredit($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Issue Credit')->first();

        if ($category) {
            return self::getTemplate($category, $store);

        }

        return false;
    }

    public static function getOrderConfirmationTemplate($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Confirmation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getOrderStatusChangeTemplate($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Status Change')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getOrderCancellationTemplate($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Cancellation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeReturn($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Return')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeRefund($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Refund')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getIntializeExchange($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Exchange')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getNewsletterTemplate($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Newsletter')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public static function getTemplate($category, $store = null)
    {
        if ($store) {
            return self::where('store_website_id', $store)->where('category_id', $category->id)->first();
        } else {
            return self::where(function($q) {
                $q->whereNull('store_website_id')->orWhere('store_website_id','=',"")->orWhere('store_website_id',"<=",0);
            })->where('category_id', $category->id)->first();
        }
    }

}
