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
use App\Models\Profit;
use App\Models\Salary;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(OrderRequest $request)
    {
        $employee = $request->user();
        $user_id = $request->client_id ?? 1;
        $card = $request->card;
        $cash = $request->cash;
        $sum = $card + $cash;
        $price = 0;
        $prices = [];
        $user = User::find($user_id);

        $warehouseBaskets = WarehouseBasket::where('status', 'given')->where('type', 'branch to branch')->where('branch_id', $employee->branch_id)->get();
        $warehouseGivenProducts = [];
        foreach ($warehouseBaskets as $warehouseBasket) {
            $warehouseBasketItems = $warehouseBasket->items;
            foreach ($warehouseBasketItems as $item) {
                if (array_key_exists($item->product_id, $warehouseGivenProducts)) {
                    $warehouseGivenProducts[$item->product_id] += $item->count;
                } else {
                    $warehouseGivenProducts[$item->product_id] = $item->count;
                }
            }
        }
        $warehouses = Warehouse::active()->where('branch_id', $employee->branch_id)->get();
        foreach ($request->orders as $order) {
            $warehouse = $warehouses->where('product_id', $order['product_id'])->first();
            $countGiven = 0;
            if (isset($warehouseGivenProducts[$warehouse->product_id])) {
                $countGiven = $warehouseGivenProducts[$warehouse->product_id];
            }
            if (($warehouse->count - $countGiven) - $order['count'] < 0) {
                return ApiResponse::error('product is not enough', data: [
                    'id' => $order['product_id'],
                    'name' => $warehouse->product->name,
                ]);
            }
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

        foreach ($request->orders as $order) {
            $warehouse = $warehouses->where('product_id', $order['product_id'])->first();
            $warehouse->count -= $order['count'];
            $warehouse->save();
            Order::create([
                'branch_id' => $employee->branch_id,
                'basket_id' => $basket->id,
                'user_id' => $user_id,
                'product_id' => $order['product_id'],
                'unit_id' => $order['unit_id'],
                'count' => $order['count'],
                'price' => $order['price'],
            ]);
            $product = Product::find($order['product_id']);
            $price += $order['count'] * $order['price'];

            if ($product->cost_price['currency_id'] == 2) {
                $cost_price = $this->UsdToUzs($product->cost_price['price'], $order['count']);
            } else {
                $cost_price = ($product->cost_price['price'] * $order['count']);
            }

            if (array_key_exists($product->category_id, $prices)) {
                $prices[$product->category_id] = [
                    'category_id' => $product->category_id,
                    'price' => $prices[$product->category_id]['price'] + $order['count'] * $order['price'],
                    'profit' => $prices[$product->category_id]['profit'] + (($order['count'] * $order['price']) - $cost_price),
                ];
            } else {
                $prices[$product->category_id] = [
                    'category_id' => $product->category_id,
                    'price' => $order['count'] * $order['price'],
                    'profit' => (($order['count'] * $order['price']) - $cost_price),
                ];
            }
            $cost_price = 0;
        }
        if ($request->debt > 0) {
            $user->balance -= $request->debt;
            $user->save();
        } elseif ($sum - $price <= 500) {
            $remaining_sum = [
                'cash' => 0,
                'card' => 0,
            ];
            $copy_price = $price;
            $tempBasket = Basket::where('user_id', $user->id)->where('debt->remaining', '>', 0)->first();
            if ($tempBasket) {
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
                    'basket_id' => $tempBasket->id,
                    'cash' => $remaining_sum['cash'],
                    'card' => $remaining_sum['card'],
                ]));
            }
            $user->balance += $sum - $price;
            $user->save();
        } elseif ($sum - $price > 500) {
            return ApiResponse::error('incorrect sum', 409);
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
            ]);
        } else {
            $cashier->update([
                'balance' => [
                    'card' => $cashier->balance['card'] + $card,
                    'cash' => $cashier->balance['cash'] + $cash,
                    'sum' => $cashier->balance['sum'] + $sum,
                ],
            ]);
        }

        $salary = Salary::where('date', $date)->where('employee_id', $employee->id)->first();
        $flex = $employee->flex;
        if ($salary) {
            $salary->update([
                'salary' => $salary->salary + ((($sum + $request->debt) * $flex) / 100),
            ]);
        } else {
            Salary::create([
                'branch_id' => $employee->branch_id,
                'employee_id' => $employee->id,
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
                'uuid' => "$basket->uuid",
                'type' => 'basket',
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
        foreach ($prices as $item) {
            $profit = Profit::where('date', $date)->where('category_id', $item['category_id'])->first();
            if ($profit) {
                $profit->update([
                    'profit' => $profit->profit + $item['profit'],
                    'sum' => $profit->sum + $item['price'],
                ]);
            } else {
                Profit::create([
                    'date' => $date,
                    'branch_id' => $employee->branch_id,
                    'category_id' => $item['category_id'],
                    'profit' => $item['profit'],
                    'sum' => $item['price'],
                ]);
            }
        }

        return ApiResponse::success(data: $final);
    }

    public function UsdToUzs($usd, $count = 1)
    {
        $usdToUzs = Forex::where('currency_id', 2)->where('to_currency_id', 1)->first();

        return ($usd * $usdToUzs->rate) * $count;
    }
}
