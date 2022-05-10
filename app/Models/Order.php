<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    protected $casts = [
        'price' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
