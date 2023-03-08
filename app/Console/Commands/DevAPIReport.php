<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use App\GoogleDeveloper;
use App\GoogleDeveloperLogs;

session_start();  

class DevAPIReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DevAPIReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crash and ANR report using playdeveloperreporting check and store DB every hour';

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
        $log = new GoogleDeveloperLogs();    
        $log->api='crash/anr';
        $redirect_uri = 'https://erpstage.theluxuryunlimited.com/google/developer-api/crash';

        // print($redirect_uri);
        $client = new Client();
        $client->setApplicationName(env("GOOGLE_PLAY_STORE_APP_ID"));
        $client->setDeveloperKey(env("GOOGLE_PLAY_STORE_DEV_KEY"));
        $client->setClientId(env("GOOGLE_PLAY_STORE_CLIENT_ID"));
        $client->setClientSecret(env("GOOGLE_PLAY_STORE_CLIENT_SECRET"));
        $SERVICE_ACCOUNT_NAME = env("GOOGLE_PLAY_STORE_SERVICE_ACCOUNT"); 
        $KEY_FILE = storage_path().env("GOOGLE_PLAY_STORE_SERVICE_CREDENTIALS");
        $client->setRedirectUri($redirect_uri);
        // $log->log_name='key_file_path';
        // $log->result=$KEY_FILE;
        // $log->save();
        $client->setAuthConfig($KEY_FILE);
        $user_to_impersonate= env("GOOGLE_PLAY_STORE_SERVICE_ACCOUNT");
        $client->setSubject($user_to_impersonate);
        $client->setScopes(array(env("GOOGLE_PLAY_STORE_SCOPES")));

        $token=null;
        if ($client->isAccessTokenExpired()) 
        {   
            $token = $client->fetchAccessTokenWithAssertion();
             
        }
        else 
        {
            $token = $client->getAccessToken();
             
        }
        $_SESSION['token']=$token;
     
        if (!$token && !isset($_SESSION['token'])) 
        {
            $authUrl = $client->createAuthUrl();
              
            $output="connect";
            $output2=$authUrl;
        } 
        else
        {

            $at=$_SESSION['token']["access_token"];
            //crash report

            $res =  Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.env("GOOGLE_PLAY_STORE_APP").'/crashRateMetricSet?access_token='.$at);

   

            if(gettype($res)!="string")
            {

                if(isset($res["error"]))
                {

                    if($res["error"]["code"]==401)
                    {
                        session_unset();
                        $log = new GoogleDeveloperLogs();    
                        // $log->api='crash'; 
                        //         $log->log_name='error_code';
                        //         $log->result="401 error";
                        //         $log->save();
                        echo "401 error";
                        // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                        // $client->setRedirectUri($redirect_uri);

                    }
                }
                if(isset($res["name"]))
                {
                    // print($res["name"]);
                    // print($res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"]);
                    // // print($res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"]);
                    $year=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["year"];
                    $day=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["day"];
                    $month=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["month"];
                    $date = $year.'-'.$month.'-'.$day;
                    // print($date);
                    //  print($res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["timeZone"]["id"]);
                    $r = new GoogleDeveloper();
                    $r->name = $res["name"];
                    $r->aggregation_period = $res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"];
                    $r->latestEndTime = $date;
                    $r->timezone = $res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["timeZone"]["id"];
                    $r->report ="crash";
                    $r->save();

                    // $log = new GoogleDeveloperLogs();    
            
                    // $log->api='crash';
                    // $log->log_name='result';
                    // $log->result="success";
                    // $log->save();
                    $postData = array(
                    'timeline_spec' => array('aggregation_period' => "DAILY","start_time"=>array("year"=>$year,"month"=>$month,"day"=>$day-2),"end_time"=>array("year"=>$year,"month"=>$month,"day"=>$day-1)),

                    'dimensions' => array('apiLevel'),
                    'metrics' => array('crashRate','distinctUsers','crashRate28dUserWeighted')
                    );

                    // Setup cURL

                    $ch = curl_init('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.env("GOOGLE_PLAY_STORE_APP").'/crashRateMetricSet:query');
                    curl_setopt_array($ch, array(
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HTTPHEADER => array(
                    'Authorization: OAuth '.$at,

                    'Content-Type: application/json'
                    ),
                    CURLOPT_POSTFIELDS => json_encode($postData)
                    ));

                    // Send the request
                    $response = curl_exec($ch);
                    print($response);

                    $log = new GoogleDeveloperLogs();    
                    $log->api='crash report';   
                    $log->log_name='result';
                    $log->result=$response;
                    $log->save();

                }

            }
            else{
                    $log = new GoogleDeveloperLogs();    

                    $log->api='crash report';
                    $log->log_name='result';
                    $log->result=$res;
                    $log->save();
            }

            //ANR Report



            $res =  Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.env("GOOGLE_PLAY_STORE_APP").'/anrRateMetricSet?access_token='.$at);
            $log = new GoogleDeveloperLogs();    
            
            //         $log->api='anr';
            //  $log->log_name='result';
            // $log->result=$res;
            // $log->save();

            if(gettype($res)!="string")
            {

                if(isset($res["error"]))
                {

                    if($res["error"]["code"]==401)
                    {
                    session_unset();
                    $log = new GoogleDeveloperLogs();    
            
                    // $log->api='anr';
                    // $log->log_name='error_code';
                    // $log->result="401 error";
                    // $log->save();
                    echo "401 error";
                    // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                    // $client->setRedirectUri($redirect_uri);
                    }
                }
                if(isset($res["name"]))
                {
                    // print($res["name"]);
                    // print($res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"]);
                    // // print($res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"]);
                    $year=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["year"];
                    $day=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["day"];
                    $month=$res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["month"];
                    $date = $year.'-'.$month.'-'.$day;
                    // print($date);
                    //  print($res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["timeZone"]["id"]);
                    $r = new GoogleDeveloper();
                    $r->name = $res["name"];
                    $r->aggregation_period = $res["freshnessInfo"]["freshnesses"][0]["aggregationPeriod"];
                    $r->latestEndTime = $date;
                    $r->timezone = $res["freshnessInfo"]["freshnesses"][0]["latestEndTime"]["timeZone"]["id"];
                    $r->report ="anr";
                    $r->save();
                     // $log = new GoogleDeveloperLogs();    
            
                    // $log->api='anr';
                 
                    // $log->log_name='result';
                    // $log->result="success";
                    // $log->save();
                    $postData = array(
                    'timeline_spec' => array('aggregation_period' => "DAILY","start_time"=>array("year"=>$year,"month"=>$month,"day"=>$day-2),"end_time"=>array("year"=>$year,"month"=>$month,"day"=>$day-1)),

                    'dimensions' => array('apiLevel'),
                    'metrics' => array('distinctUsers')
                    );

                    // Setup cURL

                    $ch = curl_init('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.env("GOOGLE_PLAY_STORE_APP").'/crashRateMetricSet:query');
                    curl_setopt_array($ch, array(
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HTTPHEADER => array(
                    'Authorization: OAuth '.$at,

                    'Content-Type: application/json'
                    ),
                    CURLOPT_POSTFIELDS => json_encode($postData)
                    ));

                    // Send the request
                    $response = curl_exec($ch);
                    // print($response);

                    $log = new GoogleDeveloperLogs();    
                    $log->api='anr report';   
                    $log->log_name='result';
                    $log->result=$response;
                    $log->save();
                    echo "Crash and ANR report added";
                }

            }
            else{
                    $log = new GoogleDeveloperLogs();    

                    $log->api='anr';
                    $log->log_name='result';
                    $log->result=$res;
                    $log->save();
            }


        }
       
    }
}
