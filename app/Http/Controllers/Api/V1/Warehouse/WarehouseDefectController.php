<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use Carbon\Carbon;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\WarehouseHistory;
use App\Http\Controllers\Controller;
use App\Http\Requests\DefectRequest;
use App\Models\WarehouseHistoryItem;
use App\Models\WarehouseHistoryBasket;
use App\Http\Controllers\Api\V1\ApiResponse;

class WarehouseDefectController extends Controller
{
    public function Defect(DefectRequest $request)
    {
        $defetives = $request->all();

        foreach ($defetives as $item) {
            $warehouse = Warehouse::where('product_id', $item['product_id'])->active()->first();
            if ($warehouse->count - $item['count'] < 0) {
                return ApiResponse::error('not enough product', 422);
            }
        }
        $basket = WarehouseHistoryBasket::create([
            'employee_id' => $request->user()->id,
            'date' => Carbon::today(),
            'description' => $request->description,
            'additional' => []
        ]);
        foreach ($defetives as $item) {
            $warehouse = Warehouse::where('product_id', $item['product_id'])->active()->first();
            $warehouse->count -= $item['count'];
            $warehouse->save();
            WarehouseHistoryItem::create([
                'warehouse_history_basket_id' => $basket->id,
                'product_id' => $item['product_id'],
                'count' => $item['count']
            ]);
        }
        WarehouseHistory::create([
            'warehouse_history_basket_id' => $basket->id,
            'employee_id' => $request->user()->id,
            'description' => $request->description,
            'type' => 'defect'
        ]);
        return ApiResponse::success();
    }

    public function ShowDefects(Request $request)
    {
        $from = $request->from ?? Carbon::today();
        $to = $request->to ?? Carbon::today();

        $histories = WarehouseHistory::where('type', 'defect')->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();
        $final = [];
        foreach ($histories as $history) {
            $temp = [
                'id' => $history->id,
                'basket_id' => $history->warehouse_history_basket_id,
                'description' => $history->description,
                'employee' => [
                    'id' => $history->employee_id,
                    'name' => $history->employee->name,
                    'role' => $history->employee->role
                ],
                'items' => []
            ];

            foreach ($history->items as $item) {
                $temp['items'][] = [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'count' => $item->count
                ];
            }
            $final[] = $temp;
        }
        return ApiResponse::success(data: $final);
    }
}
