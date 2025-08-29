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
            'status' => 'nullable|in:active,inactive',
        ]);

        $employee = Employee::create($data);

        return response()->json($employee, 201);
    }

    public function show(Request $request, Employee $employee)
    {
        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');

        // Load works with date filter + product relation
        $works = $employee->works()
            ->when($fromDate, fn($q) => $q->whereDate('work_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('work_date', '<=', $toDate))
            ->with('product')
            ->orderBy('work_date', 'asc')
            ->get();

        // Calculate totals
        $mainTotalProduct   = $works->sum('product_total');
        $mainTotalEmployee  = $works->sum('employee_total');
        $mainTotalSadi      = $works->sum('total_sadi');

        return response()->json([
            'id'                  => $employee->id,
            'name'                => $employee->name,
            'mobile'              => $employee->mobile,
            'gender'              => $employee->gender,
            'status'              => $employee->status,
            'works'               => $works,
            'main_total_product'  => $mainTotalProduct,
            'main_total_employee' => $mainTotalEmployee,
            'main_total_sadi'     => $mainTotalSadi,
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'mobile' => 'sometimes|string|unique:employees,mobile,' . $employee->id,
            'profile_pic' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'status' => 'nullable|in:active,inactive',
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
