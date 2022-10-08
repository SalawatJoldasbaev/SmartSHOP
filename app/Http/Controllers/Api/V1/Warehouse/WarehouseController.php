<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseAddRequest;
use App\Models\Warehouse;
use App\Src\WarehouseLogic;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private $warehouseLogic;
    public function __construct()
    {
        $this->warehouseLogic = new WarehouseLogic();
    }
    public function create(WarehouseAddRequest $request)
    {
        $employee = $request->user();
        if ($employee->branch->is_main === true) {
            $this->warehouseLogic->SetWarehouse($request);
            return ApiResponse::success();
        } else {
            return ApiResponse::error('your not allowed', 403);
        }
    }

    public function index(Request $request)
    {
        $employee = $request->user();
        $branch_id = $request->branch_id;
        if (!$branch_id) {
            $branch_id = $employee->branch_id;
        }
        $warehouse = $this->warehouseLogic->getWarehouse(
            search:$request->search ?? null,
            category_id:$request->category_id,
            branch_id:$branch_id
        );
        return ApiResponse::success(data:$warehouse);
    }
    public function less(Request $request)
    {
        $employee = $request->user();
        $branch_id = $request->branch_id;
        if (!$branch_id) {
            $branch_id = $employee->branch_id;
        }
        $warehouse = $this->warehouseLogic->getWarehouse(
            search:$request->search ?? null,
            min_product:true,
            category_id:$request->category_id,
            branch_id:$branch_id
        );
        return ApiResponse::success(data:$warehouse);
    }

    public function costprice(Request $request)
    {
        $warehouse = Warehouse::active()->with('product')->get();
        $final = [
            'usd' => 0,
            'uzs' => 0,
        ];
        foreach ($warehouse as $item) {
            if (isset($item['product'])) {
                if ($item['product']['cost_price']['currency_id'] == 2) {
                    $final['usd'] += $item['product']['cost_price']['price'] * $item['count'];
                } else {
                    $final['uzs'] += $item['product']['cost_price']['price'] * $item['count'];
                }
            }
        }
        return ApiResponse::success(data:$final);
    }
}
