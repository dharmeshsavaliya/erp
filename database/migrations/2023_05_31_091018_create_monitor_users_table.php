<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 64)->unique();
            $table->string('password', 255);
            $table->string('password_reset_hash', 64)->nullable();
            $table->bigInteger('password_reset_timestamp')->nullable();
            $table->string('rememberme_token', 64)->nullable();
            $table->unsignedTinyInteger('level')->default(20);
            $table->string('name', 255);
            $table->string('mobile', 15);
            $table->string('discord', 255);
            $table->string('pushover_key', 255);
            $table->string('pushover_device', 255);
            $table->string('webhook_url', 255);
            $table->string('webhook_json', 255)->default('{"text":"servermon: #message"}');
            $table->string('telegram_id', 255);
            $table->string('jabber', 255);
            $table->string('email', 255);
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
        Schema::dropIfExists('monitor_users');
    }
}