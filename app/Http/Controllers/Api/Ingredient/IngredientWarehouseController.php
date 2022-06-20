<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientWarehouseCreateRequest;
use App\Models\Ingredient;
use App\Models\IngredientWarehouse;
use App\Models\IngredientWarehouseBasket;
use App\Models\IngredientWarehouseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IngredientWarehouseController extends Controller
{
    public function create(IngredientWarehouseCreateRequest $request)
    {
        $ingredients = $request->ingredients;
        $basket = IngredientWarehouseBasket::create([
            'usd_rate'=> $request->usd_rate,
            'employee_id'=> $request->user()->id,
        ]);
        foreach ($ingredients as $ingredient) {
            IngredientWarehouseOrder::create([
                'ingredient_warehouse_basket_id'=> $basket->id,
                'ingredient_id'=> $ingredient['ingredient_id'],
                'count'=> $ingredient['count'],
                'price'=> $ingredient['price'],
            ]);
            IngredientWarehouse::create([
                'ingredient_warehouse_basket_id'=> $basket->id,
                'ingredient_id'=> $ingredient['ingredient_id'],
                'count'=> $ingredient['count'],
                'cost_price'=> $ingredient['price'],
                'ordered_at'=> Carbon::now(),
                'active'=> true
            ]);
        }
        return ApiResponse::success();
    }

    public function index(Request $request)
    {
        $ingredients = IngredientWarehouse::where('active', true)->get();
        $final = [];
        foreach ($ingredients as $ingredient) {
            $basket = IngredientWarehouseBasket::find($ingredient->ingredient_warehouse_basket_id);
            if (is_null($ingredient->ingredient)) {
                continue;
            }

            if (!array_key_exists($ingredient->ingredient_id, $final)) {
                $final[$ingredient->ingredient_id] = [
                    'ingredient_id'=> $ingredient->ingredient_id,
                    'ingredient_name'=> $ingredient->ingredient->name,
                    'unit_id'=> $ingredient->ingredient->unit_id,
                    'count'=> $ingredient->count,
                    'items'=>[
                        [
                            'count'=> $ingredient->count,
                            'price'=> $ingredient->cost_price,
                            'usd_rate'=> $basket->usd_rate,
                            'ordered_at'=> $basket->created_at
                        ]
                    ]
                ];
            } else {
                $final[$ingredient->ingredient_id]['count'] += $ingredient->count;
                $final[$ingredient->ingredient_id]['items'][] = [
                    'count'=> $ingredient->count,
                    'price'=> $ingredient->cost_price,
                    'usd_rate'=> $basket->usd_rate,
                    'ordered_at'=> $basket->created_at
                ];
            }
        }
        $ingredients = Ingredient::all();
        foreach ($ingredients as $ingredient) {
            if (!array_key_exists($ingredient->id, $final)) {
                $final[$ingredient->id] = [
                    'ingredient_id'=> $ingredient->id,
                    'ingredient_name'=> $ingredient->name,
                    'unit_id'=> $ingredient->unit_id,
                    'count'=> 0,
                    'items'=>[]
                ];
            }
        }
        return ApiResponse::data(true, 'success', array_values($final));
    }
}
