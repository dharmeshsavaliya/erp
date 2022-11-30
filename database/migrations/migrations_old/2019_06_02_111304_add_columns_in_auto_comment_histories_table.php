<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInAutoCommentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_comment_histories', function (Blueprint $table) {
            $table->integer('status')->default(1);
            $table->string('caption')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_comment_histories', function (Blueprint $table) {
            $table->dropColumn(['is_commented', 'caption']);
        });
    }
}
