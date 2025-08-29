<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeWork;

class EmployeeWorkController extends Controller
{
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'product_id'    => 'required|exists:products,id',
            'employee_rate' => 'required|numeric',
            'product_rate'  => 'required|numeric',
            'total_sadi'    => 'required|integer|min:0',
            'work_date'     => 'required|date',
        ]);

        $validated['product_total']  = $validated['product_rate'] * $validated['total_sadi'];
        $validated['employee_total'] = $validated['employee_rate'] * $validated['total_sadi'];

        $work = EmployeeWork::create($validated);

        return response()->json([
            'message' => 'Employee work record added successfully',
            'data'    => $work,
        ], 201);
    }

    
    public function index()
    {
        return response()->json(EmployeeWork::with(['employee', 'product'])->get());
    }

    public function update(Request $request, EmployeeWork $employeeWork)
    {
        $validated = $request->validate([
            'employee_id'   => 'sometimes|exists:employees,id',
            'product_id'    => 'sometimes|exists:products,id',
            'employee_rate' => 'sometimes|numeric',
            'product_rate'  => 'sometimes|numeric',
            'total_sadi'    => 'sometimes|integer|min:0',
            'work_date'     => 'sometimes|date',
        ]);

        // if rate/sadi updated recalculate totals
        $totalSadi     = $validated['total_sadi']    ?? $employeeWork->total_sadi;
        $productRate   = $validated['product_rate']  ?? $employeeWork->product_rate;
        $employeeRate  = $validated['employee_rate'] ?? $employeeWork->employee_rate;

        $validated['product_total']  = $productRate * $totalSadi;
        $validated['employee_total'] = $employeeRate * $totalSadi;

        $employeeWork->update($validated);

        return response()->json([
            'message' => 'Employee work record updated successfully',
            'data'    => $employeeWork,
        ]);
    }
    
}