<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\DatabaseHistoricalRecord;

class AddDatabaseHistoricalData extends Command
{

    CONST MAX_REACH_LIMIT = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:historical-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert historical data';

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
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // get the historical data and store into the new table
            $db = \DB::select('SELECT table_schema as "db_name",Round(Sum(data_length + index_length) / 1024 / 1024, 1) as "db_size"
                FROM information_schema.tables  where table_schema = "'.env('DB_DATABASE', 'solo').'" GROUP  BY table_schema'
            );

            $lastDb = DatabaseHistoricalRecord::where("database_name",env('DB_DATABASE', 'solo'))->latest()->first();

            if(!empty($db)) {
                foreach($db as $d) {

                    // check the last db size and current size and manage with it 
                    if($lastDb) {
                        if($lastDb->database_name == $d->db_name) {
                            if(($d->db_size - $lastDb->size) >= self::MAX_REACH_LIMIT) {
                                \App\CronJob::insertLastError($this->signature,
                                    "Database is reached to the max limit : ".self::MAX_REACH_LIMIT
                                );
                            }
                        }
                    }

                    DatabaseHistoricalRecord::create([
                        "database_name" => $d->db_name,
                        "size" => $d->db_size,
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
