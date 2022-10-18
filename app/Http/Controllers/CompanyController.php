<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Requests\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function Show(Request $request)
    {
        $company = Company::find(1);
        return ApiResponse::success(data: [
            'name' => $company->name,
            'address' => $company->address,
            'phone' => $company->phone,
            'image' => $company->image
        ]);
    }

    public function Update(CompanyUpdateRequest $request)
    {
        $company = Company::find(1);
        $company->name = $request->name;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->image = $request->image;
        $company->save();
        return ApiResponse::success();
    }
}
