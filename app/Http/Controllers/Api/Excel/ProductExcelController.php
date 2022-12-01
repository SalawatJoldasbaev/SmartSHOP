<?php

namespace App\Http\Controllers\Api\Excel;

use App\Exports\ProductExport;
use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductExcelController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new ProductImport, $file);

        return ApiResponse::success();
    }
}
