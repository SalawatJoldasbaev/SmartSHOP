<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseAddRequest;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
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
            search: $request->search ?? null,
            category_id: $request->category_id,
            branch_id: $branch_id
        );
        return ApiResponse::success(data: $warehouse);
    }
    public function less(Request $request)
    {
        $employee = $request->user();
        $branch_id = $request->branch_id;
        if (!$branch_id) {
            $branch_id = $employee->branch_id;
        }
        $warehouse = $this->warehouseLogic->getWarehouse(
            search: $request->search ?? null,
            min_product: true,
            category_id: $request->category_id,
            branch_id: $branch_id
        );
        return ApiResponse::success(data: $warehouse);
    }

    public function costprice(Request $request)
    {
        $branch_id = $request->branch_id;
        $warehouse = Warehouse::active()
            ->when($branch_id, function ($query, $branch_id) {
                return $query->where('branch_id', $branch_id);
            })
            ->with('product')->get();
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
        return ApiResponse::success(data: $final);
    }

    public function Orders(Request $request)
    {
        $employee = $request->user();

        $baskets = WarehouseBasket::where('status', 'given')
            ->where(function ($query) use ($employee) {
                return $query->where('branch_id', $employee->branch_id)
                    ->orWhere('to_branch_id', $employee->branch_id);
            })
            ->when($request->employee_id, function ($query, $employee_id) {
                return $query->where('employee_id', $employee_id);
            })->when($request->tome, function ($query, $tome) use ($employee) {
                if ($tome == 1) {
                    return $query->where('to_branch_id', $employee->branch_id);
                }
            })
            ->paginate($request->per_page ?? 30);
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
                'type' => $basket->type,
                'status' => $basket->status,
                'created_at' => $basket->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return ApiResponse::success(data: $final);
    }

    public function take(Request $request, WarehouseBasket $basket)
    {
        if ($basket->type == 'branch to branch') {
            $data = new WarehouseToBranchController();
            return $data->take($request, $basket);
        } elseif ($basket->type == 'defect' or $basket->type == 'gift') {
            $data = new WarehouseDefectController();
            return $data->take($request, $basket);
        }
    }

    public function ShowAllWarehouse(Request $request)
    {
        $warehouses = Warehouse::active()->get()->groupBy('product_id');
        $final = [];
        foreach ($warehouses as $key => $warehouse) {
            $temp = [
                'product_id' => $warehouse[0]->product_id,
                'product_name' => $warehouse[0]->product->name,
                'count' => $warehouse->sum('count'),
                'unit' => [
                    'id' => $warehouse[0]->unit->id,
                    'name' => $warehouse[0]->unit->name,
                    'code' => $warehouse[0]->unit->unit,
                ],
                'by_branch' => []
            ];
            foreach ($warehouse as $item) {
                $temp['by_branch'][] = [
                    'branch_id' => $item->branch_id,
                    'branch_name' => $item->branch->name,
                    'count' => $item->count,
                ];
            }
            $final[] = $temp;
        }
        return ApiResponse::success(data: $final);
    }
}
