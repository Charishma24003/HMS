<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;


class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('department_name')->paginate(10);
        return view('masters.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('masters.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => [
                'required',
                'max:100',
                Rule::unique('department_master', 'department_name')->whereNull('deleted_at'),
            ],
            'department_code' => [
                'required',
                'max:20',
                Rule::unique('department_master', 'department_code')->whereNull('deleted_at'),
            ],
            'description' => ['nullable'],
            'status' => ['required', Rule::in(['1', '0'])], // from select
        ]);
        Department::create([
            'department_name' => $request->department_name,
            'department_code' => strtoupper($request->department_code),
            'description'     => $request->description,
            'status'          => (bool)$request->status,
            'created_by'      => auth()->id() ?? null,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department added.');
    }

    public function edit(string $id)
    {
        $department = Department::findOrFail($id);
        return view('masters.departments.edit', compact('department'));
    }

    
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);
        $request->validate([
            'department_name' => [
                'required',
                'max:100',
                Rule::unique('department_master', 'department_name')
                    ->ignore($department->id, 'id')
                    ->whereNull('deleted_at'),
            ],
            'department_code' => [
                'required',
                'max:20',
                Rule::unique('department_master', 'department_code')
                    ->ignore($department->id, 'id')
                    ->whereNull('deleted_at'),
            ],
            'description' => ['nullable'],
            'status' => ['required', Rule::in(['1', '0'])], 
        ]);

        $department->update([
            'department_name' => $request->department_name,
            'department_code' => strtoupper($request->department_code),
            'description'     => $request->description,
            'status'          => (bool)$request->status,
            'updated_by'      => auth()->id() ?? null,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        
        if (Schema::hasTable('staff') && \DB::table('staff')->where('department_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete: department is assigned to staff.');
        }

        $department->delete(); 
        return back()->with('success', 'Department deleted.');
    }

    public function deletedHistory()
    {
        $departments = Department::onlyTrashed()
            ->orderBy('department_name')
            ->paginate(10);

        return view('masters.departments.deleted', compact('departments'));
    }

    public function restore(string $id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('departments.deleted')
            ->with('success', 'Department restored successfully.');
    }

    public function forceDelete(string $id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->forceDelete();

        return redirect()->route('departments.deleted')
            ->with('success', 'Department permanently deleted.');
    }

}
