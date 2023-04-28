<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierPriorityTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_priority', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_priority');
    }
}
