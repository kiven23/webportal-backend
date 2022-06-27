<?php

use Illuminate\Database\Seeder;

class Seeder12052019 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // roles
        DB::table('roles')->insert([
            'name' => 'Announcement Admin',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Announcement User',
            'guard_name' => 'web',
        ]);

        // permissions
        DB::table('permissions')->insert([
            'name' => 'Show Announcements',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Create Announcements',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Announcements',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Announcements',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'View Announcements',
            'guard_name' => 'web',
        ]);
    }
}
