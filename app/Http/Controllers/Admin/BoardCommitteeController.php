<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\BoardCommitteeService;
class BoardCommitteeController extends Controller
{
    protected $service;

    public function __construct(BoardCommitteeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $committees = $this->service->getAll();
        return view('backend.committees.index', compact('committees'));
    }

    public function create()
    {
        return view('backend.committees.create');
    }

    public function store(Request $request)
    {
        $this->service->store($request);
        return redirect()->route('committees.index')->with('success', 'Board committee created successfully.');
    }

    public function edit($id)
    {
        $committee = $this->service->find($id);
        return view('backend.committees.edit', compact('committee'));
    }

    public function update(Request $request, $id)
    {
        $this->service->update($request, $id);
        return redirect()->route('committees.index')->with('success', 'Board committee updated successfully.');
    }
}
