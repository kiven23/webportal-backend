<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessChartUserMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_chart_user_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accesschart_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('access_level')->unsigned()->nullable();
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
        Schema::dropIfExists('access_chart_user_maps');
    }
}
