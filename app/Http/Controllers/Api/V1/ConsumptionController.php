<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsumptionSetRequest;
use App\Models\Cashier;
use App\Models\Consumption;
use App\Models\ConsumptionCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsumptionController extends Controller
{
    public function Categories(Request $request)
    {
        $categories = ConsumptionCategory::all(['id', 'name']);

        return ApiResponse::success(data: $categories);
    }

    public function create(ConsumptionSetRequest $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $payment_type = $request->payment_type;

        $cashier = Cashier::date($today)->first();
        if ($cashier) {
            $balance = $cashier['balance'];
            if ($request->type == 'consumption') {
                $balance[$payment_type] -= $request->price;
                $balance['sum'] -= $request->price;
                $profit = $cashier->profit - $request->price;
            } elseif ($request->type == 'income') {
                $balance[$payment_type] += $request->price;
                $balance['sum'] += $request->price;
                $profit = $cashier->profit + $request->price;
            } else {
                return ApiResponse::error('not found type', 404);
            }
            $cashier->update([
                'balance' => $balance,
                'profit' => $profit,
            ]);
        } else {
            $balance = [
                'card' => 0,
                'cash' => 0,
                'sum' => 0,
            ];
            if ($request->type == 'consumption') {
                $balance[$payment_type] -= $request->price;
                $balance['sum'] -= $request->price;
                $profit = ($cashier->profit ?? 0) - $request->price;
            } elseif ($request->type == 'income') {
                $balance[$payment_type] += $request->price;
                $balance['sum'] += $request->price;
                $profit = ($cashier->profit ?? 0) + $request->price;
            } else {
                return ApiResponse::error('not found type', 404);
            }

            $cashier = Cashier::create([
                'branch_id' => $request->user()->branch_id,
                'date' => $today,
                'balance' => $balance,
                'profit' => $profit,
            ]);
        }
        $data = $request->all();
        $data['employee_id'] = $request->user()->id;
        $data['branch_id'] = $request->user()->branch_id;
        $data['consumption_category_id'] = $request->category_id;
        Consumption::create($data);

        return ApiResponse::success();
    }

    public function index(Request $request)
    {
        $type = $request->type;
        $from = $request->from;
        $to = $request->to;
        $consumptions = Consumption::whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })->when($request->branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            });
        if (!$request->branch_id) {
            $consumptions = $consumptions->where('branch_id', $request->user()->branch_id);
        }
        $paginate = $consumptions->paginate(50);
        $sum = collect($paginate)['data'];
        $final = [
            'current_page' => $paginate->currentPage(),
            'per_page' => $paginate->perPage(),
            'last_page' => $paginate->lastPage(),
            'data' => [
                'amount' => [
                    'card' => collect($sum)->where('payment_type', 'card')->sum('price'),
                    'cash' => collect($sum)->where('payment_type', 'cash')->sum('price'),
                ],
                'items' => [],
            ],
        ];
        foreach ($paginate as $consumption) {
            $final['data']['items'][] = [
                'whom' => $consumption->whom,
                'category_name' => $consumption->category->name,
                'date' => $consumption->date,
                'price' => $consumption->price,
                'description' => $consumption->description,
                'type' => $consumption->type,
                'payment_type' => $consumption->payment_type,
                'employee' => [
                    'id' => $consumption->employee_id,
                    'name' => $consumption->employee->name ?? null,
                ],
            ];
        }

        return ApiResponse::success(data: $final);
    }
}
