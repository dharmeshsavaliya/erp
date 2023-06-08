<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AssetsManager extends Model
{
    //use SoftDeletes;
    protected $table = 'assets_manager';

    protected $casts = [
        'notes' => 'array',
    ];

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="capacity",type="string")
     * @SWG\Property(property="asset_type",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="purchase_type",type="string")
     * @SWG\Property(property="payment_cycle",type="string")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="archived",type="string")
     * @SWG\Property(property="password",type="password")
     * @SWG\Property(property="provider_name",type="string")
     * @SWG\Property(property="location",type="string")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="usage",type="string")
     * @SWG\Property(property="due_date",type="datetime")
     */
    protected $fillable = [
        'name', 'capacity', 'asset_type', 'category_id', 'start_date', 'purchase_type', 'payment_cycle', 'amount', 'archived', 'password', 'provider_name', 'location', 'currency', 'usage', 'due_date', 'user_name', 'assigned_to', 'ip', 'ip_name', 'folder_name', 'server_password', 'website_id', 'asset_plate_form_id', 'email_address_id', 'whatsapp_config_id', 'created_by', 'link', 'ip','client_id'];

    public function category()
    {
        return $this->hasOne(AssetsCategory::class, 'id', 'category_id');
    }

    /**
     * Get all of the assetManager's alert logs.
     */
    public function eventAlertLogs()
    {
        return $this->morphMany(EventAlertLog::class, 'eventalertloggable');
    }

    public static function assertTypeList()
    {
        return [
            '' => '-- Assert Type --',
            'Hard' => 'Hard',
            'Soft' => 'Soft',
        ];
    }

    public static function purchaseTypeList()
    {
        return [
            '' => '-- Purchase Type --',
            'Owned' => 'Owned',
            'Rented' => 'Rented',
            'Subscription' => 'Subscription',
        ];
    }

    public static function paymentCycleList()
    {
        return [
            '' => '-- Payment Cycle --',
            'Daily' => 'Daily',
            'Weekly' => 'Weekly',
            'Bi-Weekly' => 'Bi-Weekly',
            'Monthly' => 'Monthly',
            'Yearly' => 'Yearly',
            'One time' => 'One time',
        ];
    }
}
