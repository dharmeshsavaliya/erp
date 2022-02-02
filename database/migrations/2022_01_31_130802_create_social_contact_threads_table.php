<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialContactThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_contact_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('social_contact_id');
            $table->string('sender_id', 50)->nullable();
            $table->string('recipient_id', 50)->nullable();
            $table->text('message_id')->nullable();
            $table->text('text')->nullable();
            $table->tinyInteger('type')->length(1);
            $table->datetime('sending_at');
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
        Schema::dropIfExists('social_contact_threads');
    }
}
