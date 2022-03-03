<?php

namespace App\Console\Commands;

use App\Scraper;
use App\ScraperProcess;
use App\ScrapLog;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ScrapperNotRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scrapper_not_run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $scraper_process = ScraperProcess::where("scraper_name", "!=", "")->orderBy('scraper_id', 'DESC')->groupBy('scraper_id')->get();
        $scraper_proc = [];
        foreach ($scraper_process as $key => $sp) {
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $sp->started_at);
            $from = \Carbon\Carbon::now();
            $diff_in_hours = $to->diffInMinutes($from);
            if ($diff_in_hours > 1440) {
                array_push($scraper_proc, $sp->scraper_id);
            }
        }
        $scrapers = Scraper::with(['latestScrapperProcess'])
            ->where("scraper_name", "!=", "")
            ->whereNotIn('id', $scraper_proc)
            ->get();

        foreach ($scrapers as $scrapperDetails) {
            $hasAssignedIssue = \App\DeveloperTask::where("scraper_id", $scrapperDetails->id)
                //->whereNotNull("assigned_to")
				->where("is_resolved", 0)->orderBy('id', 'desc')->first();
            if ($hasAssignedIssue != null and $hasAssignedIssue->assigned_to != null) {
                $userName = \App\User::where('id', $hasAssignedIssue->assigned_to)->pluck('name')->first();
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $hasAssignedIssue->id, 'message' => "Scraper didn't Run In Last 24 Hr", 'status' => 1]);
                $reason = "Scrapper process hasn't run yet";
                if(isset($scrapperDetails->latestScrapperProcess->started_at)){

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $scrapperDetails->latestScrapperProcess->started_at);
                    $from = \Carbon\Carbon::now();
                    $diff_in_hours = $to->diffInMinutes($from);
                    $reason="Create Date: ".$to." Current Date: ".$from." Time Difference: ". gmdate('H:i:s', $diff_in_hours);

                }

                ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Scraper didn't Run In Last 24 Hr", 'reason' => $reason]);
				
				//app('\App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
                try {
                    app('\App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Scraper didn't Run In Last 24 Hr message sent to " . $userName]);
                } catch (\Exception $e) {
                    \Log::error($e);
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Coundn't send message to " . $userName." due to ".$e->getMessage()]);
                }
            } else {
                ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Not assigned to any user"]);
            }
        }
    }
}
