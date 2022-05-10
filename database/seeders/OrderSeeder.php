<?php

namespace Database\Seeders;

use App\Models\Basket;
use App\Models\Cashier;
use App\Models\Order;
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
            'card' => 4500000,
            'cash' => 3500000,
            'debt' => [
                'debt' => 11800000,
                'paid' => 0,
                'remaining' => 11800000,
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
                'count' => 2,
                'price' => 4500000,
            ],
            [
                'basket_id' => $basket->id,
                'user_id' => 2,
                'product_id' => 2,
                'unit_id' => 1,
                'count' => 2,
                'price' => 5400000,
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
        $balance = [
            'date' => Carbon::today(),
            'balance' => [
                'sum' => 8000000,
                'card' => 4500000,
                'cash' => 3500000,
            ],
            'profit' => -7600000,
        ];
        Cashier::create($balance);
    }
}
