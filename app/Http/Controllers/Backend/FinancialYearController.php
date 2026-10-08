<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function index()
    {
        $financialYears = FinancialYear::orderByDesc('year')->get();
        return view('backend.financial_years.index', compact('financialYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_year' => 'required|integer|min:2020|max:2099',
        ]);

        $startYear = (int) $request->start_year;
        $endYear = $startYear + 1;
        $yearLabel = $startYear . '/' . $endYear;

        if (FinancialYear::where('year', $yearLabel)->exists()) {
            return redirect()->back()->with('error', 'Financial Year ' . $yearLabel . ' already exists.');
        }

        FinancialYear::create([
            'year'   => $yearLabel,
            'status' => $request->has('set_active') ? 'active' : 'inactive',
        ]);

        return redirect()->route('financial-years.index')->with('success', 'Financial Year ' . $yearLabel . ' created successfully.');
    }

    public function toggleStatus($id)
    {
        $fy = FinancialYear::findOrFail($id);
        $fy->status = $fy->status === 'active' ? 'inactive' : 'active';
        $fy->save();

        $msg = $fy->status === 'active'
            ? 'Financial Year ' . $fy->year . ' is now active.'
            : 'Financial Year ' . $fy->year . ' has been deactivated.';

        return redirect()->route('financial-years.index')->with('success', $msg);
    }

    public function destroy($id)
    {
        $fy = FinancialYear::findOrFail($id);

        if ($fy->almanacs()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete Financial Year ' . $fy->year . ' — it has almanacs linked to it.');
        }

        $fy->delete();
        return redirect()->route('financial-years.index')->with('success', 'Financial Year ' . $fy->year . ' deleted.');
    }
}
