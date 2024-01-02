<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeDoctorAccountsRemarksHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_accounts_remarks_history', function (Blueprint $table) {
            $table->id();
            $table->integer('time_doctor_account_id');
            $table->integer('user_id');
            $table->text('old_remark')->nullable();
            $table->text('new_remark')->nullable();
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
        Schema::dropIfExists('time_doctor_accounts_remarks_history');
    }
}
