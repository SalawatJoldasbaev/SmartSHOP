<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Models\Forex;
use App\Models\Order;
use App\Models\Basket;
use App\Models\Cashier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnOrderRequest;
use App\Http\Controllers\Api\V1\ApiResponse;

class ReturnOrderController extends Controller
{
    public function Orders(ReturnOrderRequest $request)
    {
        $basket_id = $request->basket_id;
        $orders = $request->orders;
        $payment_type = $request->payment_type; //card, debt, cash, paid_debt
        $basket = Basket::find($basket_id);
        $user = $basket->user;
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();
        $cost_price = 0;
        $price = 0;
        $balance = [
            'sum' => 0,
        ];
        foreach ($orders as $order) {
            $order_id = $order['order_id'];
            $count = $order['count'];
            $order = Order::where('id', $order_id)
                ->where('basket_id', $basket_id)
                ->first();
            if ($order->count - $count < 0) {
                return ApiResponse::error('An error occurred', 409);
            }
            $product = Product::where('id', $order->product_id)->withTrashed()->first();
            if ($product->cost_price['currency_id'] == 2) {
                $cost_price += (($product->cost_price['price'] * $usdToUzs->rate) * $count);
            } else {
                $cost_price += ($product->cost_price['price'] * $count);
            }
            $price += $count * $order->price;
        }
        foreach ($payment_type as $item) {
            if ($item == 'debt') {
                $balance['remaining'] = 0;
            }
            $balance[$item] = 0;
        }
        foreach ($payment_type as $item) {
            if ($balance['sum'] >= $price) {
                break;
            }
            if ($item == 'debt') {
                $balance['debt'] += $basket->debt['remaining'];
                $balance['sum'] += $basket->debt['remaining'];
            } elseif ($item == 'paid_debt') {
                $balance['paid_debt'] += $basket->debt['paid'];
                $balance['sum'] += $basket->debt['paid'];
            } else {
                $balance[$item] += $basket->$item;
                $balance['sum'] += $basket->$item;
            }
        }
        $temp_balance = $balance;
        if ($balance['sum'] - $price < 0) {
            return ApiResponse::error('The money was mistaken', 409);
        }
        $temp = 0;
        foreach ($payment_type as $item) {
            if ($balance[$item] == 0) {
                continue;
            }
            if ($balance[$item] < $price) {
                $price -= $balance[$item];
                $balance[$item] = 0;
            } else {
                $temp = $balance[$item];
                $balance[$item] -= $price;
                $price -= $temp;
                $temp = 0;
            }
        }
        if (isset($temp_balance['card'])) {
            $temp_balance['card'] -= $balance['card'];
        }
        if (isset($temp_balance['cash'])) {
            $temp_balance['cash'] -= $balance['cash'];
        }
        if (isset($temp_balance['debt'])) {
            $temp_balance['debt'] -= $balance['debt'];
        }
        if (isset($temp_balance['paid_debt'])) {
            $temp_balance['paid_debt'] -= $balance['paid_debt'];
        }
        $cashier = Cashier::orderBy('id', 'desc')->first();
        $cashierBalance = $cashier->balance;
        $cashierBalance['card'] -= ($temp_balance['card'] ?? 0);
        $cashierBalance['cash'] -= ($temp_balance['cash'] ?? 0);
        $cashierBalance['sum'] -= (($temp_balance['cash'] ?? 0) + ($temp_balance['card'] ?? 0));
        $profit = abs(($temp_balance['cash'] ?? 0) + ($temp_balance['card'] ?? 0) - $cost_price);

        $cashier->update([
            'balance' => $cashierBalance,
            'profit' => $cashier->profit > 0 ? $cashier->profit - $profit : $cashier->profit + $profit,
        ]);

        foreach ($orders as $order) {
            $order_id = $order['order_id'];
            $count = $order['count'];
            $order = Order::where('id', $order_id)
                ->where('basket_id', $basket_id)
                ->first();
            $order->count -= $count;
            $order->save();
            $warehouse = Warehouse::active()->where('product_id', $order->product_id)->first();
            $warehouse->update([
                'count' => $warehouse->count + $count,
            ]);
        }
        $debt = $basket->debt;
        if (isset($temp_balance['debt'])) {
            $debt['debt'] -= $temp_balance['debt'];
            $debt['remaining'] -= $temp_balance['debt'];
        }
        if (isset($temp_balance['paid_debt'])) {
            $debt['debt'] -= $temp_balance['paid_debt'];
            $debt['paid'] -= $temp_balance['paid_debt'];
        }
        $basket->update([
            'card' => $basket->card - ($temp_balance['card'] ?? 0),
            'cash' => $basket->cash - ($temp_balance['cash'] ?? 0),
            'debt' => $debt,
        ]);
        if (isset($temp_balance['paid_debt']) or isset($temp_balance['debt'])) {
            $debt_user = $user->balance + $temp_balance['debt'];
            $user->update([
                'balance' => $debt_user,
            ]);

        }

        return ApiResponse::success();
    }
}
