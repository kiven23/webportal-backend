<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallMentLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_ment_ledgers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('DocEntry');
            $table->string('InstlmntID');
            $table->string('DueDate');
            $table->string('Status');
            $table->string('InsTotalSy');
            $table->string('PaidSys');
            $table->string('SapEndingBalance');
            $table->string('ManualEndingBalance');
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
        Schema::dropIfExists('install_ment_ledgers');
    }
}
