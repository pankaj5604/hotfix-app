<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeWork;

class EmployeeWorkController extends Controller
{
    // ✅ Add Employee Work Entry
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

        // calculate totals
        $validated['product_total']  = $validated['product_rate'] * $validated['total_sadi'];
        $validated['employee_total'] = $validated['employee_rate'] * $validated['total_sadi'];

        // save work record
        $work = EmployeeWork::create($validated);

        return response()->json([
            'message' => 'Employee work record added successfully',
            'data'    => $work,
        ], 201);
    }

    // ✅ List Employee Work
    public function index()
    {
        return response()->json(EmployeeWork::with(['employee', 'product'])->get());
    }
}