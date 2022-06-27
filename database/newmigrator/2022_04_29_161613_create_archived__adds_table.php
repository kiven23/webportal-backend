<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivedAddsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archived__adds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reportid');
            $table->string('tct_no');
            $table->string('tct_data');
            $table->string('location');
            $table->string('date_aquired');
            $table->string('area');
            $table->string('tax_dec_no');
            $table->string('tax_dec_option');
            $table->string('tax_dec_data');
            $table->string('owner');
            $table->string('previous_owner');
            $table->string('deed_of_sale_data');
            $table->string('real_property_tax_no');
            $table->string('real_property_tax_date');
            $table->string('real_property_tax_amount');
            $table->string('real_property_tax_option');
            $table->string('real_property_tax_data');
            $table->string('zonal_value');
            $table->string('vicinity_map_data');
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
        Schema::dropIfExists('archived__adds');
    }
}
