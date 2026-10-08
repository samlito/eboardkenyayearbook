<?php

namespace App\Helpers;

use Carbon\Carbon;

class AlmanacHelper
{
    /**
     * Get current calendar year (Jan 1 – Dec 31)
     */
    public static function currentCalendarYear(): int
    {
        return Carbon::now()->year;
    }

    /**
     * Get current financial year (Jul 1 – Jun 30)
     */
    public static function currentFinancialYear(): string
    {
        $now = Carbon::now();
        $year = $now->year;

        if ($now->month >= 7) {
            // July – December: current year / next year
            return "{$year}/" . ($year + 1);
        } else {
            // January – June: previous year / current year
            return ($year - 1) . "/{$year}";
        }
    }

    /**
     * Get current financial quarter (Q1 to Q4)
     */
    public static function currentFinancialQuarter(): string
    {
        $month = Carbon::now()->month;

        switch (true) {
            case in_array($month, [7, 8, 9]):
                return 'Q1';
            case in_array($month, [10, 11, 12]):
                return 'Q2';
            case in_array($month, [1, 2, 3]):
                return 'Q3';
            case in_array($month, [4, 5, 6]):
                return 'Q4';
            default:
                return 'Unknown';
        }
    }
}
