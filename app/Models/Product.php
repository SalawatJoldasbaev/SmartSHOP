<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'whole_price' => 'array',
        'max_price' => 'array',
        'min_price' => 'array',
        'cost_price' => 'array',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function Warehouse()
    {
        return $this->hasOne(Warehouse::class)->where('active', true);
    }

    public function Ingredients()
    {
        return $this->hasMany(IngredientProduct::class, 'product_id', 'id');
    }

    public function Preparation_day()
    {
        return $this->hasOne(IngredientProductTime::class);
    }
}
