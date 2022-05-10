<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [];

    public function rate()
    {
        return $this->hasMany(Forex::class, 'currency_id');
    }
}
