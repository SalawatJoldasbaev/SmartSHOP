<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientWarehouseOrder extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    //protected $casts = [];

    public function Ingredient()
    {
        return $this->hasOne(Ingredient::class, 'id', 'ingredient_id')->withTrashed();
    }
}
