<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use App\AppRatingsReport;



class IosRatingsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosRatingsReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ratings report using Appfigure which sync with Appstore connect check and store DB every day';

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
        CURLOPT_URL => 'https://api.appfigures.com/v2/reports/ratings?group_by='.$group_by.'&start_date='.$start_date.'&end_date='.$end_date.'&products='.$product_id,
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
        // print_r($res["apple:ios"]);
        // print($res["apple:ios"]["downloads"]);
        curl_close($curl);

        if($res)
        {


            $r=new AppRatingsReport();
            $r->product_id=$product_id;
            $r->group_by=$group_by;
            $r->start_date=$start_date;
            $r->end_date=$end_date;

            $r->breakdown=implode(",",$res["apple:ios"]["breakdown"]);
            $r->new=implode(",",$res["apple:ios"]["new"]);
            $r->average=$res["apple:ios"]["average"];
            $r->total=$res["apple:ios"]["total"];
            $r->new_average=$res["apple:ios"]["new_average"];
            $r->new_total=$res["apple:ios"]["new_total"];
            $r->positive=$res["apple:ios"]["positive"];
            $r->negative=$res["apple:ios"]["negative"];
            $r->neutral=$res["apple:ios"]["neutral"];
            $r->new_positive=$res["apple:ios"]["new_positive"];
            $r->new_negative=$res["apple:ios"]["new_negative"];
            $r->new_neutral=$res["apple:ios"]["new_neutral"];
            $r->storefront=$res["apple:ios"]["storefront"];
            $r->store=$res["apple:ios"]["store"];
            $r->save();
        }


            


        
        return $this->info("Ratings Report added");
    }
}
