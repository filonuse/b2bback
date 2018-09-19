<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Role::class)->create(['name' => \App\Enums\RoleType::ADMIN]);
        factory(\App\Models\Role::class)->create(['name' => \App\Enums\RoleType::PROVIDER]);
        factory(\App\Models\Role::class)->create(['name' => \App\Enums\RoleType::CUSTOMER]);
    }
}
