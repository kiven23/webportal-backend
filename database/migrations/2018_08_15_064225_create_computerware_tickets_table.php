<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputerwareTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computerware_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_number')->nullable();
            $table->integer('user_id');
            $table->integer('branch_id');
            $table->integer('product_item_id');
            $table->string('product_item_serial_number')->nullable();
            $table->string('reported_by_name');
            $table->string('reported_by_position');
            $table->text('problem');
            $table->datetime('date_closed')->nullable();
            $table->text('assigned_tech')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('report_status');
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
        Schema::dropIfExists('computerware_tickets');
    }
}
