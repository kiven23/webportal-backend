<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectivityTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connectivity_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_number')->nullable();
            $table->integer('user_id');
            $table->integer('branch_id');
            $table->integer('service_provider_id');
            $table->integer('service_type_id');
            $table->integer('service_category_id');
            $table->string('provider_ticket')->nullable();
            $table->string('reported_by_name');
            $table->string('reported_by_position')->nullable();
            $table->text('problem');
            $table->datetime('problem_reported_ho');
            $table->datetime('problem_reported_isp')->nullable();
            $table->datetime('resolution_reported')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('updated_by');
            $table->integer('confirmed_by')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('connectivity_tickets');
    }
}
