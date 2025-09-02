<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeAdvance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeAdvanceController extends BaseController
{
    public function index(Request $request)
    {
        $fromDate   = $request->query('from_date');
        $toDate     = $request->query('to_date');
        $employeeId = $request->query('employee_id');

        $advances = EmployeeAdvance::where('created_by', Auth::id())
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($fromDate, fn($q) => $q->whereDate('advance_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('advance_date', '<=', $toDate))
            ->with('employee:id,name') 
            ->orderBy('advance_date', 'desc')
            ->get();

        return response()->json($advances);
    }

    public function show($id)
    {
        $work = EmployeeAdvance::with(['employee'])
            ->where('created_by', auth()->id()) 
            ->find($id);

        if (! $work) {
            return response()->json([
                'error' => 'Employee advance record not found or not accessible',
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
            'amount' => 'required|numeric',
            'advance_date'     => 'required|date',
        ]);

        $validated['created_by'] = Auth::id();

        $workAdvance = EmployeeAdvance::create($validated);

        return response()->json([
            'message' => 'Employee advance record added successfully',
            'data'    => $workAdvance,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();

        $advance = EmployeeAdvance::where('created_by', $userId)->find($id);

        if (! $advance) {
            return response()->json([
                'error' => 'Employee advance record not found or not accessible',
            ], 404);
        }

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
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
        ]);

        $advance->update($validated);

        return response()->json([
            'message' => 'Employee advance record updated successfully',
            'data'    => $advance,
        ]);
    }
}
