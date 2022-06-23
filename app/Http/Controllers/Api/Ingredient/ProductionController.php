<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Models\IngredientOrder;
use App\Models\IngredientBasket;
use App\Models\IngredientProduct;
use App\Models\IngredientWarehouse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Requests\ProductionCreateBasketRequest;

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

    public function createBasket(ProductionCreateBasketRequest $request)
    {
        $warehouses = IngredientWarehouse::where('active', true)->get();
        $final = [];
        $used_ingredients = [];
        $production = true;
        foreach ($request['products'] as $item) {
            $ingredients = IngredientProduct::where('product_id', $item['product_id'])->get(['ingredient_id as id', 'count']);
            $product = Product::find($item['product_id']);
            $temp = [
                'product_id'=> $product->id,
                'count'=> $item['count'],
                'ingredients'=>[]
            ];
            if ($production == false) {
                break;
            }
            for ($i = 0; $i < count($ingredients); $i++) {
                $ingredient = $ingredients[$i];
                $qty = $ingredient->count*$item['count'];
                $warehouse = $warehouses->where('count', '!=', 0)->where('ingredient_id', $ingredient->id)->first();
                if (!$warehouse) {
                    $production = false;
                    break;
                }
                $tempItem = [
                    'warehouse_id'=> $warehouse->id,
                    'ingredient_id'=> $warehouse->ingredient_id,
                    'count'=> 0,
                    'price'=> $warehouse->cost_price,
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
        if ($production == false) {
            return ApiResponse::error('error', 409);
        }
        $basket = IngredientBasket::create([
            'deadline'=> $request->deadline,
            'active'=> true
        ]);
        foreach ($final as $itemFinal) {
            foreach ($itemFinal['ingredients'] as $itemIngredient) {
                $warehouse = IngredientWarehouse::find($itemIngredient['warehouse_id']);
                if ($warehouse->count -  $itemIngredient['count'] == 0) {
                    $warehouse->active = false;
                }
                $warehouse->count = $warehouse->count -  $itemIngredient['count'];
                $warehouse->save();
                $itemIngredient['usd_rate'] = $warehouse->basket->usd_rate;
            }
            IngredientOrder::create([
                'ingredient_basket_id'=> $basket->id,
                'product_id'=> $itemFinal['product_id'],
                'count'=> $itemFinal['count'],
                'ingredients'=> $itemFinal['ingredients']
            ]);
        }
        return ApiResponse::success();
    }

    public function baskets(Request $request)
    {
        $baskets = IngredientBasket::where('active', true)->paginate(15);
        $final = [
            'last_page'=> $baskets->lastPage(),
            'data'=>[]
        ];

        foreach ($baskets as $basket) {
            $orders = [];
            foreach ($basket->orders as $order) {
                $ingredients = [];
                foreach ($order->ingredients as $ingredient) {
                    $ingredients[] = [
                        'ingredient_id'=> $ingredient['ingredient_id'],
                        'ingredient_name'=> Ingredient::where('id', $ingredient['ingredient_id'])->withTrashed()->first()->name,
                        'usd_rate'=> 11000,
                        'price'=> $ingredient['price'],
                        'count'=> $ingredient['count']
                    ];
                }
                $orders[] = [
                    'product_id'=> $order->product_id,
                    'product_name'=> $order->product->namecd,
                    'count'=> $order->count,
                    'ingredients'=> $ingredients
                ];
            }

            $temp = [
                'basket_id'=> $basket->id,
                'deadline'=> $basket->deadline,

                'orders'=> $orders
            ];

            $final['data'][] = $temp;
        }
        return ApiResponse::success(data:$final);
    }

    public function histories(Request $request)
    {
        $baskets = IngredientBasket::where('active', false)->paginate(30);
        $final = [
            'last_page'=> $baskets->lastPage(),
            'data'=>[]
        ];

        foreach ($baskets as $basket) {
            $final['data'][] = [
                'basket_id'=> $basket->id,
                'deadline'=> $basket->deadline,
            ];
        }
        return ApiResponse::success(data:$final);
    }
    public function finshed(IngredientBasket $basket)
    {
        $basket->update([
            'active'=> false
        ]);
        return ApiResponse::success();
    }
    public function orders(IngredientBasket $basket)
    {
        $final = [];
        foreach ($basket->orders as $order) {
            $ingredients = [];
            foreach ($order->ingredients as $ingredient) {
                $ingredients[] = [
                    'ingredient_id'=> $ingredient['ingredient_id'],
                    'ingredient_name'=> Ingredient::where('id', $ingredient['ingredient_id'])->withTrashed()->first()->name,
                    'price'=> $ingredient['price'],
                    'count'=> $ingredient['count']
                ];
            }
            $final[] = [
                'product_id'=> $order->product_id,
                'product_name'=> $order->product->namecd,
                'count'=> $order->count,
                'ingredients'=> $ingredients
            ];
        }
        return ApiResponse::success(data:$final);
    }

    public function products(Request $request)
    {
        $category_id = $request->category_id;
        $search = $request->search;
        $products = Product::when($category_id, function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })->when($search, function ($query) use ($search) {
            if ($search[0] == '#') {
                $search = str_replace('#', '', $search);
                $query->where('id', $search);
            } else {
                $query->where('name', 'like', '%' . $search . '%');
            }
        })->whereHas('Ingredients', function ($query) {
            return $query;
        })->orderBy('id', 'desc');
        $products = $products->paginate(30);
        $final = [
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'last_page' => $products->lastPage(),
            'data' => [],
        ];
        $temp = [];
        foreach ($products as $product) {
            $id = $product->id;
            $category = $product->category;
            $temp = [
                'id' => $id,
                'category' => [
                    'id' => $product->category_id,
                    'name'=> $category->name
                ],
                'image' => $product->image,
                'name' => $product->name,
                'brand' => $product->brand,
            ];
            $final['data'][] = $temp;
        }

        return ApiResponse::success(data:$final);
    }
}
