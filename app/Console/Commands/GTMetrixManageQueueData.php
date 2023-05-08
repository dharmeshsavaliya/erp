<?php

namespace App\Console\Commands;

use App\StoreViewsGTMetrix;
use App\StoreGTMetrixAccount;
use Illuminate\Http\Response;
use App\StoreViewsGTMetrixUrl;
use Illuminate\Console\Command;
use Entrecore\GTMetrixClient\GTMetrixClient;
use App\Helpers\LogHelper;

class GTMetrixManageQueueData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:gt_metrix_manage_queue_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT METRIX MANAGE QUEUE DATA';

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
        try{
            $gtmatrixAccount = StoreGTMetrixAccount::select('store_gt_metrix_account.*');
            $query = StoreViewsGTMetrixUrl::select('store_views_gt_metrix_url.*');
            $lists = $query->where('process', 1)->get();
            if ($lists) {
                foreach ($lists as $list) {
                    $gt_metrix['store_view_id'] = $list->store_view_id;
                    $gt_metrix['account_id'] = $list->account_id;
                    $gt_metrix['website_id'] = $list->id;
                    $gt_metrix['website_url'] = $list->website_url;

                    $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
                    $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
                    $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_id', $gt_metrix['website_id'])->first();

                    try {
                        if (! empty($gtmatrix->account_id)) {
                            $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->first();

                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_USERPWD => $gtmatrixAccountData->account_id . ':' . '',
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                            ]);

                            $response = curl_exec($curl);
                            curl_close($curl);

                            $data = json_decode($response);
                            $credits = $data->data->attributes->api_credits;

                            if ($credits != 0) {
                                $client = new GTMetrixClient();
                                $client->setUsername($gtmatrixAccountData->email);
                                $client->setAPIKey($gtmatrixAccountData->account_id);
                                $client->getLocations();
                                $client->getBrowsers();
                                $test = $client->startTest($gtmetrix->website_url);
                                $update = [
                                    'test_id' => $test->getId(),
                                    'status' => 'queued',
                                ];
                                $gtmetrix->update($update);
                            }
                        } else {
                            $AccountData = $gtmatrixAccount->orderBy('id', 'desc')->get();

                            foreach ($AccountData as $key => $value) {
                                $curl = curl_init();

                                curl_setopt_array($curl, [
                                    CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_USERPWD => $value['account_id'] . ':' . '',
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                ]);

                                $response = curl_exec($curl);

                                curl_close($curl);
                                // decode the response
                                $data = json_decode($response);
                                $credits = $data->data->attributes->api_credits;
                                if ($credits != 0) {
                                    $client = new GTMetrixClient();
                                    $client->setUsername($value['email']);
                                    $client->setAPIKey($value['account_id']);
                                    $client->getLocations();
                                    $client->getBrowsers();
                                    $test = $client->startTest($gtmetrix->website_url);
                                    $update = [
                                        'test_id' => $test->getId(),
                                        'status' => 'queued',
                                        'account_id' => $value['account_id'],
                                    ];
                                    $gtmetrix->update($update);
                                    break;
                                }
                            }
                        }
                        \Log::info('GTMetrix :: successfully');
                    } catch (\Exception $e) {
                        $update = [
                            'test_id' => null,
                            'status' => 'not_queued',
                            'error' => $e->getMessage(),
                        ];
                        $gtmetrix->update($update);
                        \Log::info('GTMetrix :: successfully' . $e->getMessage());
                    }
                }
            }
        }catch(\Exception $e){
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
