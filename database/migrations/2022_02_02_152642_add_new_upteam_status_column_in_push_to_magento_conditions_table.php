<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewUpteamStatusColumnInPushToMagentoConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_to_magento_conditions', function (Blueprint $table) {
            $table->string('upteam_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_to_magento_conditions', function (Blueprint $table) {
            $table->dropColumn('upteam_status');
        });
    }
}