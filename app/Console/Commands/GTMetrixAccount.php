<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\StoreGTMetrixAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GTMetrixAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GT-metrix-account-credit-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT metrix get Account credit limits';

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
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        // Get site report
        $AccountData = StoreGTMetrixAccount::all();

        foreach ($AccountData as $value) {
            if (! empty($value->account_id)) {
                try {
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $value->account_id.':'.'',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ]);

                    $response = curl_exec($curl);

                    curl_close($curl);
                    // $stdClass = json_decode(json_encode($response));
                    $data = json_decode($response);
                    $credits = $data->data->attributes->api_credits;
                    // print_r($data->data->attributes->api_credits);
                    if ($credits != 0) {
                        StoreGTMetrixAccount::where('account_id', $value->account_id)
                        ->update(['status' => 'active']
                        );
                    } else {
                        StoreGTMetrixAccount::where('account_id', $value->account_id)
                        ->update(['status' => 'inactive']
                        );
                    }
                } catch (\Exception $e) {
                    $value->status = 'error';
                    $value->error = $e->getMessage();
                    $value->save();
                }
            }
        }

        \Log::info('GTMetrix :: Report cron complete ');
        $report->update(['end_time' => Carbon::now()]);

        /*} catch (\Exception $e) {
    \Log::error($this->signature.' :: '.$e->getMessage() );
    \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }*/
    }
}
