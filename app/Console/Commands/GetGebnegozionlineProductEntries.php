<?php

namespace App\Console\Commands;

use App\Services\Scrap\GebnegozionlineScraper;
use Illuminate\Console\Command;

class GetGebnegozionlineProductEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gebnegozionline:get-products-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GebnegozionlineScraper $scraper)
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
        $this->scraper->scrap();
    }
}
