<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsFlaggedUrlScraperImagesTable extends Migration
{
   
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraper_imags',function($table){
        
            $table->integer('is_flaged_url')->index()->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraper_imags',function(Blueprint $table) {
            $table->dropField('is_flaged_url');
        });
    }
}
