<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToScraperProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraped_products', function (Blueprint $table) {
            $table->integer('product_id')->nullable()->index()->after('sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraped_products', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
