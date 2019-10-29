<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CronJobReport;
use App\Services\Scrap\ToryScraper as Tory;

class ToryScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:tory-list';
    private $scraper;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @param Tory $scraper
     */
    public function __construct(Tory $scraper)
    {
        $this->scraper = $scraper;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
     ]);


        $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        if (strpos($letters, 'T') === false) {
            return;
        }
        $this->scraper->scrap();

          $report->update(['end_time' => Carbon:: now()]);
    }
}
