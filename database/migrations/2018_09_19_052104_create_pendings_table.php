<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('region'); // denormalized
            $table->string('branch'); // denormalized
            $table->date('docdate');
            $table->integer('ls_or');
            $table->integer('por');
            $table->integer('ls_ci');
            $table->integer('ci');
            $table->integer('ls_ch');
            $table->integer('ch');
            $table->integer('dep');
            $table->integer('cla');
            $table->integer('grpo');
            $table->integer('si');
            $table->integer('so');
            $table->integer('sts');
            $table->integer('disb');
            $table->integer('arcm');
            $table->integer('apcm');
            $table->integer('pint');
            $table->integer('rc_cash');
            $table->integer('sc');
            $table->longtext('reason');
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
        Schema::dropIfExists('pendings');
    }
}
