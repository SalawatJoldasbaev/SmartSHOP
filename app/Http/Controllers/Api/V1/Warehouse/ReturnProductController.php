<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DefectRequest;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Carbon\Carbon;

class ReturnProductController extends Controller
{
    public function returnProduct(DefectRequest $request)
    {
        $employee = $request->user();
        $branch = Branch::find($request->branch_id);
        if ($request->type == 'return' and ($employee->branch->is_main == false or $branch->is_main == false)) {
            return ApiResponse::error('not allowed', 400);
        }
        $data = $request->data;
        foreach ($data as $item) {
            $warehouse = Warehouse::where('product_id', $item['product_id'])
                ->active()->where('branch_id', $branch->id)
                ->first();
            if (($warehouse?->count ?? 0) - $item['count'] < 0) {
                return ApiResponse::error('not enough product', 422);
            }
        }
        $basket = WarehouseBasket::create([
            'branch_id' => $request->branch_id,
            'to_branch_id' => $request->to_branch_id,
            'employee_id' => $employee->id,
            'date' => Carbon::today(),
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->type == 'return' ? 'taken' : 'given',
        ]);
        foreach ($data as $item) {
            $warehouse = Warehouse::where('product_id', $item['product_id'])
                ->where('branch_id', $request->branch_id)
                ->active()
                ->first();
            $warehouse->count -= $item['count'];
            $warehouse->save();
            WarehouseOrder::create([
                'branch_id' => $request->branch_id,
                'warehouse_basket_id' => $basket->id,
                'product_id' => $item['product_id'],
                'unit_id' => $warehouse->unit_id,
                'count' => $item['count'],
            ]);
        }
        return ApiResponse::success();
    }
}
