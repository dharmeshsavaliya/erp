<?php

namespace App\Console\Commands;

use App\Problem;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Host;
class ZabbixProblemImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zabbix:problem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get problems';

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
        $auth_key = $this->login_api();
        if ($auth_key != '') {
            $problems = $this->problem_api($auth_key);
            foreach ($problems as  $key => $val) {
                foreach($val as $problem){
                    $check_if_exists = Problem::where('eventid', $problem->eventid)->first();
                    $host = Host::where('hostid',$problem->host_id)->first();
                    if (! is_null($check_if_exists)) {
                        $array = [
                            'object_id' => $problem->object_id,
                            'name' => $problem->name,
                            'hostname' => $host->host 
                        ];
                        
                        Problem::where('eventid', $problem->eventid)->update($array);
                    } else {
                        $array = [
                            'eventid' => $problem->eventid,
                            'objectid' => $problem->objectid,
                            'name' => $problem->name,
                            'hostname' => $host->host
                        ];
                        Problem::create($array);
                    }
                }
            }
        }
    }

    public function login_api()
    {
        //Get API ENDPOINT response
        $curl = curl_init(env('ZABBIX_HOST').'/api_jsonrpc.php');
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => [
                'username' => env('ZABBIX_USERNAME'),
                'password' => env('ZABBIX_PASSWORD'),
            ],
            'id' => 1,
        ];
        $datas = json_encode([$data]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        $results = json_decode($result);

        if (isset($results[0]->result)) {
            return $results[0]->result;
        } else {
            \Log::channel('general')->info(Carbon::now().$results[0]->error->data);

            return 0;
        }
    }

    public function problem_api($auth_key)
    {
        //Get API ENDPOINT response
        $hostid =  \App\Host::pluck('hostid');
        $errorarray =  array();
            foreach($hostid as $val){
                $curl = curl_init(env('ZABBIX_HOST').'/api_jsonrpc.php');
                $data = [
                    'jsonrpc' => '2.0',
                    'method' => 'problem.get',
                    'params' => [
                        "hostids" => $val
                    ],
                    'auth' => $auth_key,
                    'id' => 1,
                ];
                $datas = json_encode([$data]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($curl);
                $result = json_decode($result);
             
                if(isset($result) && is_array($result)){
                    foreach($result as $key => $error){
                        foreach($error->result as $pushcode){
                            $pushcode->host_id = $val;
                        }
                    }      
                      curl_close($curl);
                      array_push($errorarray,$result[0]->result);  
  
                }
       
            }
            // echo "<pre/>";
            // print_r(array_filter($errorarray));
            // exit;
            return $errorarray;
    }
}