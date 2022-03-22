<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFlagToWebsiteStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_stores', function (Blueprint $table) {
            $table->integer('is_flag')->nullable()->after('is_default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_stores', function (Blueprint $table) {
            $table->dropColumn('is_flag');
        });
    }
}
