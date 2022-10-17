<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Api\V1\Price\PaymentController;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Basket;
use App\Models\Cashier;
use App\Models\Forex;
use App\Models\Order;
use App\Models\Product;
use App\Models\Salary;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(OrderRequest $request)
    {
        $cost_price = 0;
        $user_id = $request->client_id ?? 1;
        $employee = $request->user();
        $orders = $request->orders;
        $warehouses = Warehouse::active()->where('branch_id', $employee->branch_id)->get();
        $set_orders = collect([]);
        $price = 0;
        $card = $request->card;
        $cash = $request->cash;

        $sum = $card + $cash;
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();
        foreach ($orders as $order) {
            $product = Product::find($order['product_id']);
            $price += $order['count'] * $order['price'];
            $warehouse = $warehouses->where('product_id', $order['product_id'])->first();
            if ($product->cost_price['currency_id'] == 2) {
                $cost_price += (($product->cost_price['price'] * $usdToUzs->rate) * $order['count']);
            } else {
                $cost_price += ($product->cost_price['price'] * $order['count']);
            }
            if ($warehouse->count - $order['count'] >= 0) {
                $warehouse->count -= $order['count'];
                $set_orders->push([
                    'branch_id' => $employee->branch_id,
                    'basket_id' => null,
                    'user_id' => $user_id,
                    'product_id' => $order['product_id'],
                    'unit_id' => $order['unit_id'],
                    'count' => $order['count'],
                    'price' => $order['price'],
                ]);
            } else {
                return ApiResponse::error('product is not enough', data:[
                    'id' => $order['product_id'],
                    'name' => $warehouse->product->name,
                ]);
            }
        }
        $user = User::find($user_id);
        if ($request->debt > 0) {
            $user->balance -= $request->debt;
            $user->save();
        } elseif ($sum - $price <= 500) {
            $remaining_sum = [
                'cash' => 0,
                'card' => 0,
            ];
            $copy_price = $price;
            $basket = Basket::where('user_id', $user->id)->where('debt->remaining', '>', 0)->first();
            if ($basket) {
                $copy_price -= $card;
                if ($copy_price < 0) {
                    $card += $copy_price;
                    $remaining_sum['card'] = abs($copy_price);
                }
                $copy_price -= $cash;
                if ($copy_price < 0) {
                    $cash += $copy_price;
                    $remaining_sum['cash'] = abs($copy_price);
                }
                $payment = new PaymentController();
                $payment->paidDebt(new Request([
                    'employee_id' => $request->user()->id,
                    'basket_id' => $basket->id,
                    'cash' => $remaining_sum['cash'],
                    'card' => $remaining_sum['card'],
                ]));
            }
            $user->balance += $sum - $price;
            $user->save();
        } elseif ($sum - $price > 500) {
            return ApiResponse::error('incorrect sum', 409);
        }
        $basket = Basket::create([
            'branch_id' => $employee->branch_id,
            'user_id' => $user_id,
            'employee_id' => $employee->id,
            'card' => $card,
            'cash' => $cash,
            'debt' => [
                'debt' => $request->debt,
                'paid' => 0,
                'remaining' => $request->debt,
            ],
            'term' => $request->term,
            'description' => $request->description,
        ]);
        $set_orders = $set_orders->map(function ($item, $key) use ($basket) {
            $item['basket_id'] = $basket->id;
            return $item;
        })->toArray();

        foreach ($set_orders as $set_order) {
            Order::create($set_order);
        }
        foreach ($warehouses as $warehouse) {
            $warehouse->save();
        }
        $date = Carbon::today()->format('Y-m-d');
        $cashier = Cashier::where('branch_id', $employee->branch_id)->date($date)->first();
        if (!$cashier) {
            $cashier = Cashier::create([
                'branch_id' => $employee->branch_id,
                'date' => $date,
                'balance' => [
                    'card' => $card,
                    'cash' => $cash,
                    'sum' => $sum,
                ],
                'profit' => $sum - $cost_price,
            ]);
        } else {
            $cashier->update([
                'balance' => [
                    'card' => $cashier->balance['card'] + $card,
                    'cash' => $cashier->balance['cash'] + $cash,
                    'sum' => $cashier->balance['sum'] + $sum,
                ],
                'profit' => $cashier->profit + ($sum - $cost_price),
            ]);
        }
        $salary = Salary::where('date', $date)->where('employee_id', $request->user()->id)->first();
        $flex = $request->user()->flex;
        if ($salary) {
            $salary->update([
                'salary' => $salary->salary + ((($sum + $request->debt) * $flex) / 100),
            ]);
        } else {
            Salary::create([
                'branch_id' => $employee->branch_id,
                'employee_id' => $request->user()->id,
                'date' => $date,
                'salary' => (($sum + $request->debt) * $flex) / 100,
            ]);
        }

        $final = [
            'id' => $basket->id,
            'amount' => [
                'card' => $basket->card,
                'cash' => $basket->cash,
                'debt' => $basket->debt['debt'],
                'paid_debt' => $basket->debt['paid'],
                'remaining' => $basket->debt['remaining'],
                'sum' => $basket->card + $basket->cash + $basket->debt['debt'],
            ],
            'term' => $basket->term,
            'description' => $basket->description,
            'user' => [
                'id' => $basket->user_id,
                'name' => $basket->user->full_name,
                'phone' => $basket->user->phone ?? 99,
            ],
            'employee' => [
                'id' => $basket->employee_id,
                'name' => $basket->employee->name,
                'role' => $basket->employee->role,
            ],
            'orders' => [],
            'created_at' => date_format($basket->created_at, 'Y-m-d H:i:s'),
            'qr_link' => route('qrcode', [
                'type' => 'basket',
                'uuid' => $basket->uuid,
            ]),
        ];
        $orders = $basket->orders;
        foreach ($orders as $order) {
            $final['orders'][] = [
                'id' => $order->id,
                'product_id' => $order->product_id,
                'product_name' => $order->product->name,
                'brand' => $order->product->brand,
                'count' => $order->count,
                'unit_id' => $order->unit_id,
                'price' => $order->price,
            ];
        }
        return ApiResponse::success(data:$final);
    }
}
