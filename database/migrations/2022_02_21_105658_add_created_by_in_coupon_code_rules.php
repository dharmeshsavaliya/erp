<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByInCouponCodeRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('coupon_code_rules', function (Blueprint $table) {
            $table->integer('created_by')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('coupon_code_rules', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}