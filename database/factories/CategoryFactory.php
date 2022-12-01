<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'parent_id' => 0,
            'name' => $this->faker->words(3, true),
            'min_percent' => rand(5, 10),
            'max_percent' => rand(10, 20),
            'whole_percent' => rand(6, 15),
        ];
    }
}
