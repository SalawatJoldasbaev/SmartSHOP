<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientWarehouseBasket extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(IngredientWarehouseOrder::class, 'ingredient_warehouse_basket_id', 'id');
    }
}
