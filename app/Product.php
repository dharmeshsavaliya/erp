<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{

//	use LogsActivity;
	use Mediable;
	use SoftDeletes;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'sku'
	];
	protected $dates = ['deleted_at'];
	protected $appends = ['communication', 'imageurl'];
	protected $communication = '';
	protected $image_url = '';

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'product')->latest()->first();
	}

	public function getCommunicationAttribute()
	{
		return $this->messages();
	}

	public function getImageurlAttribute()
	{
		return $this->getMedia(config('constants.media_tags'))->first() ? $this->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
	}

//	protected static $logName = 'Product';
//	protected static $logAttributes = ['sku'];

	public function notifications(){
		return $this->hasMany('App\Notification');
	}

	public function suppliers()
	{
		return $this->belongsToMany('App\Supplier', 'product_suppliers', 'product_id', 'supplier_id');
	}


	public function brands(){
		return $this->hasOne('App\Brand','id','brand');
	}

	public static function getPendingProductsCount($roleType){

		$stage = new Stage();
		$stage_no = intval($stage->getID($roleType));

		return DB::table('products')
		         ->where('stage',$stage_no-1)
				 ->where('isApproved','!=',-1)
				 ->whereNull('dnf')
		         ->whereNull('deleted_at')
		         ->count();
	}

	public function purchases()
  {
    return $this->belongsToMany('App\Purchase', 'purchase_products', 'product_id', 'purchase_id');
  }

	public function orderproducts()
  {
    return $this->hasMany('App\OrderProduct', 'sku', 'sku');
  }

	public function scraped_products()
	{
		return $this->hasOne('App\ScrapedProducts', 'sku', 'sku');
	}
}
