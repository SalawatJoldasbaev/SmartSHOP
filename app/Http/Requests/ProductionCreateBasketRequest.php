<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionCreateBasketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.product_id'=> 'required|exists:products,id',
            '*.count'=> 'required',
            '*.ingredients'=> 'required|array',
            '*.ingredients.*.warehouse_id'=> 'required|exists:ingredient_warehouses,id',
            '*.ingredients.*.ingredient_id'=> 'required|exists:ingredients,id',
            '*.ingredients.*.count'=> 'required',
        ];
    }
}
