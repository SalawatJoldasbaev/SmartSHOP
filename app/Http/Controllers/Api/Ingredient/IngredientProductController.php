<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientProductCreateRequest;
use App\Models\IngredientProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class IngredientProductController extends Controller
{
    public function create(IngredientProductCreateRequest $request)
    {
        foreach ($request->ingredients as $ingredient) {
            IngredientProduct::UpdateOrCreate([
                'product_id' => $request->product_id,
                'ingredient_id' => $ingredient['ingredient_id'],
            ], [
                'count' => $ingredient['count'],
            ]);
        }

        return ApiResponse::success();
    }

    public function index(Request $request, Product $product)
    {
        $ingredients = [];
        foreach ($product->ingredients as $position) {
            $ingredients[] = [
                'position_id' => $position->id,
                'ingredient' => [
                    'id' => $position->ingredient_id,
                    'name' => $position->ingredient->name,
                    'unit_id' => $position->ingredient->unit_id,
                ],
                'count' => $position->count,
            ];
        }

        return ApiResponse::success(data:$ingredients);
    }

    public function delete(Request $request, IngredientProduct $position)
    {
        $position->delete();

        return ApiResponse::success();
    }
}
