<?php

namespace App\Http\Controllers\Api\Ingredient;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientCreateRequest;
use App\Http\Requests\IngredientUpdateRequest;
use App\Models\Ingredient;
use App\Models\IngredientProduct;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function create(IngredientCreateRequest $request)
    {
        $ingredient = Ingredient::create([
            'name'=> $request->name,
            'unit_id'=> $request->unit_id,
        ]);

        return ApiResponse::data(payload:[
            'id'=> $ingredient->id,
        ]);
    }

    public function update(IngredientUpdateRequest $request, Ingredient $ingredient)
    {
        $ingredient->update([
            'name'=> $request->name,
            'unit_id'=> $request->unit_id,
        ]);

        return ApiResponse::success();
    }

    public function delete(Request $request, Ingredient $ingredient)
    {
        $ingredientWarehouse = IngredientProduct::where('ingredient_id', $ingredient->id)->first();
        if (!$ingredientWarehouse) {
            $ingredient->delete();
            return ApiResponse::success();
        } else {
            return ApiResponse::error('not deleted', 409);
        }
    }

    public function index(Request $request)
    {
        $ingredient = Ingredient::all(['id', 'name', 'unit_id'])->toArray();
        return ApiResponse::data(payload:$ingredient);
    }
}
