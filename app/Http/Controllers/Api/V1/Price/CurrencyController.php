<?php

namespace App\Http\Controllers\Api\V1\Price;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Forex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $currencies = Currency::all();
        $final_response = [];
        $temp = [];
        foreach ($currencies as $currency) {
            $temp = [
                'id' => $currency->id,
                'name' => $currency->name,
                'code' => $currency->code,
                'rate' => [],
            ];
            if (isset($currency->rate)) {
                foreach ($currency->rate as $rate) {
                    $currency_item = Currency::find($rate->to_currency_id);
                    $temp['rate'][] = [
                        'id' => $currency_item->id,
                        'name' => $currency_item->name,
                        'code' => $currency_item->code,
                        'rate' => $rate->rate,
                    ];
                }
            }
            $final_response[] = $temp;
            $temp = [];
        }
        return ApiResponse::success(data:$final_response);
    }

    public function setCurrency(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'rate' => 'required',
            'currency_id' => 'required|exists:currencies,id',
            'to_curreny_id' => 'required|exists:currencies,id',
        ]);
        if ($validation->fails()) {
            return ApiResponse::error($validation->errors()->first(), 422);
        }

        $currency_id = $request->currency_id;
        $to_currency_id = $request->to_curreny_id;
        $rate = $request->rate;

        $currency = Forex::where('currency_id', $currency_id)->where('to_currency_id', $to_currency_id)->first();
        if (!$currency) {
            Forex::create([
                'currency_id' => $currency_id,
                'to_currency_id' => $to_currency_id,
                'rate' => $rate,
            ]);
        } else {
            $currency->update([
                'rate' => $rate,
            ]);
        }
        return ApiResponse::success();
    }
}
