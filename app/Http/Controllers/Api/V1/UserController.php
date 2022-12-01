<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'full_name' => 'required',
            'phone' => 'required|unique:users,phone',
            'type' => 'required',
            'tin' => 'required_if:type,Y|unique:users,tin|integer',
            'about' => 'nullable',
        ]);

        if ($validation->fails()) {
            return ApiResponse::data(false, $validation->errors()->first(), code: 422);
        }

        $user = User::create([
            'branch_id' => $request->user()->branch_id,

            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'type' => $request->type,
            'tin' => $request->type == 'Y' ? $request->tin : null,
            'balance' => 0,
            'about' => $request->about,
        ]);

        return ApiResponse::success(data: [
            'id' => $user->id,
        ], code: 201);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $type = $request->type;
        $debt = User::where('balance', '<', 0);
        $users = User::select('id', 'full_name', 'phone', 'type', 'tin', 'balance', 'about', 'created_at as registered_at')
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })->orderBy('balance');
        $users = Search::new()->add($users, ['full_name', 'phone'])
            ->beginWithWildcard()
            ->paginate(60)
            ->search($search);
        $final = [
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
            'data' => [
                'debt' => $debt->sum('balance'),
                'clients' => [],
            ],
        ];

        foreach ($users as $user) {
            $final['data']['clients'][] = $user;
        }

        return ApiResponse::success(data: $final);
    }

    public function update(UserUpdateRequest $request)
    {
        $client_id = $request->client_id;
        try {
            $client = User::findOrFail($client_id);
            try {
                $this->authorize('update', $client);
            } catch (\Throwable $th) {
                return ApiResponse::error('This action is unauthorized.', 403);
            }
        } catch (\Throwable $th) {
            return ApiResponse::error('client not found', 404);
        }

        $client->update($request->all());

        return ApiResponse::success();
    }
}
