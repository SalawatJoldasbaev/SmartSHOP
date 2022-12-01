<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientOrder extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    protected $casts = [
        'ingredients' => 'json',
    ];

    public function Product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
