<?php

use Illuminate\Database\Seeder;

class Seeds11262019 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'View Purchasing Reports',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Show Purchasing Reports',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Create Purchasing Reports',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Purchasing Reports',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Purchasing Reports',
            'guard_name' => 'web',
        ]);

        DB::table('roles')->insert([
            'name' => 'Purchasing Report Admin',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Purchasing Report User',
            'guard_name' => 'web',
        ]);

        DB::table('permissions')->insert([
            'name' => 'Show File Types',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Create File Types',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit File Types',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete File Types',
            'guard_name' => 'web',
        ]);

        DB::table('roles')->insert([
            'name' => 'File Type Admin',
            'guard_name' => 'web',
        ]);
    }
}
