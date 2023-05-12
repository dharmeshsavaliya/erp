<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinterestAdsAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_ads_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_mail_id');
            $table->string('ads_account_id');
            $table->string('ads_account_name');
            $table->string('ads_account_country');
            $table->string('ads_account_currency');
            $table->foreign('pinterest_mail_id')->references('id')->on('pinterest_business_account_mails')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('pinterest_ads_accounts');
    }
}
