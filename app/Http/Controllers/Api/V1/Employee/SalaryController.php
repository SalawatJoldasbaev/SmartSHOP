<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Api\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetSalaryRequest;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function show(Request $request)
    {
        $employee_id = $request->employee_id;
        $from = $request->from;
        $to = $request->to;

        $salaries = Salary::when($employee_id, function ($query) use ($employee_id) {
            return $query->where('employee_id', $employee_id);
        })->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)->get()->collect()->groupBy('employee_id');
        $final = [];
        foreach ($salaries as $key => $value) {
            $employee = Employee::find($key);
            $final[] = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'phone' => $employee->phone,
                    'salary' => $employee->salary,
                    'flex' => $employee->flex,
                    'role' => $employee->role,
                ],
                'sum' => collect($value)->sum('salary'),
            ];
        }

        return ApiResponse::success(data:$final);
    }

    public function monthly(Request $request)
    {
        $employee_id = $request->employee_id;
        $from = $request->from;
        $to = $request->to;

        $salaries = Salary::select('date', 'salary', DB::raw('MONTH(date) as month'), DB::raw('YEAR(date) as year'), DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date)) as monthyear"))->where('employee_id', $employee_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->get()->collect();
        $salaries = $salaries->sortBy('month')->sortBy('year')->groupBy('monthyear');
        $final = [];
        foreach ($salaries as $key => $value) {
            $final[] = [
                'month' => $value[0]->month,
                'year' => $value[0]->year,
                'sum' => collect($value)->sum('salary'),
            ];
        }

        return ApiResponse::success(data:$final);
    }

    public function setSalary(SetSalaryRequest $request)
    {
        try {
            $this->authorize('update', Employee::class);
        } catch (\Throwable$th) {
            return ApiResponse::error('This action is unauthorized.', 403);
        }

        $employee_id = $request->employee_id;
        $everyone = $request->is_everyone ?? false;
        if ($everyone === true) {
            $employees = Employee::query()->update([
                'salary' => $request->salary,
                'flex' => $request->flex,
            ]);
        } else {
            $employee = Employee::find($employee_id);
            $employee->update([
                'salary' => $request->salary,
                'flex' => $request->flex,
            ]);
        }

        return ApiResponse::success();
    }
}
