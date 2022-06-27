<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageCastSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_cast_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('pass');
            $table->string('from');
            $table->string('send_url');
            $table->string('to')->nullable();
            $table->longtext('msg')->nullable();
            $table->integer('msgid')->nullable();
            $table->integer('port')->nullable();
            $table->string('wapurl')->nullable();
            $table->string('dtefrom')->nullable();
            $table->string('dteto')->nullable();
            $table->string('msisdn')->nullable();
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
        Schema::dropIfExists('message_cast_settings');
    }
}
