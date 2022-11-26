<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OnHandHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onhand_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_id');
            $table->string('revolvingfund');
            $table->string('cashadvance');
            $table->string('balance');
            $table->string('incoming');
            $table->string('outgoing');
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
        //
    }
}
