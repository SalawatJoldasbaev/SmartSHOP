<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseBasket extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    //protected $casts = [];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseOrder::class);
    }
}
