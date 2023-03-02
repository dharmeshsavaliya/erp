<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use App\AppUsageReport;

session_start();  

class IosReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Usage using Appfigure which sync with Appstore connect check and store DB every day';

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
     * @return int
     */
    public function handle()
    {

        


        // https://api.appfigures.com/v2/reports/usage?group_by=network&start_date=2023-02-13&end_date=2023-02-14&products=280598515284

        $username=env("APPFIGURE_USER_EMAIL");
        $password=env("APPFIGURE_USER_PASS");
        $key=base64_encode($username.":".$password);
        
        $group_by='network';
        $start_date=date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $end_date=date('Y-m-d');
        $product_id=env("APPFIGURE_PRODUCT_ID");
        $ckey=env("APPFIGURE_CLIENT_KEY");


        //Usage Report
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.appfigures.com/v2/reports/usage?group_by='.$group_by.'&start_date='.$start_date.'&end_date='.$end_date.'&products='.$product_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'X-Client-Key:'.$ckey,
        'Authorization: Basic '.$key
        ),
        ));


        $result = curl_exec($curl);
        // print($result);
        $res=json_decode($result, true);
        // print_r($res);
        // print_r($res["apple:analytics"]);
        // print($res["apple:analytics"]["crashes"]);
        curl_close($curl);

        if($res)
        {

            $r=new AppUsageReport();
            $r->product_id=$product_id;
            $r->group_by=$group_by;
            $r->start_date=$start_date;
            $r->end_date=$end_date;
            $r->crashes=$res["apple:analytics"]["crashes"];
            $r->sessions=$res["apple:analytics"]["sessions"];
            $r->app_store_views=$res["apple:analytics"]["app_store_views"];
            $r->unique_app_store_views=$res["apple:analytics"]["unique_app_store_views"];
            $r->daily_active_devices=$res["apple:analytics"]["daily_active_devices"];
            $r->monthly_active_devices=$res["apple:analytics"]["monthly_active_devices"];
            $r->paying_users=$res["apple:analytics"]["paying_users"];
            $r->impressions=$res["apple:analytics"]["impressions"];
            $r->unique_impressions=$res["apple:analytics"]["unique_impressions"];
            $r->uninstalls=$res["apple:analytics"]["uninstalls"];
            $r->avg_daily_active_devices=$res["apple:analytics"]["avg_daily_active_devices"];
            $r->avg_optin_rate=$res["apple:analytics"]["avg_optin_rate"];
            $r->storefront=$res["apple:analytics"]["storefront"];
            $r->store=$res["apple:analytics"]["store"];
            $r->save();
        }


            


        
        return $this->info("Usage Report added");
    }
}