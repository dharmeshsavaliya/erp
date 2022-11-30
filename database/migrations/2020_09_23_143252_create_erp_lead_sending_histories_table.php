<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpLeadSendingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_lead_sending_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('customer_id');
            $table->integer('lead_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_lead_sending_histories');
    }
}
