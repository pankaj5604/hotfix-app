<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:employees,mobile',
            'profile_pic' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'status' => 'required|in:active,inactive',
        ]);

        $employee = Employee::create($data);

        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'mobile' => 'sometimes|string|unique:employees,mobile,' . $employee->id,
            'profile_pic' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $employee->update($data);

        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }
}
