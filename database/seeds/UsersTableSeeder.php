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
        $roles      = \App\Models\Role::all()->pluck('id', 'name');
        $categories = \App\Models\Category::all()->pluck('id', 'name');

        // CLIENTS
        factory(App\Models\User::class, 50)->create()->each(function ($u) use ($roles, $categories) {
            $rules = ['provider', 'customer'];
            $roleRand = $rules[array_rand($rules)];

            $u->roles()->attach($roles[$roleRand]);

            \App\Services\SettingService::saveToDefault($u);

            // The category is creating only for users with role is provider
            if ($roleRand == 'provider') {
                $num = rand(1, 3);
                $u->categories()->attach($categories->random($num));
            }
        });
    }
}
