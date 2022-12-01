<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\WarehouseBasket;
use Illuminate\Http\Request;

class WarehouseDefectController extends Controller
{
    public function take(Request $request, WarehouseBasket $basket)
    {
        $basket->update([
            'status' => 'taken',
        ]);

        return ApiResponse::success();
    }
}
