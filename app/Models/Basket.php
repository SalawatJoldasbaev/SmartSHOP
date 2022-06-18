<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Basket extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    protected $casts = [
        'card' => 'array',
        'cash' => 'array',
        'debt' => 'json',
        'remaining_debt' => 'array',
        'paid_debt' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'basket_id');
    }
}
