<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_translators', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('en');
            $table->text('es');
            $table->text('ru');
            $table->text('ko');
            $table->text('ja');
            $table->text('it');
            $table->text('de');
            $table->text('fr');
            $table->text('nl');
            $table->text('zh');
            $table->text('ar');
            $table->text('ur');
            $table->tinyInteger('status')->default(3)->comment('1.checked,2.unchecked,3.new');
            $table->integer('updated_by_user_id')->nullable();
            $table->string('updated_by_username')->nullable();
            $table->string('approved_by_username')->nullable();
            $table->integer('approved_by_user_id')->nullable();
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
        Schema::dropIfExists('csv_translators');
    }
}
