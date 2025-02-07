<?php

namespace App\Http\Controllers\Api\Pincode;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PincodeController extends Controller
{
    public function generate()
    {
        $pincode = rand(1000, 9999);
        $exist_pincode = Employee::where('pincode', md5($pincode))->first();
        while ($exist_pincode) {
            $pincode = rand(1000, 9999);
            $exist_pincode = Employee::where('pincode', md5($pincode))->first();
        }
        return ApiResponse::success(data: [
            'pincode' => $pincode
        ]);
    }
}
