<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinterestBusinessAccountMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_business_account_mails', function (Blueprint $table) {
            $table->id();
            $table->string('pinterest_account');
            $table->bigInteger('pinterest_business_account_id');
            $table->text('pinterest_refresh_token');
            $table->text('pinterest_access_token');
            $table->integer('expires_in');
            $table->integer('refresh_token_expires_in');
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
        Schema::dropIfExists('pinterest_business_account_mails');
    }
}