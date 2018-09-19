<?php

use Illuminate\Database\Seeder;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::query()
            ->whereHas('roles', function ($query) {
                return $query->where('name', '=', \App\Enums\RoleType::CUSTOMER);
            })->get();

        foreach ($users as $u) {
            factory(\App\Models\Store::class, rand(1, 5))->create([
                'user_id'    => $u->id,
            ]);
        }
    }
}
