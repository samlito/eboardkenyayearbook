<?php

namespace App\Http\Services;

use App\Models\Almanac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlmanacService
{
    public function store(Request $request): Almanac
    {
        $request->validate([
            'fy_id' => 'required|exists:financial_years,id',
            'budget' => 'required|numeric|min:0',
        ]);

        $currentYear = date('Y');

        $lastAlmanac = Almanac::whereYear('created_at', $currentYear)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastAlmanac && preg_match('/ALM-(\d+)-\d{4}/', $lastAlmanac->serialno, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        $serialNo = 'ALM-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '-' . $currentYear;

        $almanac = new Almanac();
        $almanac->fy_id = $request->fy_id;
        $almanac->budget = $request->budget;
        $almanac->status = 'Pending';
        $almanac->serialno = $serialNo;
        $almanac->created_by = Auth::user()->name;
        $almanac->save();

        // Log to audit trail
        AuditLogService::log($request, 'INSERT', 'almanacs', $almanac->id, null, $almanac->toArray());

        return $almanac;
    }
}
