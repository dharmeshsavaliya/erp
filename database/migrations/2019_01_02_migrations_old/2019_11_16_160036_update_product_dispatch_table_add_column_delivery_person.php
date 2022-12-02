<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductDispatchTableAddColumnDeliveryPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_dispatch', function ($table) {
            $table->string('delivery_person')->nullable()->after('modeof_shipment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_dispatch', function (Blueprint $table) {
            $table->dropColumn('delivery_person');
        });
    }
}
