<?php

namespace App\Jobs;

use App\Library\Magento\MagentoService;
use App\Product;
use App\ProductPushErrorLog;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImageApprovalMagentoServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    protected $log;

    protected $mode;

    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     * @param  StoreWebsite  $website
     * @param  null  $log
     * @param  null  $mode
     */
    public function __construct(Product $product, StoreWebsite $website, $log = null, $mode = null)
    {
        // Set product and website
        $this->_product = $product;
        $this->_website = $website;
        $this->log = $log;
        $this->mode = $mode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Set time limit
        set_time_limit(0);
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log, $this->mode);
        $magentoService->pushProduct();
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        $website = $this->_website;

        $error_msg = 'ImageApprovalMagentoServiceJob failed for '.$product->name;
        if ($this->log) {
            $this->log->sync_status = 'error';
            $this->log->message = $error_msg;
            $this->log->save();
        }
        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $this->log->id);
    }
    
    public function tags()
    {
        return ['product_'.$this->_product->id];
    }
}
