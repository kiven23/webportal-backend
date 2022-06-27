<?php

use Illuminate\Database\Seeder;

class NewSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'Complete Interview Schedules',
            'guard_name' => 'web',
        ]);
    }
}
