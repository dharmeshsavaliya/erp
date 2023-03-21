<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAdsAccount extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="googleadsaccounts",type="string")
     * @SWG\Property(property="account_name",type="string")
     * @SWG\Property(property="store_websites",type="string")
     * @SWG\Property(property="config_file_path",type="string")
     * @SWG\Property(property="notes",type="string")
     * @SWG\Property(property="status",type="string")
     */
    protected $table = 'googleadsaccounts';

    protected $fillable = ['google_customer_id', 'account_name', 'store_websites', 'config_file_path', 'notes', 'status'];

    public function campaigns(): HasMany
    {
        return $this->hasMany(GoogleAdsCampaign::class,'account_id');
    }
}
