<?php

namespace App\Http\Controllers\Api\V1\Price;

use Carbon\Carbon;
use App\Models\Cashier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\ApiResponse;

class CashierController extends Controller
{
    public function cashier(Request $request)
    {
        $from = $request->from ?? Carbon::today()->format('Y-m-d');
        $to = $request->to ?? Carbon::today()->format('Y-m-d');
        $cashier = Cashier::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get();
        $card = 0;
        $cash = 0;
        $profit = 0;
        foreach ($cashier as $item) {
            $card += $item->balance['card'] ?? 0;
            $cash += $item->balance['cash'] ?? 0;
            $profit += $item->profit ?? 0;
        }
        $data = [
            'card' => $card,
            'cash' => $cash,
            'profit' => $request->user()->role == "ceo" ? $profit : null,
        ];

        return ApiResponse::success(data: $data);
    }
    public function monthly(Request $request)
    {
        $from = $request->from ?? Carbon::today()->format('Y-m-d');
        $to = $request->to ?? Carbon::today()->format('Y-m-d');
        $cashier = Cashier::select(
            DB::raw("MONTH(date) as month"),
            DB::raw("YEAR(date) as year"),
            DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date)) as monthyear"),
            "balance",
            "profit"
        )
            ->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get()->collect();
        $cashier = $cashier->sortBy('month')->sortBy('year')->groupBy('monthyear');
        $final = [];
        foreach ($cashier as $key => $value) {
            $collect = collect($value);
            $final[] = [
                'month' => $value[0]->month,
                'year' => $value[0]->year,
                'card' => $collect->sum('balance.card'),
                'cash' => $collect->sum('balance.cash'),
                'profit' => $collect->sum('profit')
            ];
        }
        return ApiResponse::success(data: $final);
    }
}
