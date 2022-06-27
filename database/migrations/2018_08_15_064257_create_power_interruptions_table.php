<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePowerInterruptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_interruptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('branch_id');
            $table->string('reported_by_name');
            $table->string('reported_by_position')->nullable();
            $table->datetime('problem_reported');
            $table->datetime('datetime_from');
            $table->datetime('datetime_to')->nullable();
            $table->longtext('remarks')->nullable();
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
        Schema::dropIfExists('power_interruptions');
    }
}
