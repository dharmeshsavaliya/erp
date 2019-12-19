<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MostUsedWordsInChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:most-used-words';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to admin if scraper is not running.';

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
        // start to get the most used words from chat messages 
        $mostUsedWords = \App\Helpers\MessageHelper::getMostUsedWords();
        \App\ChatMessageWord::truncate();

        if(!empty($mostUsedWords)) {
              \App\ChatMessageWord::insert($mostUsedWords);          
        }

    }
}
