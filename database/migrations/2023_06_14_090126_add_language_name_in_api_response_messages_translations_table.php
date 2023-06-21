<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLanguageNameInApiResponseMessagesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->string("lang_name")->nullable()->after('lang_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->dropColumn("lang_name");
        });
    }
}
