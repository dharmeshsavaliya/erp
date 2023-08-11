<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGithubColumnsInBuildProcessHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('build_process_histories', function (Blueprint $table) {
            $table->bigInteger('github_organization_id')->after('build_name')->nullable();
            $table->bigInteger('github_repository_id')->after('github_organization_id')->nullable();
            $table->string('github_branch_state_name')->after('github_repository_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('build_process_histories', function (Blueprint $table) {
            $table->dropColumn('github_organization_id');
            $table->dropColumn('github_repository_id');
            $table->dropColumn('github_branch_state_name');
        });
    }
}
