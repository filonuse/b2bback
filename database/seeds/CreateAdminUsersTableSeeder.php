<?php

use Illuminate\Database\Seeder;

class CreateAdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role  = \App\Models\Role::where('name', \App\Enums\RoleType::ADMIN)->first();

        // ADMINISTRATOR
        $admin = \App\Models\User::create([
            'name'          => 'admin',
            'legal_name'    => 'administrator',
            'email'         => 'admin@example.com',
            'password'      => bcrypt('admin'),
            'phone'         => '+380123456789',
            'official_data' => 'administrator',
            'requisites'    => 'administrator',
        ]);

        $admin->roles()->attach($role->id);
    }
}
