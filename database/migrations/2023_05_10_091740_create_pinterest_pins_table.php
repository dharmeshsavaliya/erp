<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinterestPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_ads_account_id');
            $table->string('pin_id');
            $table->string('link')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedBigInteger('pinterest_board_id');
            $table->unsignedBigInteger('pinterest_board_section_id')->nullable();
            $table->json('media_source');
            $table->foreign('pinterest_board_section_id')->references('id')->on('pinterest_board_sections')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('pinterest_board_id')->references('id')->on('pinterest_boards')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('pinterest_ads_account_id')->references('id')->on('pinterest_ads_accounts')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('pinterest_pins');
    }
}
