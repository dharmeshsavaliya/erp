<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\GTMetrixCategories;
use App\StoreViewsGTMetrix;
use App\StoreGTMetrixAccount;
use App\GTMatrixErrorLog;
use Carbon\Carbon;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Illuminate\Console\Command;

class GTMetrixTestCMDGetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GT-metrix-test-get-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT metrix get site report';

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

        //try {
        \Log::info('GTMetrix :: Report cron start ');
      
        $report = CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);


        // Get site report
        $storeViewList = StoreViewsGTMetrix::whereNotNull('test_id')
            ->whereNotIn('status', ['completed','error', 'not_queued'])
            ->orderBY('id', 'desc')
            ->get()->take(1);
        
        $Api_key = env('PAGESPEEDONLINE_API_KEY'); 
        
        foreach ($storeViewList as $value) {
            
                // if ($Api_key =="") {
                //     $this->GTMatrixError($value->id, 'pagespeed',  'API Key not found', 'API Key not found');
                // }
                // $curl = curl_init();
                // curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url='.$value->website_url.'&key='.$Api_key,
                // CURLOPT_RETURNTRANSFER => true,
                // CURLOPT_ENCODING => '',
                // CURLOPT_MAXREDIRS => 10,
                // CURLOPT_TIMEOUT => 0,
                // CURLOPT_FOLLOWLOCATION => true,
                // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                // CURLOPT_CUSTOMREQUEST => 'GET',
                // CURLOPT_HTTPHEADER => array(
                //     'Accept: application/json'
                // ),
                // ));
    
                // $response = curl_exec($curl);
                // // Get possible error
                // $err = curl_error($curl);
                
                // curl_close($curl);
                // if ($err) {
                //     $this->GTMatrixError($value->id, 'pagespeed',  'API response error', $err);
                //     \Log::info('PageSpeedInsight :: Something went Wrong Not able to fetch site  Result' . $err );
                //     echo "cURL Error #:" . $err;
                // } else {
                //     //echo $response;
                // \Log::info(print_r(["Pagespeed Insight Result started to fetch"],true));

                //     // $pdfFileName = '/uploads/speed-insight/' . $value->test_id . '.pdf';
                //     // $pdfFile     = public_path() . $pdfFileName;
                //     // file_put_contents($pdfFile,$response);

                //     $JsonfileName = '/uploads/speed-insight/' . $value->test_id . '_pagespeedInsight.json';
                //     $Jsonfile     = public_path() . $JsonfileName;
                //     if(!file_exists($JsonfileName))
                //         $this->GTMatrixError($value->id, 'pagespeed', 'File not found', $value->test_id . '_pagespeedInsight.json');
                //     file_put_contents($Jsonfile,$response);
                //     $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                //     if(!$storeview)
                //         $this->GTMatrixError($value->id, 'pagespeed', 'Store view test_id', 'store view test_id not found');
                    
                //     \Log::info(print_r(["Store view found"],true));

                //     if ($storeview) {
                //         $storeview->pagespeed_insight_json = $JsonfileName;
                //         $storeview->save();

                //         if($response && !empty($response)){
                //             $responseData = json_decode($response, true);
                //             if(isset($responseData) && isset($responseData['lighthouseResult']) && $responseData['lighthouseResult'] != ""){
                                
                //                 foreach ($responseData['lighthouseResult']['audits'] as $key => $pageSpeedData) {
                //                     $key_data = GTMetrixCategories::where('name', $key)->first();
                //                     if(isset($key_data) && $key_data != "" && $key_data->website_url != $value->website_url && $key_data->test_id != $value->test_id){

                //                     }else{
                //                         $GTMetrixCategories = new GTMetrixCategories();
                //                         $GTMetrixCategories->name = $key;
                //                         $GTMetrixCategories->source = 'Pagespeed Insight';
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // }
                dd($value->account_id);    
                if(!empty($value->account_id)){
                    
                    $gtmatrix = StoreGTMetrixAccount::where('account_id', $value->account_id)->where('status', 'active')->first();
                    
                    $username = $gtmatrix['email'];
                    $password = $gtmatrix['account_id'];
                    
                    $curl = curl_init();
    
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $value->account_id . ":" . '',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        ));
    
                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        if($err)
                            $this->GTMatrixError($value->id, 'gtmetrix',  'Account ID Not found', 'account_id : '.$value->id.' Error'.$err);
                        curl_close($curl);
                       // $stdClass = json_decode(json_encode($response));
                        $data = json_decode($response);
                        $credits = '';
                        if(isset($data->data->attributes->api_credits))
                        {
                            $credits = $data->data->attributes->api_credits;
                        }
                       
                       // print_r($data->data->attributes->api_credits);
                        if($credits != 0){
                            
                            $client = new GTMetrixClient();
                            $client->setUsername($username);
                            $client->setAPIKey($password);
                            $client->getLocations();
                            $client->getBrowsers();
                        }
                        else{
                            $gtmatrixAccount = StoreGTMetrixAccount::select(\DB::raw('store_gt_metrix_account.*'));
                            $AccountData = $gtmatrixAccount->where('status', 'active')->orderBy('id','desc')->get();
                            
                            foreach ($AccountData as $key => $ValueData) {
                                $curl = curl_init();
    
                                curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_USERPWD => $ValueData['account_id'] . ":" . '',
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                ));
    
                                $response = curl_exec($curl);
                                $err = curl_error($curl);
                                if($err)
                                    $this->GTMatrixError($value->id, 'gtmetrix',  'gtmetrix status error', $err);
                                curl_close($curl);
                                // decode the response 
                                $data = json_decode($response);
                                dd($data);
                                $credits = $data->data->attributes->api_credits;
                                if($credits!= 0){
                                    $username = $ValueData['email'];
                                    $password = $ValueData['account_id'];
                                    $client = new GTMetrixClient();
                                    $client->setUsername($username);
                                    $client->setAPIKey($password);
                                    $client->getLocations();
                                    $client->getBrowsers();
                                }
                                
                            }
                        }
                        
                        try{                      
                            $test   = $client->getTestStatus($value->test_id);                     
                            $model = $value->update([
                                'status'          => $test->getState(),
                                'error'           => $test->getError(),
                                'report_url'      => $test->getReportUrl(),
                                'html_load_time'  => $test->getHtmlLoadTime(),
                                'html_bytes'      => $test->getHtmlBytes(),
                                'page_load_time'  => $test->getPageLoadTime(),
                                'page_bytes'      => $test->getPageBytes(),
                                'page_elements'   => $test->getPageElements(),
                                'pagespeed_score' => $test->getPagespeedScore(),
                                'yslow_score'     => $test->getYslowScore(),
                                'resources'       => json_encode($test->getResources()),
                                //'pdf_file'        => $fileName,
                            ]);
            
                            $resources = $test->getResources();
                            \Log::info(print_r(["Resource started",$resources],true));
            
                            if (!empty($resources['report_pdf'])) {
                                $ch = curl_init($resources['report_pdf']);
                                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 0);
                                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                                $result     = curl_exec($ch);
                                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $curlErrNo  = curl_errno($ch);
                                $curlError  = curl_error($ch);
                                $err = curl_error($curl);
                                if($err)
                                    $this->GTMatrixError($value->id, $resources['report_pdf'],  'Generate report pdf Error', $err);
                                curl_close($ch);
            
                                \Log::info(print_r(["Result started to fetch"],true));
            
                                $fileName = '/uploads/gt-matrix/' . $value->test_id . '.pdf';
                                $file     = public_path() . $fileName;
                                file_put_contents($file,$result);
                                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();
            
                                \Log::info(print_r(["Store view found",$storeview],true));
            
                                    if(!$storeview){
                                        $this->GTMatrixError($value->id, 'pagespeed', 'Store view test_id', 'store view test_id not found');
                                    }
                                    $storeview->pdf_file = $fileName;
                                    $storeview->save();
                            }
                           
                            if (!empty($resources['pagespeed'])) {
                                $ch = curl_init($resources['pagespeed']);
                                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 0);
                                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                                $result     = curl_exec($ch);
                                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $curlErrNo  = curl_errno($ch);
                                $curlError  = curl_error($ch);
                                $err = curl_error($curl);
                                if($err)
                                    $this->GTMatrixError($value->id, 'pagespeed',  'Result started to fetch pagespeed json error', $err);
                                curl_close($ch);
            
                                \Log::info(print_r(["Result started to fetch pagespeed json"],true));
            
                                $fileName = '/uploads/gt-matrix/' . $value->test_id . '_pagespeed.json';
                                $file     = public_path() . $fileName;
                                file_put_contents($file,$result);
                                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();
            
                                \Log::info(print_r(["Store view found",$storeview],true));
            
                                if ($storeview) {
                                    $storeview->pagespeed_json = $fileName;
                                    $storeview->save();
                                    if($result && !empty($result)){
                                        $responseData = json_decode($result, true);
                                        if(isset($responseData) && isset($responseData['rules']) && $responseData['rules'] != ""){
                                            foreach($responseData['rules'] as $key=>$valueData){
                                                $key_data = GTMetrixCategories::where('name', $valueData['name'])->first();
                                                if(isset($key_data) && $key_data != "" && $key_data->website_url != $value->website_url && $key_data->test_id != $value->test_id){
                                                }else{
                                                    $GTMetrixCategories = new GTMetrixCategories();
                                                    $GTMetrixCategories->name = $valueData['name'];
                                                    $GTMetrixCategories->source = 'pagespeed';
                                                    $GTMetrixCategories->save();
                                                }
                                                
                                            }
                                        }
                                        
                                    }
    
                                }
                            }
                            if (!empty($resources['yslow'])) {
                                $ch = curl_init($resources['yslow']);
                                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 0);
                                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                                $result     = strip_tags(curl_exec($ch));
                                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $curlErrNo  = curl_errno($ch);
                                $curlError  = curl_error($ch);
                                $err = curl_error($curl);
                                if($err)
                                    $this->GTMatrixError($value->id, 'yslow',  'Result started to fetch yslow json error', $err. ' Account ID : '.$value->id);
                                curl_close($ch);
            
                                \Log::info(print_r(["Result started to fetch yslow json"],true));
            
                                $fileName = '/uploads/gt-matrix/' . $value->test_id . '_yslow.json';
                                $file     = public_path() . $fileName;
                                file_put_contents($file, $result);
                                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();
            
                                \Log::info(print_r(["Store view found",$storeview],true));
            
                                if ($storeview) {
                                    $storeview->yslow_json = $fileName;
                                    $storeview->save();
                                    if($result && !empty($result)){
                                        $responseData = json_decode($result, true);
                                        if(isset($responseData) && isset($responseData['g']) && $responseData['g'] != ""){
                                            foreach($responseData['g'] as $key=>$values){
                                                $key_data = GTMetrixCategories::where('name', $key)->first();
                                                if(isset($key_data) && $key_data != "" && $key_data->website_url != $value->website_url && $key_data->test_id != $value->test_id){
                                                    
                                                }else{
                                                    $GTMetrixCategories = new GTMetrixCategories();
                                                    $GTMetrixCategories->name = $key;
                                                    $GTMetrixCategories->source = 'yslow';
                                                    $GTMetrixCategories->save();
                                                }
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }catch(\Exception $e) {
                            $value->status = "error";
                            $value->error = $e->getMessage();
                            $value->save();
                            if($err)
                                    $this->GTMatrixError($value->id, 'pagespeed',  'pagespeed catch error', $e->getMessage());
                        }
                } else {
                    if(empty($value->account_id))
                        $this->GTMatrixError($value->id, 'pagespeed', 'Store view Account_id', 'pagespeed store view Account id not found');
                    
                }
            
        }
        \Log::info('GTMetrix :: Report cron complete ');
        $report->update(['end_time' => Carbon::now()]);

        /*} catch (\Exception $e) {
    \Log::error($this->signature.' :: '.$e->getMessage() );
    \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }*/
    }

    public function GTMatrixError($store_viewGTM_id = '', $erro_type = '', $error_title, $error = ''){
        try {
            $GTError = new GTMatrixErrorLog();
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type = $erro_type;
            $GTError->error_title = $error_title;
            $GTError->error = $error;
            $GTError->save();
        }catch(\Exception $e) {
            $GTError = new GTMatrixErrorLog();
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type = $erro_type;
            $GTError->error = $e->getMessage();
            $GTError->save();
        }
    }
}
