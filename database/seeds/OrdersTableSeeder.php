<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = \App\Models\User::query()
            ->whereHas('roles', function ($query) {
                return $query->where('name', '=', 'customer');
            })->get();

        $statuses = \App\Models\Status::all();

        foreach ($customers as $customer) {

            factory(\App\Models\Order::class, rand(1, 10))
                ->create([
                    'customer_id'       => $customer->id,
                    'provider_id'       => 0,
                    'current_status_id' => 0,
                    'amount'            => 0,
                ])
                ->each(function ($o) use ($statuses, $customer) {
                    $goods  = \App\Models\Goods::all()->random(rand(1, 5));
                    $status = $statuses->random(1)[0];
                    $amount = 0;
                    foreach ($goods as $good) {
                        $qty = rand(1, $good->quantity_available / 2);

                        \App\Models\Relationships\OrderGoods::create([
                            'order_id' => $o->id,
                            'goods_id' => $good->id,
                            'quantity' => $qty,
                            'price'    => $good->price,
                        ]);

                        if ($status->name !== 'canceled') {
                            $good->increment('quantity_reserve', $qty);
                            $good->decrement('quantity_available', $qty);
                        }

                        $amount += ($good->price * $qty);
                    }

                    $o->update([
                        'provider_id'       => $good->user_id,
                        'current_status_id' => $status->id,
                        'amount'            => $amount,
                    ]);

                    event(new \App\Events\Order\OrderCreated($o));
                });
        }
    }
}
