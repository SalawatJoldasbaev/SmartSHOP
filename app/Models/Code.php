<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Code extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    protected $casts = [
        'cost_price'=> 'array'
    ];

    public function scopeNewCode($query)
    {
        $newcode = Str::random(9);
        $code = $this->where('code', $newcode)->first();
        while($code){
            $newcode = Str::random(9);
            $code = $this->where('code', $newcode)->first();
        }
        return $newcode;
    }
}
