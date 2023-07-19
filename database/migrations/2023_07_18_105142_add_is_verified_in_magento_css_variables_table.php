<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVerifiedInMagentoCssVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_css_variables', function (Blueprint $table) {
            $table->boolean("is_verified")->after("create_by");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_css_variables', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
}
