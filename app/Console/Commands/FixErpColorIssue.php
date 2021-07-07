<?php

namespace App\Console\Commands;

use app\ColorNamesReference;
use App\Helpers\StatusHelper;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class FixErpColorIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-erp-color-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Erp color issue';

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
        //
        $products = Product::join("scraped_products as sp", "sp.product_id", "products.id")->where("products.status_id", StatusHelper::$unknownColor)->where("products.supplier_id", ">", 0)
            ->where(function ($q) {
                $q->where("sp.color", "!=", "")->where("sp.color", "!=", "0");
            })->where(function ($q) {
            $q->orWhereNull("products.color")->orWhere("products.color", "=", "");
        })->select("products.*")->get();   

        if (!$products->isEmpty()) {
            foreach ($products as $product) {
                $this->info("Started for product id :" . $product->id);
                $scrapedProduct = ScrapedProducts::where("product_id", $product->id)->where(function ($q) {
                    $q->orWhereNotNull("color")->orWhere("color", "!=", "");
                })->first();
                if ($scrapedProduct) {
                    $this->info("Started for product id :" . $product->id. " and find the scraped product");

                    $color = ColorNamesReference::getColorRequest(
                        $scrapedProduct->color,
                        $scrapedProduct->url,
                        $scrapedProduct->title,
                        $scrapedProduct->description
                    );

                    $this->info("Started for product id :" . $product->id. " and find the color =>".$color);

                    if ($color) {
                        // check for the auto crop
                        $product->color    = $color;
                        $needToCheckStatus = [
                            StatusHelper::$requestForExternalScraper,
                            StatusHelper::$unknownComposition,
                            StatusHelper::$unknownColor,
                            StatusHelper::$unknownCategory,
                            StatusHelper::$unknownMeasurement,
                            StatusHelper::$unknownSize,
                        ];

                        if (!in_array($product->status_id, $needToCheckStatus)) {
                            $product->status_id = StatusHelper::$autoCrop;
                        }
                        $product->save();
                        $product->checkExternalScraperNeed();
                    } else {
                        $product->status_id = StatusHelper::$unknownColor;
                        $product->save();
                    }
                } else {
                    $product->status_id = StatusHelper::$unknownColor;
                    $product->save();
                }
            }
        }

    }
}
