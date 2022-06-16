<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientWarehouse extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    //protected $casts = [];
    public $timestamps = false;

    public function Ingredient()
    {
        return $this->hasOne(Ingredient::class, 'id', 'ingredient_id');
    }

    public function basket()
    {
        return $this->hasOne(IngredientWarehouseBasket::class, 'id');
    }
}
