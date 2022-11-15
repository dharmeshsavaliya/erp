<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeedbackCatIdToUsersFeedbackHrTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_feedback_hr_tickets', function (Blueprint $table) {
            $table->integer('feedback_cat_id')->nulable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_feedback_hr_tickets', function (Blueprint $table) {
            $table->dropColumn('feedback_cat_id');
        });
    }
}