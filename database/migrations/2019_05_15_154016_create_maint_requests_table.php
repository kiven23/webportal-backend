<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maint_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->string('nature_concern');
            $table->longtext('remarks');
            $table->string('location');
            $table->datetime('ins_date')->nullable();
            $table->longtext('instruction')->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->string('req_no')->nullable();
            $table->integer('received_by')->unsigned()->nullable();
            $table->datetime('date_received')->nullable();
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
        Schema::dropIfExists('maint_requests');
    }
}
