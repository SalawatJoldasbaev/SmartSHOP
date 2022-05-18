<?php

namespace App\Http\Controllers\Api\V1\Price;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Cashier;
use App\Models\Forex;
use App\Models\PaymentHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paidDebt(Request $request)
    {
        $basket_id = $request->basket_id;
        $card = $request->card;
        $cash = $request->cash;
        $basket = Basket::find($basket_id);
        $orders = $basket->orders;
        $debt = $basket->debt;
        $cost_price = 0;
        $remaining_sum = [
            'card'=> 0,
            'cash'=> 0,
        ];
        $paid_sum = $basket->cash + $basket->card + $basket->debt['paid'];
        $sum = $card + $cash;
        if($sum - $debt['remaining'] > 500){
            return ApiResponse::error('There is no extra charge', 409);
        }
        if ($card) {
            $debt['remaining'] -= $card;
            $debt['paid'] += $card;
            if ($debt['remaining'] < 0) {
                $remaining_sum['card'] = abs($debt['remaining']);
                $card +=$debt['remaining'];
            }
        }
        if ($cash) {
            $debt['remaining'] -= $cash;
            $debt['paid'] += $cash;
            if ($debt['remaining'] < 0) {
                $remaining_sum['cash'] = abs($debt['remaining']);
                $cash += $debt['remaining'];
            }
        }
        if ($debt['remaining'] < 0) {
            $debt['remaining'] = 0;
        }
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();
        foreach ($orders as $order) {
            $product = $order->product;
            $count = $order->count;
            if ($product->cost_price['currency_id'] == 2) {
                $cost_price += (($product->cost_price['price'] * $usdToUzs->rate) * $count);
            } else {
                $cost_price += ($product->cost_price['price'] * $count);
            }
        }
        $cashier = Cashier::orderBy('id', 'desc')->first();
        $balance = $cashier->balance;
        $balance['card'] += $card;
        $balance['cash'] += $cash;
        $balance['sum'] += $sum;
        PaymentHistory::create([
            'basket_id' => $basket->id,
            'employee_id' => $request->user()->id ?? $request->employee_id,
            'user_id' => $basket->user_id,
            'amount_paid' => [
                'card' => $card,
                'cash' => $cash,
            ],
            'paid_time' => Carbon::now(),
        ]);
        if(array_sum($remaining_sum) > 0){
            $remaining_basket = Basket::where('user_id', $basket->user_id)->where('debt->remaining', '>', 0)->whereNot('id', $basket->id)->first();
            if ($remaining_basket) {
                $this->paidDebt(new Request([
                    'employee_id'=> $request->user()->id,
                    'basket_id' => $remaining_basket->id,
                    'cash' => $remaining_sum['cash'],
                    'card' => $remaining_sum['card'],
                ]));
            }
        }
        $user = $basket->user;
        $user->balance += $sum - $debt['remaining'];
        $user->save();

        $profit = $paid_sum - $cost_price;
        $temp_profit = $profit;

        if ($profit + $card > 0) {
            $temp = $profit;
            $profit += $card;
            $card = $temp + $card;
        } else {
            $profit += $card;
        }
        if ($profit + $cash > 0 and $profit < 0) {
            $temp = $profit;
            $profit += $cash;
            $cash = $temp + $cash;
        } else {
            $profit += $cash;
        }
        $basket->debt = $debt;
        $basket->save();

        if ($profit > 0) {
            $cashier->update([
                'balance' => $balance,
                'profit' => ($cashier->profit + $profit) - $temp_profit,
            ]);
        } else {
            $cashier->update([
                'balance' => $balance,
                'profit' => ($cashier->profit + $profit) - $temp_profit,
            ]);
        }
        return ApiResponse::success();
    }

    public function index(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        if (!$from or !$to) {
            return ApiResponse::error('from and to required', 422);
        }
        $user_id = $request->user_id;

        $histories = PaymentHistory::whereDate('paid_time', '>=', $from)
            ->whereDate('paid_time', '<=', $to)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            });

        $paginate = $histories->paginate(33);
        $final = [
            'current_page' => $paginate->currentPage(),
            'per_page' => $paginate->perPage(),
            'last_page' => $paginate->lastPage(),
            'data' => [
                'amount' => [
                    'card' => $histories->sum('amount_paid->card'),
                    'cash' => $histories->sum('amount_paid->cash'),
                ],
                'histories' => [],
            ],
        ];
        foreach ($paginate as $history) {
            $final['data']['histories'][] = [
                'payment_id' => $history->id,
                'basket_id' => $history->basket_id,
                'employee' => [
                    'id' => $history->employee_id,
                    'name' => $history->employee->name,
                ],
                'client' => [
                    'id' => $history->user_id,
                    'name' => $history->user->full_name,
                ],
                'amount_paid' => $history->amount_paid,
                'paid_time' => $history->paid_time,
            ];
        }
        return ApiResponse::success(data:$final);
    }
}
