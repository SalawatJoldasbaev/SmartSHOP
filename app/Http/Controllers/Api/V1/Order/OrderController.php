<?php

namespace App\Http\Controllers\Api\V1\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Forex;
use App\Models\Order;
use App\Models\Basket;
use App\Models\Salary;
use App\Models\Cashier;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\ApiResponse;

class OrderController extends Controller
{
    public function create(OrderRequest $request)
    {
        $cost_price = 0;
        $user_id = $request->client_id ?? 1;
        $employee = $request->user();
        $orders = $request->orders;
        $warehouses = Warehouse::active()->get();
        $set_orders = collect([]);
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();
        foreach ($orders as $order) {
            $product = Product::find($order['product_id']);
            if ($product->cost_price['currency_id'] == 2) {
                $cost_price += (($product->cost_price['price'] * $usdToUzs->rate) * $order['count']);
            } else {
                $cost_price += ($product->cost_price['price'] * $order['count']);
            }
            $warehouse = $warehouses->where('product_id', $order['product_id'])->first();
            if ($warehouse->count - $order['count'] > 0) {
                $warehouse->count -= $order['count'];
                $set_orders->push([
                    'basket_id' => null,
                    'user_id' => $user_id,
                    'product_id' => $order['product_id'],
                    'unit_id' => $order['unit_id'],
                    'count' => $order['count'],
                    'price' => json_encode($order['price'])
                ]);
            } else {
                return ApiResponse::error('product is not enough', data: [
                    'id' => $order['product_id'],
                    'name' => $warehouse->product->name
                ]);
            }
        }
        $basket = Basket::create([
            'user_id' => $user_id,
            'employee_id' => $employee->id,
            'card' => $request->card,
            'cash' => $request->cash,
            'debt' => [
                'debt' => $request->debt,
                'paid' => 0,
                'remaining' => $request->debt
            ],
            'term' => $request->term,
            'description' => $request->description
        ]);
        $sum = $request->card + $request->cash;
        $set_orders = $set_orders->map(function ($item, $key) use ($basket) {
            $item['basket_id'] = $basket->id;
            return $item;
        })->toArray();

        Order::upsert($set_orders, [
            'basket_id',
            'user_id',
            'product_id',
            'unit_id',
            'count',
            'price',
        ]);
        foreach ($warehouses as $warehouse) {
            $warehouse->save();
        }
        $date = Carbon::today()->format('Y-m-d');
        $cashier = Cashier::date($date)->first();
        if (!$cashier) {
            $cashier = Cashier::create([
                'date' => $date,
                'balance' => [
                    'card' => $request->card,
                    'cash' => $request->cash,
                    'sum' => $sum,
                ],
                'profit' => $sum - $cost_price
            ]);
        } else {
            $cashier->update([
                'balance' => [
                    'card' => $cashier->balance['card'] + $request->card,
                    'cash' => $cashier->balance['cash'] + $request->cash,
                    'sum' => $cashier->balance['sum'] + $sum,
                ],
                'profit' => $cashier->profit + ($sum - $cost_price)
            ]);
        }
        $salary = Salary::where('date', $date)->where('employee_id', $request->user()->id)->first();
        $flex = $request->user()->flex;
        if ($salary) {
            $salary->update([
                'salary' => $salary->salary + ((($sum + $request->debt) * $flex) / 100)
            ]);
        } else {
            Salary::create([
                'employee_id' => $request->user()->id,
                'date' => $date,
                'salary' => (($sum + $request->debt) * $flex) / 100
            ]);
        }
        if ($request->debt > 0) {
            $user = User::find($user_id);
            $user->balance -= $request->debt;
            $user->save();
        }
        $final = [
            'id' => $basket->id,
            'amount' => [
                'card' => $basket->card,
                'cash' => $basket->cash,
                'debt' => $basket->debt['debt'],
                'paid_debt' => $basket->debt['paid'],
                'remaining' => $basket->debt['remaining'],
                'sum' => $basket->card + $basket->cash + $basket->debt['debt']
            ],
            'term' => $basket->term,
            'description' => $basket->description,
            'user' => [
                'id' => $basket->user_id,
                'name' => $basket->user->full_name,
                'phone' => $basket->user->phone ?? 99
            ],
            'employee' => [
                'id' => $basket->employee_id,
                'name' => $basket->employee->name,
                'role' => $basket->employee->role
            ],
            'orders' => [],
            'created_at' => date_format($basket->created_at, 'Y-m-d H:i:s'),
            'qr_link' => route('qrcode', [
                'type' => 'basket',
                'uuid' => $basket->uuid
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
                'price' => $order->price
            ];
        }
        return ApiResponse::success(data: $final);
    }
}
