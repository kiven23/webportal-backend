<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToConcerns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concerns', function($table) {
            $table->datetime('date_solved')->nullable();
            $table->longtext('resolution')->nullable();
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
        Schema::table('concerns', function($table) {
            $table->dropColumn('date_solved');
            $table->dropColumn('resolution');
            $table->dropColumn('status');
        });
    }
}
