<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumption extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    //protected $casts = [];

    public function category()
    {
        return $this->belongsTo(ConsumptionCategory::class, 'consumption_category_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
