<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToApprovedLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approved_logs', function($table) {
            $table->integer('maint_request_id')->unsigned()->nullable();
            $table->integer('po_file_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approved_logs', function($table) {
            $table->dropColumn('maint_request_id');
            $table->dropColumn('po_file_id');
        });
    }
}
