<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::Create([
            'category_id' => 1,
            'name' => 'Redmi Note 10 Pro',
            'brand' => 'Xiomi',
            'cost_price' => [
                'currency_id' => 1,
                'price' => 10000,
            ],
            'min_price' => [
                'currency_id' => 1,
                'price' => 10000,
            ],
            'max_price' => [
                'currency_id' => 1,
                'price' => 15000,
            ],
            'whole_price' => [
                'currency_id' => 2,
                'price' => 10000,
            ],
        ]);

        Warehouse::create([
            'branch_id' => 1,
            'product_id' => 1,
            'unit_id' => 1,
            'count' => 350,
            'codes' => ['ss' => 350],
            'date' => Carbon::today(),
            'active' => true,
        ]);

    }
}
