<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];

    protected $casts = ['balance' => 'array'];

    public function scopeDate($query, $date = null)
    {
        $date = $date ?? Carbon::today()->format('Y-m-d');

        return $query->where('date', $date);
    }

    public function scopeSetCashier($query, $date, $currency_id, $sum)
    {
        $cashier = $this->where('date', $date)->first();
        if (! $cashier) {
            $cashier = $this->create([
                'date' => $date,
                'balance' => [
                    [
                        'currency_id' => $currency_id,
                        'sum' => $sum,
                    ],
                ],
            ]);

            return true;
        }
        $exists = false;
        $balance = [];
        foreach ($cashier['balance'] as $item) {
            if ($item['currency_id'] == $currency_id) {
                $item['sum'] += $sum;
                $exists = true;
            }
            $balance[] = $item;
        }
        if ($exists === false) {
            $balance[] = [
                'currency_id' => $currency_id,
                'sum' => $sum,
            ];
        }
        $cashier->balance = $balance;
        $cashier->save();
    }
}
