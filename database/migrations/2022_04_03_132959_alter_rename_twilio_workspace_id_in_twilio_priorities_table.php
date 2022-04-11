<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterRenameTwilioWorkspaceIdInTwilioPrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::select("ALTER TABLE twilio_priorities CHANGE twilio_workspace_id account_id INT(11)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_priorities', function (Blueprint $table) {
            $table->renameColumn('account_id', 'twilio_workspace_id');
        });
    }
}
