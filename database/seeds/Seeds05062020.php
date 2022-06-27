<?php

use Illuminate\Database\Seeder;

class Seeds05062020 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'Import Customers',
            'guard_name' => 'web',
        ]);
    }
}
