<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\toBranchRequest;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'to_branch_id' => $request->branch_id,
            'employee_id' => $employee->id,
            'date' => $date,
            'status' => 'given',
            'type' => 'branch to branch',
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

    public function take(Request $request, WarehouseBasket $basket)
    {
        $employee = $request->user();
        if ($basket->to_branch_id != $employee->branch_id) {
            return ApiResponse::error('Not allowed', 400);
        }
        if ($basket->status == 'taken') {
            return ApiResponse::error('Already taken', 400);
        }
        $orders = WarehouseOrder::where('warehouse_basket_id', $basket->id)->get();
        $date = Carbon::today()->format('Y-m-d');
        foreach ($orders as $order) {
            $warehouse = Warehouse::where('active', true)->where('branch_id', $basket->to_branch_id)->where('product_id', $order->product_id)->first();
            $updated_count = ($warehouse->count ?? 0) + $order->count;
            if ($warehouse and $date == $warehouse->date) {
                $warehouse->update([
                    'count' => $updated_count,
                ]);
            } else {
                Warehouse::create([
                    'branch_id' => $basket->to_branch_id,
                    'product_id' => $order->product_id,
                    'unit_id' => $order->unit_id,
                    'date' => $date,
                    'active' => true,
                    'count' => $updated_count,
                ]);
                $warehouse?->update([
                    'active' => false,
                ]);
            }
        }
        $basket->status = 'taken';
        $basket->save();
        return ApiResponse::success('success');
    }
}
