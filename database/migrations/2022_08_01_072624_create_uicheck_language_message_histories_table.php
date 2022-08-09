<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUicheckLanguageMessageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uicheck_language_message_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ui_languages_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('languages_id')->nullable();
            $table->integer('uicheck_id')->nullable();
            $table->string('message')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('uicheck_language_message_histories');
    }
}