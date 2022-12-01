<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCommunicationHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communication_histories', function (Blueprint $table) {
            $table->boolean('is_stopped')->default(0)->after('method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('communication_histories', function (Blueprint $table) {
            $table->dropColumn('is_stopped');
        });
    }
}
