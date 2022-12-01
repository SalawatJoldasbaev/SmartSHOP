<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHistoryBasket extends Model
{
    use HasFactory;

    public $timestamps = false;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    protected $casts = [
        'additional' => 'json',
    ];
}
