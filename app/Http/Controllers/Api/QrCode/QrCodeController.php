<?php

namespace App\Http\Controllers\Api\QrCode;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $uuid = $request->uuid ?? '123';
        $type = $request->type;

        return QrCode::size(250)->generate($type."\n".$uuid);
    }

    public function code(Request $request)
    {
        $uuid = $request->uuid;
        $type = $request->type;
        if ($type == 'product') {
            $data = $this->product($uuid);
        } elseif ($type == 'basket') {
            $data = $this->basket($uuid);
        }

        return $data;
    }

    private function product($uuid)
    {
        try {
            $product = Product::where('uuid', $uuid)->firstOrFail();
        } catch (\Throwable $th) {
            return ApiResponse::error('product not found', 404);
        }

        $currency = Currency::whereIn('id', [
            $product->cost_price['currency_id'],
            $product->max_price['currency_id'],
            $product->whole_price['currency_id'],
            $product->min_price['currency_id'],
        ])->get()->collect();
        $cost_price = $currency->where('id', $product->cost_price['currency_id'])->first();
        $min_price = $currency->where('id', $product->min_price['currency_id'])->first();
        $whole_price = $currency->where('id', $product->whole_price['currency_id'])->first();
        $max_price = $currency->where('id', $product->max_price['currency_id'])->first();
        $data = [
            'id' => $product->id,
            'category' => [
                'id' => $product->category_id,
                'min_percent' => $product->category?->min_percent,
                'max_percent' => $product->category?->max_percent,
                'whole_percent' => $product->category?->whole_percent,
            ],
            'image' => $product->image,
            'name' => $product->name,
            'brand' => $product->brand,
            'cost_price' => [
                'currency_id' => $product->cost_price['currency_id'],
                'name' => $cost_price['name'],
                'code' => $cost_price['code'],
                'price' => $product->cost_price['price'],
            ],
            'min_price' => [
                'currency_id' => $product->min_price['currency_id'],
                'name' => $min_price['name'],
                'code' => $min_price['code'],
                'price' => $product->min_price['price'],
            ],
            'max_price' => [
                'currency_id' => $product->max_price['currency_id'],
                'name' => $max_price['name'],
                'code' => $max_price['code'],
                'price' => $product->max_price['price'],
            ],
            'whole_price' => [
                'currency_id' => $product->whole_price['currency_id'],
                'name' => $whole_price['name'],
                'code' => $whole_price['code'],
                'price' => $product->whole_price['price'],
            ],
            'warehouse' => isset($product->Warehouse) ? [
                'unit' => [
                    'id' => $product->warehouse->unit->id,
                    'name' => $product->warehouse->unit->name,
                    'code' => $product->warehouse->unit->unit,
                ],
                'count' => $product->Warehouse->count,
            ] : null,
            'qr_code_link' => $product->uuid ? route('qrcode', [
                'uuid' => $product->uuid,
                'type' => 'product',
            ]) : null,
        ];

        return ApiResponse::success(data: $data);
    }

    private function basket($uuid)
    {
    }
}
