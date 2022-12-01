<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Requests\Branch\BranchCreateRequest;
use App\Http\Requests\Branch\BranchUpdateRequest;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function create(BranchCreateRequest $request)
    {
        if ($request->user()->role != 'ceo') {
            return ApiResponse::error('Forbidden', 403);
        }
        Branch::create([
            'name' => $request->name,
            'is_main' => $request->is_main,
        ]);

        return ApiResponse::success();
    }

    public function show(Request $request)
    {
        $branches = Branch::all(['id', 'name', 'is_main']);

        return ApiResponse::success(data:$branches);
    }

    public function update(BranchUpdateRequest $request, Branch $branch)
    {
        if ($request->user()->role != 'ceo') {
            return ApiResponse::error('Forbidden', 403);
        }

        $branch->update([
            'name' => $request->name,
            'is_main' => $request->is_main,
        ]);

        return ApiResponse::success();
    }
}
