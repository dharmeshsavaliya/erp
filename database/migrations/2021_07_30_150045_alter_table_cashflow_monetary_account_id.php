<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCashflowMonetaryAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("cash_flows",function(Blueprint $table) {
            $table->integer("monetary_account_id")->nullable()->after("cash_flow_able_type");
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
        Schema::table("cash_flows",function(Blueprint $table) {
            $table->dropField("monetary_account_id");
        });
    }
}
