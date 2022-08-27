<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvFundExpensesForCheckPreparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_fund_expenses_for_check_preparations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_fund_id');
            $table->foreign('rv_fund_id')->references('id')->on('revolving_funds')->onDelete('cascade');
            $table->date('pcv_date');
            $table->string('particulars');
            $table->decimal('amount', 8, 2);
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
        Schema::dropIfExists('rv_fund_expenses_for_check_preparations');
    }
}
