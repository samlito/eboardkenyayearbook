<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Almanac;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;
class GenerateAlmanacPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $almanacId;
    public function __construct($almanacId)
    {
        $this->almanacId = $almanacId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle0()
    {
        $almanac = Almanac::with('schedules')->findOrFail($this->almanacId);

        $logoSetting = setting('logo_letterhead') ?: setting('logo_main');
        $logoPath = $logoSetting ? public_path($logoSetting) : public_path('images/banner.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/banner.png');
        }

        $data = [
            'institutionName' => setting('institution_name', 'eBoard System'),
            'logo' => $logoPath,
            'almanac' => $almanac,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        // Generate the PDF using a Blade view
        $pdf = Pdf::loadView('pdf.almanac_report', $data);

        // Define the file path
        $filename = 'almanac_' . $this->almanacId . '.pdf';
        $filePath = 'public/reports/' . $filename;

        // Store the file
        Storage::put($filePath, $pdf->output());

        return Storage::url($filePath);
    }
    public function handle()
    {
        try {
            $almanac = Almanac::with('schedules')->findOrFail($this->almanacId);

            $logoSetting = setting('logo_letterhead') ?: setting('logo_main');
            $logoPath = $logoSetting ? public_path($logoSetting) : public_path('images/banner.png');
            if (!file_exists($logoPath)) {
                $logoPath = public_path('images/banner.png');
            }

            $data = [
                'institutionName' => setting('institution_name', 'eBoard System'),
                'logo' => $logoPath,
                'almanac' => $almanac,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            $pdf = Pdf::loadView('pdf.almanac_report', $data);

            $filename = 'almanac_' . $this->almanacId . '.pdf';
            $filePath = 'public/reports/' . $filename;

            Storage::put($filePath, $pdf->output());

            return $filePath;
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return null;
        }
    }
}
