<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Vendor;
use App\VendorCategory;
use App\CustomerCharity;
use App\Category;
use App\Brand;
use App\Product;

class UpdateCharities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateCharities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendor_category = VendorCategory::where('title', 'charity')->first();
        if($vendor_category){
            $vendors = Vendor::where('category_id', $vendor_category->id)->get()->toArray();
            foreach($vendors as $v){
                $customer_charity = CustomerCharity::where('name', $v['name'])->first();
                unset($v['id']);
                if($customer_charity){
                    continue;
                }
                $charity = CustomerCharity::create($v); 
                $charity_category = Category::where('title', 'charity')->first();
                $charity_brand = Brand::where('name', 'charity')->first();
                $product = new Product();
                $product->sku = '';
                $product->name = $charity->name;
                $product->brand = $charity_brand->id;
                $product->category = $charity_category->id;
                $product->save();
                CustomerCharity::where('id', $charity->id)->update(['product_id' => $product->id]);
            }
        }else{
            dump('charity category not exist!');
        }
    }
}