<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Models\IngredientProduct;
use App\Models\IngredientWarehouse;
use App\Models\Product;
use Illuminate\Http\Request;

// $temp['ingredients'][] = [
//     'warehouse_id'=> 0,
//     'ingredient_id'=> $ingredient->ingredient_id,
//     'ingredient_name'=> $ingredient->ingredient->name,
//     'count'=> isset($used_ingredients[$product->id][$ingredient->ingredient_id]) ? $qty - $used_ingredients[$product->id][$ingredient->ingredient_id]: $qty,
//     'price'=> 0,
//     'ordered_at'=> null,
//     'status'=> 'not enough'
// ];

class ProductionController extends Controller
{
    public function Production(ProductionRequest $request)
    {
        $warehouses = IngredientWarehouse::where('active', true)->get();
        $final = [];
        $used_ingredients = [];

        foreach ($request->all() as $item) {
            $ingredients = IngredientProduct::where('product_id', $item['product_id'])->get(['ingredient_id', 'count']);
            $product = Product::find($item['product_id']);
            $temp = [
                'product_id'=> $product->id,
                'product_name'=> $product->name,
                'count'=> $item['count'],
                'ingredients'=>[]
            ];
            for ($i = 0; $i < count($ingredients); $i++) {
                $ingredient = $ingredients[$i];
                $qty = $ingredient->count*$item['count'];
                $warehouse = $warehouses->where('count', '!=', 0)->where('ingredient_id', $ingredient->ingredient_id)->first();
                if (!$warehouse) {
                    continue;
                }
                if ($warehouse->count - $qty >= 0) {
                    if (isset($used_ingredients[$product->id][$warehouse->ingredient_id])) {
                        $temp_qty = ($qty - $used_ingredients[$product->id][$warehouse->ingredient_id]);
                        $warehouse->count -= $temp_qty;
                        $used_ingredients[$product->id][$warehouse->ingredient_id] += $temp_qty;
                    } else {
                        $warehouse->count -= $qty;
                        $used_ingredients[$product->id][$warehouse->ingredient_id] = $qty;
                    }

                    $temp['ingredients'][] = [
                        'warehouse_id'=> $warehouse->id,
                        'ingredient_id'=> $warehouse->ingredient_id,
                        'ingredient_name'=> $warehouse->ingredient->name,
                        'count'=> $qty - ($temp_qty ?? 0),
                        'price'=> $warehouse->cost_price,
                        'ordered_at'=> $warehouse->ordered_at,
                        'status'=> 'enough'
                    ];
                } else {
                    if (isset($used_ingredients[$warehouse->ingredient_id])) {
                        $used_ingredients[$product->id][$warehouse->ingredient_id] += $warehouse->count;
                    } else {
                        $used_ingredients[$product->id][$warehouse->ingredient_id] = $warehouse->count;
                    }
                    $temp['ingredients'][] = [
                        'warehouse_id'=> $warehouse->id,
                        'ingredient_id'=> $warehouse->ingredient_id,
                        'ingredient_name'=> $warehouse->ingredient->name,
                        'count'=> $warehouse->count,
                        'price'=> $warehouse->cost_price,
                        'ordered_at'=> $warehouse->ordered_at,
                        'status'=> 'enough'
                    ];
                    $warehouse->count = 0;
                    $i--;
                }
            }
            $final[] = $temp;
            $temp = [];
        }
        return ApiResponse::success(data:$final);
    }
}
