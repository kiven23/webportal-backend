<?php

use Illuminate\Database\Seeder;

class AccessLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('access_levels')->insert([
            'level' => 2,
        ]);
    }
}
