<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Category;

class DeleteCategoriesWithNoProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-categories:with-no-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete categories with no products';

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
        $unKnownCategory  = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $neededCategories = [];
        if ($unKnownCategory) {
            $unKnownCategories = explode(',', $unKnownCategory->references);
            $unKnownCategories = array_unique($unKnownCategories);
            if (!empty($unKnownCategories)) {
                foreach ($unKnownCategories as $unKnownCategory) {
                    $count = \App\Category::ScrapedProducts($unKnownCategory);
                    if ($count > 1) {
                        $neededCategories[] = $unKnownCategory;
                        echo "Added in  {$unKnownCategory} categories";
                        echo  PHP_EOL;
                    }else{
                        echo "removed from  {$unKnownCategory} categories";
                        echo  PHP_EOL;
                    }
                }

                $unKnownCategory->references = implode(",",array_filter($neededCategories));
                $unKnownCategory->save();
            }
        }
    }
}
