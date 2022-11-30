<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwilioActiveNumberIdToTwilioCallForwarding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_call_forwarding', function (Blueprint $table) {
            $table->unsignedInteger('twilio_active_number_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_call_forwarding', function (Blueprint $table) {
            $table->dropColumn('twilio_active_number_id');
        });
    }
}
