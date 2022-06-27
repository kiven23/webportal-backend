<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function($table) {
            $table->integer('from')->unsigned()->nullable();
            $table->integer('to')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('remarks')->nullable();
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
            $table->dropColumn('from');
            $table->dropColumn('to');
            $table->dropColumn('company_id');
            $table->dropColumn('remarks');
        });
    }
}
