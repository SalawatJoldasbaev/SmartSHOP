<?php

namespace App\Exports;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Currency;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $final = [];
        $temp = [];
        foreach (Product::all() as $product) {
            $cost_price = Currency::find($product->cost_price['currency_id']);
            $min_price = Currency::find($product->min_price['currency_id']);
            $max_price = Currency::find($product->max_price['currency_id']);
            $whole_price = Currency::find($product->whole_price['currency_id']);
            $id = $product->id;
            $unit = Unit::find($product->warehouse?->unit_id);
            $temp = [
                'id' => $id,
                'category' => [
                    'id' => $product->category_id,
                    'name' => $product->category->name,
                ],
                'name' => $product->name,
                'brand' => $product->brand,
                'cost_price' => [
                    'code' => $cost_price->code,
                    'price' => $product->cost_price['price']
                ],
                'min_price' => [
                    'code' => $min_price->code,
                    'price' => $product->min_price['price']
                ],
                'max_price' => [
                    'code' => $max_price->code,
                    'price' => $product->max_price['price']
                ],
                'whole_price' => [
                    'name' => $whole_price->name,
                    'code' => $whole_price->code,
                    'price' => $product->whole_price['price']
                ],
                'warehouse' => isset($product->warehouse) ? [
                    'unit' => [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'code' => $unit->unit
                    ],
                    'count' => $product->warehouse->count
                ] : null,
            ];
            $final[] = $temp;
        }
        return view('export.product', ['products' => $final]);
    }
}
