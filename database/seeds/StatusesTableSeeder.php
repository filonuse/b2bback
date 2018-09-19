<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('statuses')->insert([
            [
                'name' => \App\Enums\OrderStatus::PENDING,
                'type' => 'order'
            ],
            [
                'name' => \App\Enums\OrderStatus::PROCESSED,
                'type' => 'order'
            ],
            [
                'name' => \App\Enums\OrderStatus::SHIPPED,
                'type' => 'order'
            ],
            [
                'name' => \App\Enums\OrderStatus::ACCEPTED_CUSTOMER,
                'type' => 'order'
            ],
            [
                'name' => \App\Enums\OrderStatus::CANCELED,
                'type' => 'order'
            ],
        ]);
    }
}
