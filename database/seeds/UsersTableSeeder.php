<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Alexander',
            'last_name' => 'Flores',
            'email' => 'alexela8882@gmail.com',
            'password' => bcrypt('M15@2dwin0n7y'),
        ]);

        DB::table('users')->insert([
            'first_name' => 'Abegail',
            'last_name' => 'Valdez',
            'email' => 'abegailvaldez@addessa.com',
            'password' => bcrypt('M15@2dwin0n7y'),
        ]);
    }
}
