<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use App\AppSubscriptionReport;



class IosSubscriptionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosSubscriptionReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscription report using Appfigure which sync with Appstore connect check and store DB every day';

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

 $array_app_name=explode(",",env("APPFIGURE_APP_NAME"));
        $i=0;
$array_app=explode(",",env("APPFIGURE_PRODUCT_ID"));
            foreach ($array_app as $app_value) {

        //Usage Report
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.appfigures.com/v2/reports/subscriptions?group_by='.$group_by.'&start_date='.$start_date.'&end_date='.$end_date.'&products='.$app_value,
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


            $r=new AppSubscriptionReport();
           $r->product_id=$array_app_name[$i]." [".$product_id."]";
            $r->group_by=$group_by;
            $r->start_date=$start_date;
            $r->end_date=$end_date;

            $r->active_subscriptions=$res["apple:ios"]["active_subscriptions"];
            $r->active_free_trials=$res["apple:ios"]["active_free_trials"];
            $r->new_subscriptions=$res["apple:ios"]["new_subscriptions"];
            $r->cancelled_subscriptions=$res["apple:ios"]["cancelled_subscriptions"];
            $r->new_trials=$res["apple:ios"]["new_trials"];
            $r->trial_conversion_rate=$res["apple:ios"]["trial_conversion_rate"];
            $r->mrr=$res["apple:ios"]["mrr"];
            $r->actual_revenue=$res["apple:ios"]["actual_revenue"];
            $r->renewals=$res["apple:ios"]["renewals"];
            $r->first_year_subscribers=$res["apple:ios"]["first_year_subscribers"];
            $r->non_first_year_subscribers=$res["apple:ios"]["non_first_year_subscribers"];
            $r->reactivations=$res["apple:ios"]["reactivations"];
            $r->transitions_out=$res["apple:ios"]["transitions_out"];
            $r->trial_cancellations=$res["apple:ios"]["trial_cancellations"];       
            $r->transitions_in=$res["apple:ios"]["transitions_in"];
            $r->activations=$res["apple:ios"]["activations"];
            $r->cancellations=$res["apple:ios"]["cancellations"];
            $r->trial_conversions=$res["apple:ios"]["trial_conversions"];
            $r->churn=$res["apple:ios"]["churn"];
            $r->gross_revenue=$res["apple:ios"]["gross_revenue"];
            $r->gross_mrr=$res["apple:ios"]["gross_mrr"];
            $r->active_grace=$res["apple:ios"]["active_grace"];
            $r->new_grace=$res["apple:ios"]["new_grace"];
            $r->grace_drop_off=$res["apple:ios"]["grace_drop_off"];
            $r->grace_recovery=$res["apple:ios"]["grace_recovery"];
            $r->new_trial_grace=$res["apple:ios"]["new_trial_grace"];
            $r->trial_grace_drop_off=$res["apple:ios"]["trial_grace_drop_off"];
            $r->trial_grace_recovery=$res["apple:ios"]["trial_grace_recovery"];
            $r->active_trials=$res["apple:ios"]["active_trials"];
            $r->active_discounted_subscriptions=$res["apple:ios"]["active_discounted_subscriptions"];
            $r->all_active_subscriptions=$res["apple:ios"]["all_active_subscriptions"];
            $r->paying_subscriptions=$res["apple:ios"]["paying_subscriptions"];
            $r->all_subscribers=$res["apple:ios"]["all_subscribers"];           
            $r->storefront=$res["apple:ios"]["storefront"];
            $r->store=$res["apple:ios"]["store"];
            $r->save();
        }

$i+=1;
            
}

        
        return $this->info("Subscription Report added");
    }
}
