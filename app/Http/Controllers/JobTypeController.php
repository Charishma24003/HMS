<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobTypeController extends Controller
{
    public function index()
    {
        $jobTypes = JobType::latest()->get();
        return view('masters.job_type.index', compact('jobTypes'));
    }

    public function create()
    {
        return view('masters.job_type.create');
    }

    public function store(Request $request)
    {
$request->validate([
    'job_type_code' => 'required|max:50|unique:job_type_master',
    'job_type_name' => 'required|max:100',
    'status' => 'required'
]);

JobType::create([
    'job_type_code' => $request->job_type_code,
    'job_type_name' => $request->job_type_name,
    'description'   => $request->description,
    'status'        => $request->status,
    'created_by'    => 1
]);


        return redirect()->route('job-type.index')
            ->with('success', 'Job Type added successfully');
    }

    public function edit($id)
    {
        $jobType = JobType::findOrFail($id);
        return view('masters.job_type.edit', compact('jobType'));
    }

    public function update(Request $request, $id)
    {
$request->validate([
    'job_type_code' => "required|max:50|unique:job_type_master,job_type_code,$id",
    'job_type_name' => 'required|max:100',
    'status' => 'required'
]);

$jobType->update([
    'job_type_code' => $request->job_type_code,
    'job_type_name' => $request->job_type_name,
    'description'   => $request->description,
    'status'        => $request->status,
    'updated_by'    => 1
]);


        return redirect()->route('job-type.index')
            ->with('success', 'Job Type updated successfully');
    }

    public function destroy($id)
    {
        $jobType = JobType::findOrFail($id);
        $jobType->delete();

        return redirect()->route('job-type.index')
            ->with('success', 'Job Type deleted successfully');
    }

    public function trash()
    {
        $jobTypes = JobType::onlyTrashed()->get();
        return view('masters.job_type.trash', compact('jobTypes'));
    }

    public function restore($id)
    {
        JobType::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('job-type.trash')
            ->with('success', 'Job Type restored');
    }

    public function forceDelete($id)
    {
        JobType::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('job-type.trash')
            ->with('success', 'Job Type removed permanently');
    }
}
