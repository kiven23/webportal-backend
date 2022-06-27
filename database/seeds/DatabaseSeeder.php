<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(BranchesTableSeeder::class);
        $this->call(MessageCastSettingsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(AccessLevelsTableSeeder::class);
        $this->call(NewSeeds::class);
        $this->call(Seeds11262019::class);
        $this->call(Seeder12022019::class);
        $this->call(Seeder12052019::class);
    }
}
