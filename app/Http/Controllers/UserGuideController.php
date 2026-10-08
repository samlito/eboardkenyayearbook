<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class UserGuideController extends Controller
{
    public function index()
    {
        return view('help.user_guide');
    }

    public function pdf()
    {
        $acronym = setting('institution_acronym', 'ORG');

        $pdf = Pdf::loadView('help.user_guide_pdf', [
            'acronym'         => $acronym,
            'institutionName' => setting('institution_name', 'eBoard System'),
            'generatedOn'     => now()->format('d F Y'),
        ])->setPaper('a4');

        return $pdf->download("{$acronym}_eBoard_User_Guide.pdf");
    }
}
