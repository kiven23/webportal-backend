<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerDigitizedReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_digitized_reqs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doc_id');
            $table->string('customer_name');
            $table->string('birthday');
            $table->string('branch');
            $table->string('unit_availed');
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
        Schema::dropIfExists('customer_digitized_reqs');
    }
}
