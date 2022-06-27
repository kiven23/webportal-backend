<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFilesPart2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function($table) {
            $table->integer('po_accesschart_id')->unsigned()->nullable();
            $table->integer('remarks_by')->unsigned()->nullable();
            $table->text('remarks2')->nullable();
            $table->integer('waiting_for')->unsigned()->nullable();
            // 0 = pending, 1 = rejected, 2 = approved
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function($table) {
            $table->dropColumn('po_accesschart_id');
            $table->dropColumn('remarks_by');
            $table->dropColumn('remarks2');
            $table->dropColumn('waiting_for');
            $table->dropColumn('status');
        });
    }
}
