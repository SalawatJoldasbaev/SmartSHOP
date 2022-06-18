<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Models\Ingredient;
use App\Models\IngredientProduct;
use App\Models\IngredientWarehouse;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function Production(ProductionRequest $request)
    {
        $warehouses = IngredientWarehouse::where('active', true)->get();
        $final = [];
        $used_ingredients = [];
        foreach ($request->all() as $item) {
            $ingredients = IngredientProduct::where('product_id', $item['product_id'])->get(['ingredient_id as id', 'count']);
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
                $warehouse = $warehouses->where('count', '!=', 0)->where('ingredient_id', $ingredient->id)->first();
                if (!$warehouse) {
                    $temp['ingredients'][] = [
                        'warehouse_id'=> 0,
                        'ingredient_id'=> $ingredient->id,
                        'ingredient_name'=> Ingredient::find($ingredient->id)->name,
                        'count'=> isset($used_ingredients[$product->id][$ingredient->id]) ?$qty-$used_ingredients[$product->id][$ingredient->id]: $qty,
                        'price'=> 0,
                        'ordered_at'=> null,
                        'status'=> 'not enough'
                    ];
                    continue;
                }
                $tempItem = [
                    'warehouse_id'=> $warehouse->id,
                    'ingredient_id'=> $warehouse->ingredient_id,
                    'ingredient_name'=> $warehouse->ingredient->name,
                    'count'=> 0,
                    'price'=> $warehouse->cost_price,
                    'ordered_at'=> date_format($warehouse->basket->created_at, 'Y-m-d H:i:s'),
                    'status'=> 'enough'
                ];
                if ($warehouse->count - $qty >= 0) {
                    $this->cal($qty, $used_ingredients, $product, $ingredient);
                    $warehouse->count -= $qty;
                    $tempItem['count'] = $qty;
                    $temp['ingredients'][] = $tempItem;
                } else {
                    $this->cal($qty, $used_ingredients, $product, $ingredient, $warehouse->count);
                    $tempItem['count'] = $warehouse->count;
                    $temp['ingredients'][] = $tempItem;
                    $warehouse->count = 0;
                    if ($ingredient->count*$item['count'] != $used_ingredients[$product->id][$ingredient->id]) {
                        $i--;
                    }
                }
            }
            $final[] = $temp;
        }

        return $final;
    }

    private function cal(&$qty, &$used_ingredients, $product, $ingredient, $min= null)
    {
        if (isset($used_ingredients[$product->id][$ingredient->id])) {
            $qty  -= $used_ingredients[$product->id][$ingredient->id];
            $used_ingredients[$product->id][$ingredient->id] += $min?? $qty;
        } else {
            $used_ingredients[$product->id][$ingredient->id] = $min?? $qty;
        }
    }
}
