<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeWork;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeWork::with([
                'employee:id,name',
                'product:id,type',
            ])
            ->where('created_by', auth()->id());

        if ($request->filled('date')) {
            $query->whereDate('work_date', $request->date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $works = $query->orderBy('work_date', 'asc')->get();

        $main_total_product = $works->sum(function ($work) {
            return $work->product_rate * $work->total_sadi;
        });

        $main_total_employee_rate = $works->sum(function ($work) {
            return $work->employee_rate * $work->total_sadi;
        });

        $main_total_sari = $works->sum('total_sadi');

        return response()->json([
            'works' => $works,
            'main_total_product' => $main_total_product,
            'main_total_employee_rate' => $main_total_employee_rate,
            'main_total_sari' => $main_total_sari,
        ]);
    }

    public function show($id)
    {
        $work = EmployeeWork::with(['employee', 'product'])
            ->where('created_by', auth()->id()) 
            ->find($id);

        if (! $work) {
            return response()->json([
                'error' => 'Employee work record not found or not accessible',
            ], 404);
        }

        return response()->json($work);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'employee_id'   => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($userId) {
                    if (!\App\Models\Employee::where('id', $value)->where('created_by', $userId)->exists()) {
                        $fail("The selected employee is invalid.");
                    }
                },
            ],
            'product_id'    => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($userId) {
                    if (!\App\Models\Product::where('id', $value)->where('created_by', $userId)->exists()) {
                        $fail("The selected product is invalid.");
                    }
                },
            ],
            'employee_rate' => 'required|numeric',
            'product_rate'  => 'required|numeric',
            'total_sadi'    => 'required|integer|min:0',
            'work_date'     => 'required|date',
        ]);

        $validated['product_total']  = $validated['product_rate'] * $validated['total_sadi'];
        $validated['employee_total'] = $validated['employee_rate'] * $validated['total_sadi'];

        $validated['created_by'] = Auth::id();

        $work = EmployeeWork::create($validated);

        return response()->json([
            'message' => 'Employee work record added successfully',
            'data'    => $work,
        ], 201);
    }

    public function update(Request $request, EmployeeWork $employeeWork)
    {
        $validated = $request->validate([
            'employee_id'   => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Employee::where('id', $value)
                        ->where('created_by', auth()->id())
                        ->exists()) {
                        $fail('The selected employee is invalid or not created by you.');
                    }
                },
            ],
            'product_id'    => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Product::where('id', $value)
                        ->where('created_by', auth()->id())
                        ->exists()) {
                        $fail('The selected product is invalid or not created by you.');
                    }
                },
            ],
            'employee_rate' => 'sometimes|numeric',
            'product_rate'  => 'sometimes|numeric',
            'total_sadi'    => 'sometimes|integer|min:0',
            'work_date'     => 'sometimes|date',
        ]);

        unset($validated['created_by']);

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