<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Code;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithCalculatedFormulas, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $data = $row;
        if ($data['category'] === null) {
            return;
        }
        $category = Category::where('name', $data['category'])->first();
        if (!$category) {
            $category = Category::create([
                'parent_id' => 0,
                'name' => $data['category'],
                'min_percent' => 1,
                'max_percent' => 1,
                'whole_percent' => 1,
            ]);
        }
        $product_name = '';
        $list = [];
        foreach (explode(" ", $data['product_name']) as $key => $value) {
            if (strlen(trim($value)) > 0) {
                $list[] = trim($value);
            }
        }
        $product_name = implode(" ", $list);
        //warehouse_count
        $product = Product::where('name', $product_name)->first();
        if (!$product) {
            $max = Currency::where('code', $data['price_max_currency'])->first();
            $wholesale = Currency::where('code', $data['wholesale_price_currency'])->first();
            $cost = Currency::where('code', $data['cost_price_currency'])->first();
            $min = Currency::where('code', $data['price_min_currency'])->first();
            $product = Product::create([
                'category_id' => $category->id,
                'image' => null,
                'name' => $product_name,
                'brand' => $data['brand'],
                'cost_price' => [
                    'price' => $data['cost_price'],
                    'currency_id' => $cost->id,
                ],
                'min_price' => [
                    'price' => $data['price_min'],
                    'currency_id' => $min->id,
                ],
                'max_price' => [
                    'price' => $data['price_max'],
                    'currency_id' => $max->id,
                ],
                'whole_price' => [
                    'price' => $data['wholesale_price'],
                    'currency_id' => $wholesale->id,
                ],
            ]);
            if (isset($data['warehouse_count'])) {
                $code = Code::newCode();
                $basket = WarehouseBasket::create([
                    'employee_id' => 1,
                    'date' => Carbon::today()->format('Y-m-d'),
                ]);
                $warehouseOrder = WarehouseOrder::create([
                    'warehouse_basket_id' => $basket->id,
                    'product_id' => $product->id,
                    'unit_id' => 1,
                    'count' => $data['warehouse_count'],
                    'code' => $code,
                ]);
                $createCode = Code::create([
                    'warehouse_basket_id' => $basket->id,
                    'warehouse_order_id' => $warehouseOrder->id,
                    'product_id' => $product->id,
                    'code' => $code,
                    'cost_price' => [
                        'price' => $data['cost_price'],
                        'currency_id' => $cost->id,
                    ],
                ]);
                $warhouse = Warehouse::setWarehouse($product->id, $code, $data['warehouse_count'], 1);
                $createCode->update([
                    'warehouse_id' => $warhouse->id,
                ]);
            }
            return $product;
        }
    }
}
