<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvFundAvailOnHandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_fund_avail_on_hands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_fund_id');
            $table->foreign('rv_fund_id')->references('id')->on('revolving_funds')->onDelete('cascade');
            $table->decimal('fund_on_hand', 8, 2);
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
        Schema::dropIfExists('rv_fund_avail_on_hands');
    }
}
