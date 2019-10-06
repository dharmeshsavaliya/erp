<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Brand;
use App\Product;
use App\ScrapStatistics;
use App\Helpers\ProductHelper;

class ScrapedProducts extends Model
{
    protected $casts = [
        'images' => 'array',
        'properties' => 'array',
    ];

    protected $fillable = [
        'sku',
        'website',
        'images',
        'properties',
        'title',
        'brand_id',
        'description',
        'url',
        'is_properly_updated',
        'is_price_updated',
        'is_enriched',
        'has_sku',
        'price',
        'can_be_deleted'
    ];

    public function bulkScrapeImport($arrBulkJson = [], $isExcel = 0)
    {
        // Check array
        if (!is_array($arrBulkJson) || count($arrBulkJson) == 0) {
            // return false
            return false;
        }

        // Set count to 0
        $count = 0;

        // Loop over array
        foreach ($arrBulkJson as $json) {
            // Excel?
            if ( $isExcel == 1 ) {
                $json->title = empty($json->title) ? $json->title : ' ';
            }

            // Check for required values
            if (
                !empty($json->title) &&
                !empty($json->sku) &&
                !empty($json->brand_id)
            ) {
                // Set possible alternate SKU
                $sku2 = ProductHelper::getSku($json->sku);

                // Create new scraped product if product doesn't exist
                $scrapedProduct = ScrapedProducts::whereIn('sku', [$json->sku, $sku2])->where('website', $json->website)->first();

                // Get brand name
                $brand = Brand::find($json->brand_id);
                $brandName = $brand->name;

                // Existing product
                if ($scrapedProduct) {
                    // Update scraped product
                    $scrapedProduct->is_excel = $isExcel;
                    $scrapedProduct->properties = $json->properties;
                    $scrapedProduct->original_sku = $json->sku;
                    $scrapedProduct->is_sale = false;
                    $scrapedProduct->properties = $json->properties;
                    $scrapedProduct->description = $json->description;
                    $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
                    $scrapedProduct->save();

                    // Add to scrap statistics
                    $scrapStatistics = new ScrapStatistics();
                    $scrapStatistics->supplier = $json->website;
                    $scrapStatistics->type = 'EXISTING_SCRAP_PRODUCT';
                    $scrapStatistics->brand = $brandName;
                    $scrapStatistics->url = $json->url;
                    $scrapStatistics->description = $json->sku;
                    $scrapStatistics->save();
                    // Create the product
                    $productsCreatorResult = Product::createProductByJson($json, $isExcel);
                } else {
                    // Add new scraped product
                    $scrapedProduct = new ScrapedProducts();
                    $scrapedProduct->brand_id = $json->brand_id;
                    $scrapedProduct->sku = $sku2;
                    $scrapedProduct->original_sku = $json->sku;
                    $scrapedProduct->website = $json->website;
                    $scrapedProduct->title = $json->title;
                    $scrapedProduct->description = $json->description;
                    $scrapedProduct->images = $json->images;
                    $scrapedProduct->price = $json->price;
                    if ($json->sku != 'N/A') {
                        $scrapedProduct->has_sku = 1;
                    }
                    $scrapedProduct->is_price_updated = 1;
                    $scrapedProduct->url = $json->url;
                    $scrapedProduct->is_sale = $json->is_sale;
                    $scrapedProduct->properties = $json->properties;
                    $scrapedProduct->save();

                    // Add to scrap statistics
                    $scrapStatistics = new ScrapStatistics();
                    $scrapStatistics->supplier = $json->website;
                    $scrapStatistics->type = 'NEW_SCRAP_PRODUCT';
                    $scrapStatistics->brand = $brandName;
                    $scrapStatistics->url = $json->url;
                    $scrapStatistics->description = $json->sku;
                    $scrapStatistics->save();

                    // Create the product
                    $productsCreatorResult = Product::createProductByJson($json, $isExcel);
                }

                // Product created successfully
                if ($productsCreatorResult) {
                    // Add or update supplier / inventory
                    SupplierInventory::firstOrCreate(['supplier' => $json->website, 'sku' => $sku2, 'inventory' => $json->stock]);

                    // Update count
                    $count++;
                }
            }
        }

        // Return count
        return $count;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function product()
    {
        return $this->hasOne('App\Product', 'sku', 'sku');
    }
}
