<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        try {
            $this->authorize('create', Product::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $validation = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable',
            'name' => 'required',
            'brand' => 'nullable',
            'cost_price' => 'required|array',
            'price_min' => 'required|array',
            'price_min.currency_id' => 'required|exists:units,id',
            'price_max' => 'required|array',
            'price_max.currency_id' => 'required|exists:units,id',
            'price_wholesale' => 'required|array',
            'price_wholesale.currency_id' => 'required|exists:currencies,id',
            'warehouse' => 'nullable|array',
            'warehouse.unit_id' => 'required_unless:warehouse,null|exists:units,id',
            'warehouse.count' => 'required_unless:warehouse,null|numeric|min:0',
        ]);
        if ($validation->fails()) {
            return ApiResponse::error($validation->errors()->first(), 422);
        }
        $product = Product::create([
            'category_id' => $request->category_id,
            'image' => $request->image,
            'name' => $request->name,
            'brand' => $request->brand,
            'cost_price' => $request->cost_price,
            'min_price' => $request->price_min,
            'max_price' => $request->price_max,
            'whole_price' => $request->price_wholesale,
        ]);

        if (isset($request->warehouse)) {
            $basket = WarehouseBasket::create([
                'employee_id' => $request->user()->id,
                'date' => Carbon::today()->format('Y-m-d'),
            ]);
            $warehouseOrder = WarehouseOrder::create([
                'warehouse_basket_id' => $basket->id,
                'product_id' => $product->id,
                'unit_id' => $request->warehouse['unit_id'],
                'count' => $request->warehouse['count'],
            ]);
            $warhouse = Warehouse::setWarehouse($product->id, $request->warehouse['count'], $request->warehouse['unit_id']);
        }
        return ApiResponse::success(data:$product);
    }

    public function index(Request $request)
    {
        $delete = $request->delete == "true" ? true : false;
        $category_id = $request->category_id;
        $search = $request->search;
        $count = $request->count;
        $products = Product::when($category_id, function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })->when($search, function ($query) use ($search) {
            if ($search[0] == '#') {
                $search = str_replace('#', '', $search);
                $query->where('id', $search);
            } else {
                $query->where('name', 'like', '%' . $search . '%');
            }
        })->orderBy('id', 'desc');
        if (isset($count)) {
            $products = $products->whereHas('warehouse', function ($query) use ($count) {
                $query->where('count', '>', $count);
            });
        }
        if ($delete) {
            $products = $products->withTrashed();
        }
        $products = $products->paginate(30);
        $final = [
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'last_page' => $products->lastPage(),
            'data' => [],
        ];
        $temp = [];
        $currency = Currency::all();
        $units = Unit::all();
        foreach ($products as $product) {
            $cost_price = $currency->where('id', $product->cost_price['currency_id'])->first();
            $min_price = $currency->where('id', $product->min_price['currency_id'])->first();
            $max_price = $currency->where('id', $product->max_price['currency_id'])->first();
            $whole_price = $currency->where('id', $product->whole_price['currency_id'])->first();
            $id = $product->id;
            $unit = $units->where('id', $product->warehouse?->unit_id)->first();
            if ($delete) {
                $category = $product->category()->withTrashed()->first();
            } else {
                $category = $product->category;
            }
            $temp = [
                'id' => $id,
                'category' => [
                    'id' => $product->category_id,
                    'min_percent' => $category->min_percent,
                    'max_percent' => $category->max_percent,
                    'whole_percent' => $category->whole_percent,
                    'min_product' => $category->min_product ?? 0,
                ],
                'image' => $product->image,
                'name' => $product->name,
                'brand' => $product->brand,
                'cost_price' => [
                    'currency_id' => $cost_price->id,
                    'name' => $cost_price->name,
                    'code' => $cost_price->code,
                    'price' => $product->cost_price['price'],
                ],
                'min_price' => [
                    'currency_id' => $min_price->id,
                    'name' => $min_price->name,
                    'code' => $min_price->code,
                    'price' => $product->min_price['price'],
                ],
                'max_price' => [
                    'currency_id' => $max_price->id,
                    'name' => $max_price->name,
                    'code' => $max_price->code,
                    'price' => $product->max_price['price'],
                ],
                'whole_price' => [
                    'currency_id' => $whole_price->id,
                    'name' => $whole_price->name,
                    'code' => $whole_price->code,
                    'price' => $product->whole_price['price'],
                ],
                'warehouse' => [
                    'unit' => [
                        'id' => $unit?->id,
                        'name' => $unit?->name,
                        'code' => $unit?->unit,
                    ],
                    'count' => $product->warehouse?->count,
                ],
                'qr_code_link' => route('qrcode', [
                    'uuid' => $product->uuid,
                    'type' => 'product',
                ]),
                'deleted_at' => $product->deleted_at,
            ];
            $final['data'][] = $temp;
        }
        return ApiResponse::success(data:$final);
    }

    public function update(Request $request)
    {
        try {
            $this->authorize('update', Product::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $validation = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'brand' => 'nullable',
            'image' => 'nullable',
            'cost_price' => 'required|array',
            'price_min' => 'required|array',
            'price_min.currency_id' => 'required|exists:units,id',
            'price_max' => 'required|array',
            'price_max.currency_id' => 'required|exists:units,id',
            'price_wholesale' => 'required|array',
            'price_wholesale.currency_id' => 'required|exists:currencies,id',
        ]);
        if ($validation->fails()) {
            return ApiResponse::error($validation->errors()->first(), 422);
        }

        try {
            $product = Product::findOrFail($request->product_id);
        } catch (\Throwable$th) {
            return ApiResponse::error('product not found', 404);
        }
        $product->update([
            'category_id' => $request->category_id,
            'image' => $request->image,
            'name' => $request->name,
            'brand' => $request->brand,
            'cost_price' => $request->cost_price,
            'min_price' => $request->price_min,
            'max_price' => $request->price_max,
            'whole_price' => $request->price_wholesale,
        ]);
        return ApiResponse::success();
    }

    public function delete($id)
    {
        try {
            $this->authorize('delete', Product::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $product = Product::findOrFail($id)->delete();
        return ApiResponse::success();
    }
}
