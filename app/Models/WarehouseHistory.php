<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHistory extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    //protected $casts = [];

    public function basket()
    {
        return $this->belongsTo(WarehouseHistoryBasket::class, 'warehouse_history_basket_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseHistoryItem::class, 'warehouse_history_basket_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
