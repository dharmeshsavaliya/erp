<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the inventory in the ERP';

    /**
     * Create a new command instance.
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
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // Set empty array with SKU
            $arrInventory = [];

            // Update all products in database to inventory = 0
            //Product::where('id', '>', 0)->update(['stock' => 0]);

            // Get all scraped products with stock
            $sqlScrapedProductsInStock = "
            SELECT
                sp.sku,
                COUNT(sp.id) AS cnt
            FROM suppliers s
            JOIN scrapers sc on sc.supplier_id = s.id
            JOIN scraped_products sp ON sp.website=sc.scraper_name
            WHERE
                s.supplier_status_id=1 AND
                sp.last_inventory_at < DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
            GROUP BY
                sp.sku";

            $scrapedProducts = DB::select($sqlScrapedProductsInStock);

            // Loop over scraped products
            foreach ($scrapedProducts as $scrapedProduct) {
                $arrInventory[$scrapedProduct->sku] = $scrapedProduct->cnt;
            }

            foreach ($arrInventory as $sku => $cnt) {
                // Find product
                Product::where('sku', $sku)->update(['stock' => 0]);
                echo "Updated " . $sku . "\n";
            }

            // TODO: Update stock in Magento
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
