<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpressWayDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_way_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->text('uid');
            $table->text('driver');
            $table->text('department');
            $table->text('brand');
            $table->text('model');
            $table->text('plate');
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
        Schema::dropIfExists('express_way_drivers');
    }
}
