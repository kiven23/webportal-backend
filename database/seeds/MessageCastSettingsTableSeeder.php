<?php

use Illuminate\Database\Seeder;

class MessageCastSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('message_cast_settings')->insert([
            'user' => 'ADDESSA',
            'pass' => 'MPoq5g7y',
            'from' => 'ADDESSA',
            'send_url' => 'http://mcpro1.sun-solutions.ph/mc/send.aspx?',
        ]);
    }
}
