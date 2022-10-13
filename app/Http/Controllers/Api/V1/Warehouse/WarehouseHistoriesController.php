<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Illuminate\Http\Request;

class WarehouseHistoriesController extends Controller
{
    public function ShowAllHistoriesBaskets(Request $request)
    {
        $baskets = WarehouseBasket::where('status', 'taken')->paginate($request->per_page ?? 50);
        $final = [
            'per_page' => $baskets->perPage(),
            'last_page' => $baskets->lastPage(),
            'data' => [],
        ];
        foreach ($baskets as $basket) {
            $final['data'][] = [
                'id' => $basket->id,
                'branch' => [
                    'id' => $basket->branch_id,
                    'name' => $basket->branch->name,
                ],
                'to_branch' => [
                    'id' => $basket->to_branch_id,
                    'name' => $basket->toBranch?->name,
                ],
                'employee' => [
                    'id' => $basket->employee_id,
                    'name' => $basket->employee->name,
                ],
                'status' => $basket->status,
                'created_at' => $basket->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return ApiResponse::success(data:$final);
    }

    public function ShowAllHistoriesOrders(Request $request, WarehouseBasket $basket)
    {
        $orders = WarehouseOrder::where('warehouse_basket_id', $basket->id)->get();
        $final = [];
        foreach ($orders as $order) {
            $final[] = [
                'id' => $order->id,
                'product_id' => $order->product_id,
                'product_name' => $order->product->name,
                'unit_id' => $order->unit_id,
                'unit' => $order->unit->unit,
                'count' => $order->count,
            ];
        }
        return ApiResponse::success(data:$final);
    }
}
