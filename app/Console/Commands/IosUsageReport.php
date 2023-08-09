<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\AppUsageReport;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class IosUsageReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosUsageReport:check';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            // https://api.appfigures.com/v2/reports/usage?group_by=network&start_date=2023-02-13&end_date=2023-02-14&products=280598515284

            $username = env('APPFIGURE_USER_EMAIL');
            $password = env('APPFIGURE_USER_PASS');
            $key = base64_encode($username . ':' . $password);

            $group_by = 'network';
            $start_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
            $end_date = date('Y-m-d');
            $product_id = env('APPFIGURE_PRODUCT_ID');
            $ckey = env('APPFIGURE_CLIENT_KEY');
            // $app_name=env("APPFIGURE_APP_NAME");
            $array_app_name = explode(',', env('APPFIGURE_APP_NAME'));
            $i = 0;
            $array_app = explode(',', env('APPFIGURE_PRODUCT_ID'));
            foreach ($array_app as $app_value) {
                //Usage Report
                $curl = curl_init();
                $url = "https://api.appfigures.com/v2/reports/usage?group_by=' . $group_by . '&start_date=2019-01-01&end_date=' . $end_date . '&products=' . $app_value,";
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'X-Client-Key:' . $ckey,
                        'Authorization: Basic ' . $key,
                    ],
                ]);

                $result = curl_exec($curl);
                // print($result);
                $res = json_decode($result, true); //response decode
                // print_r($res);
                // print_r($res["apple:analytics"]);
                // print($res["apple:analytics"]["crashes"]);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), $res, $httpcode, \App\Console\Commands\IosUsageReport::class, 'handle');
                curl_close($curl);

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api call finished.']);
                print_r($res);

                if ($res) {
                    $r = new AppUsageReport();
                    $r->product_id = $array_app_name[$i] . ' [' . $product_id . ']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;
                    $r->crashes = $res['apple:analytics']['crashes'];
                    $r->sessions = $res['apple:analytics']['sessions'];
                    $r->app_store_views = $res['apple:analytics']['app_store_views'];
                    $r->unique_app_store_views = $res['apple:analytics']['unique_app_store_views'];
                    $r->daily_active_devices = $res['apple:analytics']['daily_active_devices'];
                    $r->monthly_active_devices = $res['apple:analytics']['monthly_active_devices'];
                    $r->paying_users = $res['apple:analytics']['paying_users'];
                    $r->impressions = $res['apple:analytics']['impressions'];
                    $r->unique_impressions = $res['apple:analytics']['unique_impressions'];
                    $r->uninstalls = $res['apple:analytics']['uninstalls'];
                    $r->avg_daily_active_devices = $res['apple:analytics']['avg_daily_active_devices'];
                    $r->avg_optin_rate = $res['apple:analytics']['avg_optin_rate'];
                    $r->storefront = $res['apple:analytics']['storefront'];
                    $r->store = $res['apple:analytics']['store'];
                    $r->save();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'App user report was added.']);

                    return $this->info('Usage Report added');
                } else {
                    return $this->info('Usage Report not generated');
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'App user report was not generated.']);
                }

                $i += 1;
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron job was ended.']);
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
