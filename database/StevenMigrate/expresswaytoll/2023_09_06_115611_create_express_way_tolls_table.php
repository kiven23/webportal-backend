<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpressWayTollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_way_tolls', function (Blueprint $table) {
            $table->increments('id');
            $table->text('uid');
            $table->text('posted');
            $table->text('pay');
            $table->text('toll1');
            $table->text('toll2');
            $table->text('toll3');
            $table->text('toll4');
            $table->text('toll5');
            $table->text('toll6');
            $table->text('toll7');
            $table->text('toll8');
            $table->text('toll9');
            $table->text('data');
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
        Schema::dropIfExists('express_way_tolls');
    }
}
