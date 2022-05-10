<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'brand' => $this->faker->word(2),
            'cost_price' => [
                'currency_id' => 2,
                'price' => $this->faker->numberBetween($min = 15, $max = 45)
            ],
            'min_price' => [
                'currency_id' => 1,
                'price' => $this->faker->numberBetween($min = 300000, $max = 500000)
            ],
            'max_price' => [
                'currency_id' => 1,
                'price' => $this->faker->numberBetween($min = 500000, $max = 1500000)
            ],
            'whole_price' => [
                'currency_id' => 2,
                'price' => $this->faker->numberBetween($min = 30, $max = 50)
            ]
        ];
    }
}
