<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $employees = Employee::all('id', 'avatar', 'name', 'phone', 'salary', 'flex', 'role');
        return ApiResponse::success(data:$employees);
    }
    public function register(Request $request)
    {
        try {
            $this->authorize('create', Employee::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $validation = Validator::make($request->all(), [
            'phone' => 'required|unique:employees,phone',
            'avatar' => 'nullable',
            'name' => 'required|string',
            'password' => 'nullable',
            'role' => 'required',
            'pincode' => 'required',
            'flex' => 'required',
            'salary' => 'required',
        ]);

        if ($validation->fails()) {
            return ApiResponse::data(false, $validation->errors()->first(), code:422);
        }

        Employee::create([
            'avatar' => $request->avatar,
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'pincode' => md5($request->pincode),
            'flex' => $request->flex,
            'salary' => $request->salary,
            'role' => $request->role,
        ]);

        return ApiResponse::success();
    }

    public function login(Request $request)
    {
        $phone = $request->phone;
        $password = $request->password;
        $pincode = md5($request->pincode);
        $user = Employee::query();

        if (isset($phone) and isset($password)) {
            $user = $user->where('phone', $phone)->first();
            if (!$user or !Hash::check($password, $user->password)) {
                return ApiResponse::message('phone or password incorrect', 401);
            }
            $token = $user->createToken('web application: ' . $request->header('User-Agent'))->plainTextToken;
        } elseif (isset($pincode)) {
            $user = $user->where('pincode', $pincode)->first();
            if (!$user) {
                return ApiResponse::message('pincode not found', 401);
            }
            $token = $user->createToken('mobile application:' . $request->header('User-Agent'))->plainTextToken;
        } else {
            return ApiResponse::error('unknown error', 520);
        }

        return ApiResponse::data(true, 'successful', [
            'id' => $user->id,
            'avatar' => $user->avatar,
            'role' => $user->role,
            'phone' => $user->phone,
            'name' => $user->name,
            'token' => $token,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $this->authorize('update', Employee::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $validation = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'avatar' => 'nullable',
            'phone' => 'required',
            'name' => 'required|string',
            'password' => 'nullable',
            'role' => 'required',
            'pincode' => 'nullable',
        ]);

        if ($validation->fails()) {
            return ApiResponse::data(false, $validation->errors()->first(), code:422);
        }
        $employee = Employee::find($request->employee_id);
        $data = [
            'avatar' => $request->avatar,
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
        ];
        if (!is_null($request->password)) {
            $data['password'] = Hash::make($request->password);
        }
        if (!is_null($request->pincode)) {
            $data['pincode'] = md5($request->pincode);
        }

        $employee->update($data);
        return ApiResponse::success();
    }
}
