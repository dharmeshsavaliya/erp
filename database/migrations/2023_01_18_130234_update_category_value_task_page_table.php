<?php

use Illuminate\Database\Migrations\Migration;

class UpdateCategoryValueTaskPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE `task_categories` SET `title` = 'All Select' WHERE `task_categories`.`id` = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
