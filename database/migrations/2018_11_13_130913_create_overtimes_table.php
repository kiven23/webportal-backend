<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('accesschart_id')->nullable();
            $table->integer('remarks_by')->nullable();
            $table->integer('waiting_for')->nullable();
            $table->datetime('date_from');
            $table->datetime('date_to')->nullable();
            $table->longtext('reason')->nullable();
            $table->longtext('remarks')->nullable();
            $table->integer('status')->nullable();
            $table->integer('working_dayoff')->nullable();
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
        Schema::dropIfExists('overtimes');
    }
}
