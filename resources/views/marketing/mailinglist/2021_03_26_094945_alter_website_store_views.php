<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWebsiteStoreViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_store_views', function (Blueprint $table) {
            $table->integer('site_submit_webmaster')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_store_views', function (Blueprint $table) {
            $table->dropField('site_submit_webmaster');
        });
    }
}
