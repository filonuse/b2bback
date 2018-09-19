<?php

use Illuminate\Database\Seeder;

class GoodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = \App\Models\User::query()
            ->whereHas('roles', function ($query) {
                return $query->where('name', '=', 'provider');
            })->get();

        foreach ($providers as $provider) {
            foreach ($provider->categories as $category) {
                factory(App\Models\Goods::class, rand(1, 5))->create([
                    'user_id'     => $provider->id,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
