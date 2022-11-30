<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableLogListMagentoImageNot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement("ALTER TABLE `log_list_magentos` CHANGE `sync_status` `sync_status` ENUM('success','error','waiting','started_push','size_chart_needed','image_not_found') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
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
