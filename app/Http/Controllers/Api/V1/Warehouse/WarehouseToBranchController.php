<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\toBranchRequest;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Carbon\Carbon;

class WarehouseToBranchController extends Controller
{
    public function toBranch(toBranchRequest $request)
    {
        $employee = $request->user();
        if ($employee->role != 'warehouseManager') {
            return ApiResponse::error('Forbidden', 403);
        }
        $warehouses = Warehouse::active()->where('branch_id', $employee->branch_id)->get()->collect();
        foreach ($request->products as $product) {
            $warehouse = $warehouses->where('product_id', $product['product_id'])?->first();
            if (!$warehouse or $warehouse->count - $product['count'] < 0) {
                return ApiResponse::error('product not enough', 400);
            }
        }
        $date = Carbon::today()->format('Y-m-d');
        $basket = WarehouseBasket::create([
            'branch_id' => $employee->branch_id,
            'employee_id' => $employee->id,
            'date' => $date,
            'status' => 'given',
        ]);
        foreach ($request->products as $product) {
            $warehouse = $warehouses->where('product_id', $product['product_id'])?->first();
            $warehouse->count -= $product['count'];
            WarehouseOrder::create([
                'branch_id' => $employee->branch_id,
                'warehouse_basket_id' => $basket->id,
                'product_id' => $product['product_id'],
                'unit_id' => $warehouse->unit_id,
                'count' => $product['count'],
            ]);
            $warehouse->save();
        }
        return ApiResponse::success('success');

    }
}
