<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Forex;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;


class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'percents' => 'required|array',
            'percents.min' => 'required',
            'percents.max' => 'required',
            'percents.wholesale' => 'required',
            'percents.min_product' => 'required',
        ]);
        if ($validation->fails()) {
            return ApiResponse::error($validation->errors()->first(), 422);
        }
        $category = Category::create([
            'parent_id' => 0,
            'name' => $request->name,
            'min_percent' => $request->percents['min'],
            'max_percent' => $request->percents['max'],
            'whole_percent' => $request->percents['wholesale'],
            'min_product' => $request->percents['min_product'],
        ]);

        return ApiResponse::success(data: $category);
    }

    public function index(Request $request)
    {
        $delete = $request->delete == "true" ? true : false;
        $categories = Category::select('id', 'name', 'min_percent', 'max_percent', 'whole_percent', 'min_product', 'deleted_at')->orderBy('id');
        if ($delete == true) {
            $categories = $categories->withTrashed();
        }
        $categories = Search::new()->add($categories, 'name')
            ->beginWithWildcard()
            ->search($request->search);
        return ApiResponse::success(data: $categories);
    }

    public function update(Request $request)
    {
        try {
            $category = Category::findOrFail($request->category_id);
        } catch (\Throwable $th) {
            return ApiResponse::error('not found', 404);
        }
        $percents = $request->percents;
        $data = [];
        if (isset($percents['min'])) {
            $data['min_percent'] = $percents['min'];
        }
        if (isset($percents['max'])) {
            $data['max_percent'] = $percents['max'];
        }
        if (isset($percents['wholesale'])) {
            $data['whole_percent'] = $percents['wholesale'];
        }
        if (isset($request->name)) {
            $data['name'] = $request->name;
        }
        if (isset($request->name)) {
            $data['min_product'] = $request->min_product;
        }
        $products = Product::where('category_id', $request->category_id)->get();
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();
        foreach ($products as $product) {
            $cost = $product->cost_price;
            $min = $product['min_price'];
            if (isset($percents['min'])) {
                if ($min['currency_id'] == 2) {
                    $min['price'] = $cost['price'] * $percents['min'] / 100 + $cost['price'];
                } else {
                    if ($cost['currency_id'] == 2) {
                        $min['price'] = floor(((($cost['price'] * $percents['min'] / 100 + $cost['price']) * $usdToUzs->rate + 500) / 1000)) * 1000;
                    } else {
                        $min['price'] = $cost['price'] * $percents['min'] / 100 + $cost['price'];
                    }
                }
            }
            $max = $product['max_price'];
            if (isset($percents['max'])) {
                if ($max['currency_id'] == 2) {
                    $max['price'] = $cost['price'] * $percents['max'] / 100 + $cost['price'];
                } else {
                    if ($cost['currency_id'] == 2) {
                        $max['price'] = floor(((($cost['price'] * $percents['max'] / 100 + $cost['price']) * $usdToUzs->rate + 500) / 1000)) * 1000;
                    } else {
                        $max['price'] = $cost['price'] * $percents['max'] / 100 + $cost['price'];
                    }
                }
            }
            $whole = $product['whole_price'];
            if (isset($percents['wholesale'])) {
                if ($whole['currency_id'] == 2) {
                    $whole['price'] = $cost['price'] * $percents['wholesale'] / 100 + $cost['price'];
                } else {
                    if ($cost['currency_id'] == 2) {
                        $whole['price'] = floor(((($cost['price'] * $percents['wholesale'] / 100 + $cost['price']) * $usdToUzs->rate + 500) / 1000)) * 1000;
                    } else {
                        $whole['price'] = $cost['price'] * $percents['wholesale'] / 100 + $cost['price'];
                    }
                }
            }
            $product->update([
                'min_price' => $min,
                'max_price' => $max,
                'whole_price' => $whole
            ]);
        }
        $category = $category->update($data);
        return ApiResponse::success();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->products()->delete();
        $category->delete();
        return ApiResponse::success();
    }
}
