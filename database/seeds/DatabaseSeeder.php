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
        $this->call(CategoriesTableSeeder::class);
        $this->call(StatusesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(CreateAdminUsersTableSeeder::class);

        // For testing
        $this->call(UsersTableSeeder::class);
        $this->call(GoodsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(ReviewsTableSeeder::class);
        $this->call(MessageTableSeeder::class);
        $this->call(AddressTableSeeder::class);
        $this->call(StoreTableSeeder::class);
        $this->call(NewsTableSeeder::class);
    }
}
