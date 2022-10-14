<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archiveds', function (Blueprint $table) {
            $table->increments('id');
            $table->text('tct_no');
            $table->text('tct_data');
            $table->text('location');
            $table->text('date_aquired');
            $table->text('area');
            $table->text('tax_dec_no');
            $table->text('tax_dec_option');
            $table->text('tax_dec_data');
            $table->text('owner');
            $table->text('previous_owner');
            $table->text('deed_of_sale_data');
            $table->text('real_property_tax_no');
            $table->text('real_property_tax_date');
            $table->text('real_property_tax_amount');
            $table->text('real_property_tax_option');
            $table->text('real_property_tax_data');
            $table->text('zonal_value');
            $table->text('vicinity_map_data');
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
        Schema::dropIfExists('archiveds');
    }
}
