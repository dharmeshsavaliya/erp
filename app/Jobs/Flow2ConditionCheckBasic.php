<?php

namespace App\Jobs;

use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\ProductPushErrorLog;
use App\ProductPushJourney;
use App\PushToMagentoCondition;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Flow2ConditionCheckBasic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    protected $log;

    protected $mode;
    
    protected $details;
    
    protected $product_index;
    
    protected $no_of_product;

    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     * @param  StoreWebsite  $website
     * @param  null  $log
     * @param  null  $mode
     */
    public function __construct(Product $product, StoreWebsite $website, $log = null, $mode = null,$details = [])
    {
        // Set product and website
        $this->_product = $product;
        $this->_website = $website;
        $this->log = $log;
        $this->mode = $mode;
        $this->details = $details;
        $this->product_index = (isset($details) && isset($details['product_index'])) ? $details['product_index']: 0;
        $this->no_of_product = (isset($details) && isset($details['no_of_product'])) ? $details['no_of_product']: 0;
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

        $date_time = date('Y-m-d H:i:s');
        // Load product and website
        if ($this->log) {
            $this->log->sync_status = 'first_job_started';
            $this->log->message = 'Condition Check First job started';
            $this->log->job_start_time = $date_time;
            $this->log->save();
        }
        $product = $this->_product;
        $website = $this->_website;
        $conditionsWithIds = PushToMagentoCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $upteamconditionsWithIds = PushToMagentoCondition::where('upteam_status', 1)->pluck('id', 'condition')->toArray();
        $upteamconditions = array_keys($upteamconditionsWithIds);
        $categorym = $product->categories;
        $topParent = ProductHelper::getTopParent($categorym->id);

        $charity = 0;
        $isCharityChecked = 0;
        if (($topParent == 'NEW' && in_array('charity_condition', $conditions)) || ($topParent == 'PREOWNED' && in_array('charity_condition', $upteamconditions))) {
            $isCharityChecked = 1;
            $p = \App\CustomerCharity::where('product_id', $product->id)->first();
            if ($p) {
                $charity = 1;
            }
        }
        try {
            if ((in_array('status_condition', $conditions) && $topParent == 'NEW') || ($topParent == 'PREOWNED' && in_array('status_condition', $upteamconditions))) {
                if ($product->status_id == StatusHelper::$finalApproval) {
                    if ($this->log) {
                        $this->log->sync_status = 'condition_checking';
                        $this->log->message = 'Product has been started to check conditions.';
                        $this->log->queue_id = $this->job->getJobId();
                        $this->log->job_start_time = $date_time;
                        $this->log->save();
                    }
                    ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'entered_in_product_push', 'is_checked' => 1]);

                    if ($isCharityChecked == 1) {
                        ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'charity_condition', 'is_checked' => 1]);
                    }
                    ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'status_condition', 'is_checked' => 1]);
                    if ($website->sale_old_products == 0 and strtoupper($topParent) == 'PREOWNED') {
                        ProductPushErrorLog::log('', $product->id, 'Website do not sale preowned products.', 'error', $website->id, null, null, $this->log->id);
                        $this->log->message = 'Website do not sale preowned products';
                        $this->log->sync_status = 'error';
                        $this->log->job_end_time = date('Y-m-d H:i:s');
                        $this->log->save();

                        return false;
                    }
                    if ((in_array('website_source', $conditions) && $topParent == 'NEW') || ($topParent == 'PREOWNED' && in_array('website_source', $upteamconditions))) {
                        if (! $website->website_source || $website->website_source == '') {
                            ProductPushErrorLog::log('', $product->id, 'Website Source not found', 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['website_source']);
                            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'website_source', 'is_checked' => 1]);
                            $this->log->message = 'Website source not found';
                            $this->log->sync_status = 'error';
                            $this->log->job_end_time = date('Y-m-d H:i:s');
                            $this->log->save();

                            return false;
                        }
                        ProductPushErrorLog::log('', $product->id, 'Website Source  found', 'success', $website->id, null, null, $this->log->id, $conditionsWithIds['website_source']);
                    }
                    if (($topParent == 'NEW' && in_array('disable_push', $conditions)) || ($topParent == 'PREOWNED' && in_array('disable_push', $upteamconditions))) {
                        if ($website->disable_push == 1) {
                            ProductPushErrorLog::log('', $product->id, 'Website is disable for push product', 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['disable_push']);
                            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'disable_push', 'is_checked' => 1]);
                            $this->log->message = 'Website is disable for push product';
                            $this->log->sync_status = 'error';
                            $this->log->job_end_time = date('Y-m-d H:i:s');
                            $this->log->save();

                            return false;
                        }
                        ProductPushErrorLog::log('', $product->id, 'Website is enabled for push product', 'success', $website->id, null, null, $this->log->id, $conditionsWithIds['disable_push']);
                    }

                    // started to check the validation for the category size is available or not and if not then throw the error
                    if ($categorym && ! $product->isCharity()) {
                        $categoryparent = $categorym->parent;
                        if (($topParent == 'NEW' && in_array('check_if_size_chart_exists', $conditions)) || ($topParent == 'PREOWNED' && in_array('check_if_size_chart_exists', $upteamconditions))) {
                            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'check_if_size_chart_exists', 'is_checked' => 1]);
                            if ($categoryparent && $categoryparent->size_chart_needed == 1 && empty($categoryparent->getSizeChart($website->id))) {
                                ProductPushErrorLog::log('', $product->id, 'Size chart is needed for push product', 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['check_if_size_chart_exists']);
                                $this->log->message = 'Size chart is needed for push product';
                                $this->log->sync_status = 'size_chart_needed';
                                $this->log->job_end_time = date('Y-m-d H:i:s');
                                $this->log->save();

                                return false;
                            }

                            if ($categorym && $categorym->size_chart_needed == 1 && empty($categorym->getSizeChart($website->id))) {
                                ProductPushErrorLog::log('', $product->id, 'Size chart is needed for push product', 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['check_if_size_chart_exists']);
                                $this->log->message = 'Size chart is needed for push product';
                                $this->log->sync_status = 'size_chart_needed';
                                $this->log->job_end_time = date('Y-m-d H:i:s');
                                $this->log->save();

                                return false;
                            }
                            ProductPushErrorLog::log('', $product->id, 'Size chart is needed for push product for topParent: '.$topParent, 'success', $website->id, null, null, $this->log->id, $conditionsWithIds['check_if_size_chart_exists']);
                        }
                    }

                    // check the product has images or not and then if no image for push then assign error it
                    if (($topParent == 'NEW' && in_array('check_if_images_exists', $conditions)) && ($topParent == 'PREOWNED' && in_array('check_if_images_exists', $upteamconditions))) {
                        ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $product->id, 'condition' => 'check_if_images_exists', 'is_checked' => 1]);
                        $images = $product->getImages('gallery_'.$website->cropper_color);
                        if (empty($images) && $charity == 0) {
                            ProductPushErrorLog::log('', $product->id, 'Image(s) is needed for push product', 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['check_if_images_exists']);
                            $this->log->message = 'Image(s) is needed for push product';
                            $this->log->sync_status = 'image_not_found';
                            $this->log->job_end_time = date('Y-m-d H:i:s');
                            $this->log->save();

                            return false;
                        }
                        ProductPushErrorLog::log('', $product->id, 'Image(s) is needed for push product', 'success', $website->id, null, null, $this->log->id, $conditionsWithIds['check_if_images_exists']);
                    }
                    
                   
                    try {
                        Flow2ConditionCheckAll::dispatch($product, $website, $this->log, $this->mode,$this->details)->onQueue($this->log->queue);
                    } catch (\Exception $e) {
                        $error_msg = 'Condition Check Second Job failed: '.$e->getMessage();
                        $this->log->sync_status = 'error';
                        $this->log->message = $error_msg;
                        $this->log->save();
                        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $this->log->id, null);
                    }

                    if ($this->log) {
                        $this->log->job_end_time = date('Y-m-d H:i:s');
                        $this->log->save();
                    }
                } else {
                    $errorMessage = 'Product have not set for final approval, current status is -'.$product->status_id;
                    if ($this->log) {
                        ProductPushErrorLog::log('', $product->id, $errorMessage, 'error', $website->id, null, null, $this->log->id, $conditionsWithIds['status_condition']);
                        $this->log->message = $errorMessage;
                        $this->log->sync_status = 'error';
                        $this->log->queue_id = $this->job->getJobId();
                        $this->log->job_end_time = date('Y-m-d H:i:s');
                        $this->log->save();
                    } else {
                        \Log::error($errorMessage);
                    }
                }
            } else {
                $errorMessage = 'Either one of the following condition failed: Top parent is NEW and status_condition exists in '.json_encode($conditions).' || Top parent is PREOWNED and status_condition exists in upteamconditions';
                ProductPushErrorLog::log('', $product->id, $errorMessage, 'error', $website->id, null, null, $this->log->id, null);
            }
        } catch (\Exception $e) {
            if ($this->log) {
                ProductPushErrorLog::log('', $product->id, $e->getMessage(), 'error', $website->id, null, null, $this->log->id);
                $this->log->message = $e->getMessage();
                $this->log->sync_status = 'error';
                $this->log->queue_id = $this->job->getJobId();
                $this->log->job_end_time = date('Y-m-d H:i:s');
                $this->log->save();
            } else {
                \Log::error($e);
            }
        }
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        $website = $this->_website;

        $error_msg = 'Flow2ConditionCheckBasic failed for '.$product->name;
        if ($this->log) {
            $this->log->sync_status = 'error';
            $this->log->message = $error_msg;
            $this->log->save();
        }
        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $this->log->id);
    }

    public function tags()
    {
        return ['product_'.$this->_product->id,'#'.$this->product_index,$this->no_of_product];
    }
}
