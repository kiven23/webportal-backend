<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RvFundExpensesPreparationHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_fund_expenses_for_check_preparations_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_fund_id');
           
            $table->date('pcv_date');
            $table->string('particulars');
            $table->decimal('amount', 8, 2);
            $table->string('tin');
            $table->string('status');
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
