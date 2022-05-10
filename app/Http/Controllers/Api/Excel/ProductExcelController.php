<?php

namespace App\Http\Controllers\Api\Excel;

use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

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
