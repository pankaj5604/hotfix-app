<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(
            Employee::where('created_by', Auth::id())->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            // 'mobile'      => [
            //     'required',
            //     'string',
            //     Rule::unique('employees', 'mobile')->where(function ($query) {
            //         return $query->where('created_by', auth()->id());
            //     }),
            // ],
            'mobile' => 'nullable|string',
            'profile_pic' => 'nullable|string',
            'gender'      => 'nullable|in:male,female',
            'status'      => 'nullable|in:active,inactive',
        ]);

        $data['created_by'] = Auth::id();

        $employee = Employee::create($data);

        return response()->json([
            'message'  => 'Employee created successfully',
            'employee' => $employee,
        ], 201);
    }

    public function show(Request $request, Employee $employee)
    {
        if ($employee->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');

        $works = $employee->works()
            ->when($fromDate, fn($q) => $q->whereDate('work_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('work_date', '<=', $toDate))
            ->with(['product:id,type'])
            ->orderBy('work_date', 'asc')
            ->get();

        return response()->json([
            'id'                  => $employee->id,
            'name'                => $employee->name,
            'mobile'              => $employee->mobile,
            'gender'              => $employee->gender,
            'status'              => $employee->status,
            'works'               => $works,
            'main_total_product'  => $works->sum('product_total'),
            'main_total_employee' => $works->sum('employee_total'),
            'main_total_sadi'     => $works->sum('total_sadi'),
        ]);
    }

    public function showPublic(Request $request,$id)
    {
        $employee = Employee::where("id",$id)->first();
      
        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');

        $works = $employee->works()
            ->when($fromDate, fn($q) => $q->whereDate('work_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('work_date', '<=', $toDate))
            ->with(['product:id,type'])
            ->orderBy('work_date', 'asc')
            ->get();

        return response()->json([
            'id'                  => $employee->id,
            'name'                => $employee->name,
            'mobile'              => $employee->mobile,
            'gender'              => $employee->gender,
            'status'              => $employee->status,
            'works'               => $works,
            'main_total_product'  => $works->sum('product_total'),
            'main_total_employee' => $works->sum('employee_total'),
            'main_total_sadi'     => $works->sum('total_sadi'),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        if ($employee->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            // 'mobile'      => [
            //     'sometimes',
            //     'string',
            //     Rule::unique('employees', 'mobile')
            //         ->where(fn ($q) => $q->where('created_by', auth()->id()))
            //         ->ignore($employee->id),
            // ],
            'mobile' => 'nullable|string',
            'profile_pic' => 'nullable|string',
            'gender'      => 'nullable|in:male,female',
            'status'      => 'nullable|in:active,inactive',
        ]);

        $employee->update($data);

        return response()->json([
            'message'  => 'Employee updated successfully',
            'employee' => $employee,
        ]);
    }

    public function destroy(Employee $employee)
    {
        if ($employee->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }
}