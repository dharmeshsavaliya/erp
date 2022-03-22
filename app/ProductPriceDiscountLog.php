<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPriceDiscountLog extends Model
{
       /**
     * @var string
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="stage",type="string")
     * @SWG\Property(property="log",type="longText")
     * @SWG\Property(property="created_by",type="datetime")
     * @SWG\Property(property="product_id",type="interger")
     */
	public $table  = "product_price_discount_logs";
    protected $fillable = ['id', 'order_id','product_id','stage', 'log', 'created_at', 'updated_at'];

    public function product()
    {
    	return $this->hasOne("\App\Products","id","product_id");
    }

}
