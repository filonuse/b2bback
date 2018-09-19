<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Ноутбуки и компьютеры'],
            ['name' => 'Товары для дома'],
            ['name' => 'Бытовая техника'],
            ['name' => 'Инструменты и автотовары'],
            ['name' => 'Дача, сад и огород'],
            ['name' => 'Спорт и увлечения'],
            ['name' => 'Одежда, обувь и украшения'],
            ['name' => 'Детские товары'],
            ['name' => 'Канцтовары и книги'],
            ['name' => 'Товары для бизнеса'],
        ];

        foreach ($data as $item) {
            \App\Models\Category::create($item);
        }
    }
}
