<?php

namespace App\Src;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseBasket;
use App\Models\WarehouseOrder;
use Auth;
use Carbon\Carbon;

class WarehouseLogic
{
    public function SetWarehouse($data, $id = null)
    {
        $date = Carbon::today()->format('Y-m-d');
        $employee = $data->user();
        $basket = WarehouseBasket::create([
            'branch_id' => $employee->branch_id,
            'employee_id' => $data->user()?->id ?? Auth::user()->id,
            'date' => $date,
            'status' => 'taken',
            'type' => 'factory to main warehouse',
        ]);
        $data = $data->all();
        foreach ($data as $product) {
            $warehouse = Warehouse::where('active', true)->where('branch_id', $employee->branch_id)->where('product_id', $product['product_id'])->first();
            $productModel = Product::find($product['product_id']);
            $cost = $product['price'];
            $updated_count = ($warehouse->count ?? 0) + $product['count'];

            $warehouseOrder = WarehouseOrder::create([
                'branch_id' => $employee->branch_id,
                'warehouse_basket_id' => $basket->id,
                'product_id' => $product['product_id'],
                'unit_id' => $product['unit_id'],
                'count' => $product['count'],
            ]);

            $productModel->update([
                'cost_price' => $cost,
                'min_price' => $product['min_price'],
                'max_price' => $product['max_price'],
                'whole_price' => $product['whole_price'],
            ]);
            if ($warehouse and $date == $warehouse->date) {
                $warehouse->update([
                    'count' => $updated_count,
                ]);
            } else {
                $new_warehouse = Warehouse::create([
                    'branch_id' => $employee->branch_id,
                    'product_id' => $product['product_id'],
                    'unit_id' => $product['unit_id'],
                    'date' => $date,
                    'active' => true,
                    'count' => $updated_count,
                ]);
                $warehouse?->update([
                    'active' => false,
                ]);
            }
        }
        return true;
    }

    public function getWarehouse(
        $search = null,
        $min_product = false,
        $category_id = null,
        $branch_id = null
    ) {
        $warehouses = Warehouse::active()
            ->where('branch_id', $branch_id)
            ->whereHas('product', function ($query) use ($search, $category_id) {
                if ($category_id) {
                    $query->where('category_id', $category_id);
                }
                $query->where('name', 'like', '%' . $search . '%');
            })->paginate(30);
        $final = [
            'current_page' => $warehouses->currentPage(),
            'per_page' => $warehouses->perPage(),
            'last_page' => $warehouses->lastPage(),
            'data' => [],
        ];
        foreach ($warehouses as $warehouse) {
            $product = $warehouse->product;
            $category = $product?->category;
            $temp = [
                'product' => [
                    'id' => $product?->id,
                    'name' => $product?->name,
                    'brand' => $product?->brand,
                    'image' => $product?->image,
                    'cost_price' => $product->cost_price,
                ],
                'category' => [
                    'id' => $category?->id,
                    'name' => $category?->name,
                    'min_percent' => $category?->min_percent,
                    'max_percent' => $category?->max_percent,
                    'whole_percent' => $category?->whole_percent,
                    'min_product' => $category?->min_product,
                ],
                'count' => $warehouse->count,
                'unit' => [
                    'id' => $warehouse->unit->id,
                    'name' => $warehouse->unit->name,
                    'code' => $warehouse->unit->unit,
                ],
                'date' => $warehouse->date,
            ];
            if ($min_product === true) {
                if ($category->min_product >= $warehouse->count) {
                    $final['data'][] = $temp;
                }
            } else {
                $final['data'][] = $temp;
            }
        }
        return $final;
    }
}
