<?php

namespace Database\Seeders;

use App\Models\Basket;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $basket = Basket::create([
            'user_id' => 2,
            'employee_id' => 1,
            'card' => 5000,
            'cash' => 0,
            'debt' => [
                'debt' => 10000,
                'paid' => 5000,
                'remaining' => 5000,
            ],
            'term' => Carbon::today()->add(1, 'day'),
            'description' => 'qoyishay',
        ]);
        $orders = [
            [
                'basket_id' => $basket->id,
                'user_id' => 2,
                'product_id' => 1,
                'unit_id' => 1,
                'count' => 1,
                'price' => 15000,
            ],
        ];

        Order::upsert($orders, [
            'basket_id',
            'user_id',
            'product_id',
            'unit_id',
            'count',
            'price',
        ]);
        User::find(2)->update([
            'balance' => -5000,
        ]);
    }
}
