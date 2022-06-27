<?php

use Illuminate\Database\Seeder;

class Seeder12022019 extends Seeder
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
            'name' => 'Purchase Order User',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Purchase Order Approver',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Purchase Order Admin',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Purchase Order Super Admin',
            'guard_name' => 'web',
        ]);

        // permissions
        DB::table('permissions')->insert([
            'name' => 'Show Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'View Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'View Approved Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Create Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Approve Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Reject Purchase Order Files',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Overlook Purchase Order Files',
            'guard_name' => 'web',
        ]);
    }
}
