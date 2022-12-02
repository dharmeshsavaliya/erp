<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullScrapeColumnInScrapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scrapers', function (Blueprint $table) {
            $table->tinyInteger('full_scrape')->default(0)->after('scraper_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scrapers', function (Blueprint $table) {
            $table->dropColumn('full_scrape');
        });
    }
}
