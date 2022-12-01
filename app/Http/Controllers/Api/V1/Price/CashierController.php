<?php

namespace App\Http\Controllers\Api\V1\Price;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\Category;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function cashier(Request $request)
    {
        $from = $request->from ?? Carbon::today()->format('Y-m-d');
        $to = $request->to ?? Carbon::today()->format('Y-m-d');
        $cashier = Cashier::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get();
        $card = 0;
        $cash = 0;
        foreach ($cashier as $item) {
            $card += $item->balance['card'] ?? 0;
            $cash += $item->balance['cash'] ?? 0;
        }
        $data = [
            'card' => $card,
            'cash' => $cash,
        ];

        return ApiResponse::success(data: $data);
    }

    public function monthly(Request $request)
    {
        $from = $request->from ?? Carbon::today()->format('Y-m-d');
        $to = $request->to ?? Carbon::today()->format('Y-m-d');
        $cashier = Cashier::select(
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date)) as monthyear"),
            'balance',
        )
            ->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get()->collect();
        $cashier = $cashier->sortBy('month')->sortBy('year')->groupBy('monthyear');
        $final = [];
        foreach ($cashier as $key => $value) {
            $lastDay = Carbon::create($value[0]->year, $value[0]->month)->endOfMonth();
            $profit = Profit::whereDate('date', '>=', $value[0]->year.'-'.$value[0]->month.'-01')
                ->whereDate('date', '<=', $lastDay)
                ->sum('profit');
            $collect = collect($value);
            $final[] = [
                'month' => $value[0]->month,
                'year' => $value[0]->year,
                'card' => $collect->sum('balance.card'),
                'cash' => $collect->sum('balance.cash'),
                'profit' => $profit,
            ];
        }

        return ApiResponse::success(data: $final);
    }

    public function ProfitShow(Request $request)
    {
        $from = $request->from ?? Carbon::today()->format('Y-m-d');
        $to = $request->to ?? Carbon::today()->format('Y-m-d');
        $categories = Category::all();
        $profits = Profit::whereDate('date', '>=', $from)
            ->when($request->branch_id, function ($query, $branch_id) {
                return $query->where('branch_id', $branch_id);
            })
            ->whereDate('date', '<=', $to)->get();
        $final = [];
        foreach ($categories as $category) {
            $profit = $profits->where('category_id', $category->id);
            $final[] = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'amount' => $profit->sum('sum'),
                'profit' => $profit->sum('profit'),
            ];
        }

        return ApiResponse::success(data: $final);
    }
}
