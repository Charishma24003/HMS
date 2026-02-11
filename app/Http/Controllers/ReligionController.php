<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReligionController extends Controller
{
    public function index()
    {
        $religions = Religion::latest()->get();
        return view('masters.religion.index', compact('religions'));
    }

    public function create()
    {
        return view('masters.religion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'religion_name' => 'required|max:100|unique:religion_master',
            'status' => 'required'
        ]);

        Religion::create([
            'religion_name' => $request->religion_name,
            'status' => $request->status,
            'created_by' => 1
        ]);

        return redirect()->route('religion.index')
            ->with('success', 'Religion added successfully');
    }

    public function edit($id)
    {
        $religion = Religion::findOrFail($id);
        return view('masters.religion.edit', compact('religion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'religion_name' => "required|max:100|unique:religion_master,religion_name,$id",
            'status' => 'required'
        ]);

        $religion = Religion::findOrFail($id);

        $religion->update([
            'religion_name' => $request->religion_name,
            'status' => $request->status,
            'updated_by' => 1
        ]);

        return redirect()->route('religion.index')
            ->with('success', 'Religion updated successfully');
    }

    public function destroy($id)
    {
        $religion = Religion::findOrFail($id);
        $religion->delete();

        return redirect()->route('religion.index')
            ->with('success', 'Religion deleted successfully');
    }


    public function trash()
    {
        $religions = Religion::onlyTrashed()->get();
        return view('masters.religion.trash', compact('religions'));
    }

    public function restore($id)
    {
        Religion::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('religion.trash')->with('success', 'Religion restored');
    }

    public function forceDelete($id)
    {
        Religion::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('religion.trash')->with('success', 'Religion removed permanently');
    }




}
