<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProductPushInformation extends Model
{
    protected  $guarded = [];

    use SoftDeletes;

    public static function boot()
    {
        static::updated(function (ProductPushInformation $p) {
            $dirties = $p->getDirty();
            $old_contents = $p->getOriginal();
            $user_id = Auth::id();

            $old_arr = [];
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id','real_product_id','is_available'];
            foreach ($old_contents as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;
                    continue;
                }
                if ($key === 'store_website_id') {
                    $old_arr['store_website_id'] = $oldValue;
                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
            }

            $new_values =  array_merge($old_arr, $dirties);
            $new_values['user_id'] =$user_id  ?? 'command' ;
            // unset($new_values['store_website_id']);
            unset($new_values['real_product_id']);
            unset($new_values['is_available']);
            unset($new_values['id']);
            ProductPushInformationHistory::create($new_values);
        });


        static::created(function (ProductPushInformation $p) {
            $old_arr = [];
            $user_id = Auth::id();
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id','real_product_id','is_available','real_product_id'];
            foreach ($p->toArray() as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;
                    continue;
                }
                if ($key === 'store_website_id') {
                    $old_arr['store_website_id'] = $oldValue;
                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
                $old_arr['user_id'] = $user_id ?? 'command';
            }


            ProductPushInformationHistory::create($old_arr);
            $productSku = explode('-',$p->sku)[0];

            $availableProduct = Product::where('sku',$productSku)->first();

            if($availableProduct){
                $p->real_product_id = $availableProduct->id;
                $p->save();
            }

        });
    }

    public function storeWebsite(){
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    // public function product(){
    //     return Product::with('brands', 'categories')->where('sku', explode('-', $this->sku)[0])->first();
    // }

    public function product()
    {
        return $this->hasOne(Product::class,'id','real_product_id');
    }

    public static function filterProductSku($categories, $brands){
        $sku = [];
        if($categories){
            $categories = Category::whereIn('id', $categories)->get();
            foreach($categories as $cat){
                $products = $cat->products;
                if(count($products)){
                    foreach($products as $pro){
                        if(!empty($pro->sku)){
                            $sku[] = $pro->sku;
                        }
                    }
                }
            }
        }
        if($brands){
            $brands = Brand::with('products')->whereIn('id', $brands)->get();
            foreach($brands as $b){
                $products = $b->products;
                if(count($products)){
                    foreach($products as $pro){
                        if(!empty($pro->sku)){
                            $sku[] = $pro->sku;
                        }
                    }
                }
            }
        }
        return $sku;
    }
}
