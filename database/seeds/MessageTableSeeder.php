<?php

use Illuminate\Database\Seeder;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::where('id', '!=', 1)->get();

        factory(\App\Models\Message::class, 200)->create()->each(function ($m) use($users){
            $u = $users->random(2);

            $m->sender()->attach([$u[0]->id => ['to_user_id' => $u[1]->id]]);

            factory(\App\Models\Message::class, 1)->create()->each(function ($m) use($u){
                $m->recipients()->attach([$u[0]->id => ['from_user_id' => $u[1]->id]]);
            });
        });
    }
}
