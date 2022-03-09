<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallSidFieldsToTwilioDequeueCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_dequeue_calls', function (Blueprint $table) {
            $table->string('call_sid')->after('reservation_sid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_dequeue_calls', function (Blueprint $table) {
            $table->dropColumn('call_sid');
        });
    }
}
