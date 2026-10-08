<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Directorate;
use App\Http\Services\DivisionService;

class DivisionController extends Controller
{
    //
    protected $service;

    public function __construct(DivisionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $divisions = Division::with('directorate')->orderBy('name')->get();
        return view('backend.divisions.index', compact('divisions'));
    }

    public function create()
    {
        $directorates = Directorate::orderBy('name')->get();
        return view('backend.divisions.create', compact('directorates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name',
            'directorate_id' => 'nullable|exists:directorates,id',
            'requires_ceo_approval' => 'boolean',
        ]);

        $this->service->store($request);

        return redirect()->route('divisions.index')->with('success','Division created successfully');
    }

    public function edit(Division $division)
    {
        $directorates = Directorate::orderBy('name')->get();
        return view('backend.divisions.edit', compact('division', 'directorates'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name,' . $division->id,
            'directorate_id' => 'nullable|exists:directorates,id',
            'requires_ceo_approval' => 'boolean',
        ]);

        $this->service->update($request, $division);

        return redirect()->route('divisions.index')->with('success','Division updated successfully');
    }

    public function destroy(Request $request, Division $division)
    {
        $this->service->delete($request, $division);

        return redirect()->route('divisions.index')->with('success','Division deleted successfully');
    }
}
