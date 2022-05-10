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
                'currency_id' => 2,
                'price' => 300,
            ],
            'min_price' => [
                'currency_id' => 1,
                'price' => 3400000,
            ],
            'max_price' => [
                'currency_id' => 1,
                'price' => 4500000,
            ],
            'whole_price' => [
                'currency_id' => 2,
                'price' => 320,
            ],
        ]);
        Product::Create([
            'category_id' => 1,
            'name' => 'Redmi Note 11 Pro Max+',
            'brand' => 'Xiomi',
            'cost_price' => [
                'currency_id' => 2,
                'price' => 450,
            ],
            'min_price' => [
                'currency_id' => 1,
                'price' => 4850000,
            ],
            'max_price' => [
                'currency_id' => 1,
                'price' => 5400000,
            ],
            'whole_price' => [
                'currency_id' => 2,
                'price' => 455,
            ],
        ]);

        Warehouse::create([
            'product_id' => 1,
            'unit_id' => 1,
            'count' => 100,
            'codes' => ['ss' => 350],
            'date' => Carbon::today(),
            'active' => true,
        ]);
        Warehouse::create([
            'product_id' => 2,
            'unit_id' => 1,
            'count' => 1000,
            'codes' => ['jj' => 350],
            'date' => Carbon::today(),
            'active' => true,
        ]);

    }
}
