<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    protected $casts = [
    ];

    public function scopeSetWarehouse($query, $product_id, $count, $unit_id)
    {
        $date = Carbon::today()->format('Y-m-d');
        $product = $this->where('active', true)->where('product_id', $product_id)->first();
        if ($product and $date == $product->date) {
            $product->update([
                'count' => $product->count + $count,
            ]);
        } elseif ($product and $date != $product->date) {
            $this->create([
                'product_id' => $product_id,
                'unit_id' => $unit_id,
                'date' => $date,
                'active' => true,
                'count' => $product->count + $count,
            ]);
            $product->update([
                'active' => false,
            ]);
        } else {
            $this->create([
                'product_id' => $product_id,
                'unit_id' => $unit_id,
                'date' => $date,
                'active' => true,
                'count' => $count,
            ]);
        }
        $product = $this->where('active', true)->where('product_id', $product_id)->first();
        return $product;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
