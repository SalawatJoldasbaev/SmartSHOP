<?php

namespace App\Http\Requests;

use App\Http\Controllers\Api\V1\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WarehouseAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.product_id' => 'required|exists:products,id',
            '*.count' => 'required|numeric|min:0',
            '*.unit_id' => 'required|exists:units,id',
            '*.price' => 'required|array',
            '*.price.currency_id' => 'required|exists:currencies,id',
            '*.price.price' => 'required',

            '*.max_price' => 'required|array',
            '*.max_price.currency_id' => 'required|exists:currencies,id',
            '*.max_price.price' => 'required',

            '*.min_price' => 'required|array',
            '*.min_price.currency_id' => 'required|exists:currencies,id',
            '*.min_price.price' => 'required',

            '*.whole_price' => 'required|array',
            '*.whole_price.currency_id' => 'required|exists:currencies,id',
            '*.whole_price.price' => 'required',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponse::error($validator->errors()->first(), 422));
    }
}
