<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Product;
use Illuminate\Http\Request;

class StatisticaController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $employee_id = $request->employee_id;
        $category_id = $request->category_id;
        $baskets = Basket::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($employee_id, function ($query) use ($employee_id) {
                return $query->where('employee_id', $employee_id);
            })->get();
        $orders = collect([]);

        foreach ($baskets as $basket) {
            $orders->push($basket->orders);
        }
        $orders = $orders->flatten()->groupBy('product_id');
        $response = $orders->mapWithKeys(function ($group, $key) use ($category_id) {
            $product = Product::find($group->first()['product_id']);
            if ($category_id) {
                if ($category_id == $product->category_id) {
                    return [
                        $key => [
                            'product_id' => $key,
                            'product_name' => $product->name ?? null,
                            'count' => $group->sum('count'),
                        ],
                    ];
                } else {
                    return [];
                }
            } else {
                return [
                    $key => [
                        'product_id' => $key,
                        'product_name' => $product->name ?? null,
                        'count' => $group->sum('count'),
                    ],
                ];
            }
        })->values()->toArray();

        return ApiResponse::success(data:$response);
    }
}
