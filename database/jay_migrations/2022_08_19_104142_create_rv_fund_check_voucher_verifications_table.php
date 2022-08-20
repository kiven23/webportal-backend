<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvFundCheckVoucherVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_fund_check_voucher_verifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_fund_id');
            $table->foreign('rv_fund_id')->references('id')->on('revolving_funds')->onDelete('cascade');
            $table->date('date_transmitted');
            $table->string('ck_no', 100);
            $table->string('status', 50);
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
        Schema::dropIfExists('rv_fund_check_voucher_verifications');
    }
}
