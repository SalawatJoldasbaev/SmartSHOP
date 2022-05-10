<?php

namespace App\Http\Controllers\Api\V1\Order;

use Carbon\Carbon;
use App\Models\Basket;
use Illuminate\Http\Request;
use App\Models\PaymentHistory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\ApiResponse;
use App\Models\QrCode;

class BasketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status ?? 'finished';
        $user_id = $request->user_id;
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $filter = explode('|', $request->filter);
        $baskets = Basket::where('status', $status)
            ->whereHas('user', function ($query) use ($search) {
                $query->where('full_name', 'like', '%' . $search . '%');
            })
            ->when(!empty($filter), function ($query) use ($filter) {
                $is_card = in_array('card', $filter);
                $is_cash = in_array('cash', $filter);
                $is_debt = in_array('debt', $filter);
                if ($is_card and !$is_cash and !$is_debt) {
                    return $query->where('card', '>', 0)->where('cash', 0)->where('debt->debt', 0);
                } elseif (!$is_card and $is_cash and !$is_debt) {
                    return $query->where('card', 0)->where('cash', '>', 0)->where('debt->debt', 0);
                } elseif (!$is_card and !$is_cash and $is_debt) {
                    return $query->where('card', 0)->where('cash', 0)->where('debt->debt', '>', 0);
                } elseif ($is_card and $is_cash and !$is_debt) {
                    return $query->where('card', '>', 0)->where('cash', '>', 0)->where('debt->debt', 0);
                } elseif ($is_card and !$is_cash and $is_debt) {
                    return $query->where('card', '>', 0)->where('cash', 0)->where('debt->debt', '>', 0);
                } elseif (!$is_card and $is_cash and $is_debt) {
                    return $query->where('card', 0)->where('cash', '>', 0)->where('debt->debt', '>', 0);
                } elseif ($is_card and $is_cash and $is_debt) {
                    return $query->where('card', '>', 0)->where('cash', '>', 0)->where('debt->debt', '>', 0);
                }
            });

        if (isset($user_id)) {
            $baskets = $baskets->where('user_id', $user_id);
        }
        if (isset($from)) {
            $baskets = $baskets->whereDate('created_at', '>=', $from);
        }
        if (isset($to)) {
            $baskets = $baskets->whereDate('created_at', '<=', $to);
        }
        $card = $baskets->sum('card');
        $cash = $baskets->sum('cash');
        $debt = $baskets->sum('debt->debt');
        $paid_debt = $baskets->sum('debt->paid');
        $remaining_debt = $baskets->sum('debt->remaining');
        $baskets = $baskets->paginate(30);

        $final = [
            'current_page' => $baskets->currentPage(),
            'per_page' => $baskets->perPage(),
            'last_page' => $baskets->lastPage(),
            'data' => [
                'amount' => [
                    'card' => $card,
                    'cash' => $cash,
                    'debt' => $debt,
                    'paid_debt' => $paid_debt,
                    'remaining' => $remaining_debt,
                    'sum' => $card + $cash + $debt
                ],
                'baskets' => []
            ],
        ];
        $temp = [];
        foreach ($baskets as $basket) {
            if (is_null($basket->user)) {
                continue;
            }
            $temp = [
                'id' => $basket->id,
                'card' => $basket->card,
                'cash' => $basket->cash,
                'debt' => [
                    'debt' => $basket->debt['debt'],
                    'paid' => $basket->debt['paid'],
                    'remaining' => $basket->debt['remaining']
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
                'qr_link' => route('qrcode', [
                    'type' => 'basket',
                    'uuid' => $basket->uuid
                ]),
                'created_at' => date_format($basket->created_at, 'Y-m-d H:i:s'),
            ];
            $final['data']['baskets'][] = $temp;
        }
        return ApiResponse::success(data: $final);
    }

    public function basketOrders(Request  $request)
    {
        try {
            $basket_id = $request->basket_id;
            $uuid = $request->uuid;
            if (!$basket_id and $uuid) {
                $uuid = QrCode::where('uuid', $uuid)->firstOrFail();
                $basket_id = $uuid->additional['basket_id'];
            }
            $basket = Basket::findOrFail($basket_id);
        } catch (\Throwable $th) {
            return ApiResponse::error('not found', 404);
        }

        $orders = $basket->orders;
        $final = [
            'id' => $basket->id,
            'user' => [
                'id' => $basket->user_id,
                'name' => $basket->user->full_name,
                'phone' => $basket->user->phone ?? 99
            ],
            'amount' => [
                'card' => $basket->card,
                'cash' => $basket->cash,
                'debt' => $basket->debt['debt'],
                'paid_debt' => $basket->debt['paid'],
                'remaining' => $basket->debt['remaining'],
                'sum' => $basket->card + $basket->cash + $basket->debt['debt']
            ],
            'orders' => []
        ];
        foreach ($orders as $order) {
            $order_product = $order->product()->withTrashed()->first();
            $final['orders'][] = [
                'id' => $order->id,
                'product_id' => $order->product_id,
                'product_name' => $order_product->name,
                'brand' => $order_product->brand,
                'count' => $order->count,
                'unit_id' => $order->unit_id,
                'price' => $order->price
            ];
        }
        return ApiResponse::success(data: $final);
    }
}
