<?php

namespace App\Http\Requests;

use App\Http\Controllers\Api\V1\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IngredientWarehouseCreateRequest extends FormRequest
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
            'usd_rate'=> 'required',
            'ingredients'=> 'required|array',
            'ingredients.*.ingredient_id'=> 'required|exists:ingredients,id',
            'ingredients.*.count'=> 'required',
            'ingredients.*.price'=> 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponse::error($validator->errors()->first(), 422));
    }
}
