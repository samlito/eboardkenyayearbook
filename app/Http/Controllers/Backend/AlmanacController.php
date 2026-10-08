<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Services\AlmanacScheduleService;
use App\Http\Services\AlmanacService;
use App\Http\Services\AuditLogService;
use App\Jobs\GenerateAlmanacPdf;
use App\Models\AlmanacSchedule;
use App\Models\BoardCommittee;
use App\Models\BoardMeeting;
use App\Models\FinancialYear;
use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Almanac;
use Illuminate\Support\Facades\Storage;
use App\Models\AlmanacScheduleVersion;


class AlmanacController extends Controller
{

    public function generatePdf0(Request $request)
    {
        $almanacId = $request->almanac_id;

        // Dispatch the job
        $job = new GenerateAlmanacPdf($almanacId);
        $pdfPath = $job->handle();

        return response()->json([
            'status' => 'success',
            'pdf_url' => Storage::url($pdfPath)
        ]);
    }
    public function generatePdf(Request $request)
    {
        $almanacId = $request->input('almanac_id');

        if (!$almanacId) {
            return response()->json(['status' => 'error', 'message' => 'Missing Almanac ID'], 400);
        }

        try {
            $job = new GenerateAlmanacPdf($almanacId);
            $pdfPath = $job->handle(); // Normally, you would dispatch it, but let's test this way first.

            if (!$pdfPath || !Storage::exists($pdfPath)) {
                return response()->json(['status' => 'error', 'message' => 'PDF not generated'], 500);
            }

            return response()->json([
                'status' => 'success',
                'pdf_url' => Storage::url($pdfPath)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function generatePdf23($id)
    {
        try {
            // Fetch the Almanac
            $almanac = Almanac::findOrFail($id);
            $schedules = AlmanacSchedule::where('almanac_id', $id)->get();

            // Debugging Step
            \Log::info("Generating PDF for Almanac ID: " . $id);

            // Generate PDF
            $pdf = Pdf::loadView('pdf.almanac_report2', [
                'almanac' => $almanac,
                'schedules' => $schedules,
                'timestamp' => Carbon::now()->format('d M Y, H:i:s'),
            ]);

            return $pdf->stream('almanac_report.pdf');
        } catch (\Exception $e) {
            \Log::error("Error generating PDF: " . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }
    public function generatePdf2(Request $request, $id)
    {
        try {
            \Log::info("Received Almanac ID: " . $id);

            // Fetch the Almanac with schedules
            $almanac = Almanac::with('schedules')->findOrFail($id);

            \Log::info("Generating PDF for Almanac ID: " . $id . " | Schedules Count: " . $almanac->schedules->count());

            // Generate PDF
            $pdf = Pdf::loadView('pdf.almanac_report2', [
                'almanac' => $almanac,
                'schedules' => $almanac->schedules,
                'timestamp' => now()->format('d M Y, H:i:s'),
            ]);

            return $pdf->stream('almanac_report.pdf');
        } catch (\Exception $e) {
            \Log::error("Error generating PDF: " . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }
    public function almanacindex(){
        $almanacs = Almanac::where('status', '!=', 'Rejected')->latest()->get();
        return view('backend.mainalmanac.index', compact('almanacs'));
    }

    public function uploadSigned(Request $request, $id)
    {
        $request->validate([
            'signed_almanac' => 'required|mimes:pdf|max:5120', // up to 5MB
        ]);

        $almanac = Almanac::findOrFail($id);

        if ($request->hasFile('signed_almanac')) {
            $path = $request->file('signed_almanac')
                ->store('almanac/signed', 'public');

            // assuming you have a column in `almanacs` table
            $almanac->signed_almanac = $path;
            $almanac->save();
        }

        return back()->with('success', 'Signed Almanac uploaded successfully.');
    }


    public function almanacrejectedindex(){
        $almanacs=Almanac::where('status','Rejected')->latest()->get();
        return view('backend.mainalmanac.amendindex',compact('almanacs'));
    }

    public function almaniccreate()
    {
        $financialyear = FinancialYear::active()->orderByDesc('year')->get();

        return view('backend.mainalmanac.create',compact('financialyear'));
    }

    public function show($id)
    {
        // Get all schedules for this almanac, with their versions
        $allshcedules = AlmanacSchedule::where('almanac_id', $id)
            ->with('versions')
            ->get();

        $almanac = Almanac::findOrFail($id);

        $initialBudget = $almanac->budget;
        $totalScheduleBudget = $allshcedules->sum('budget');
        $remainingBudget = $initialBudget - $totalScheduleBudget;

        return view('backend.mainalmanac.show', compact(
            'allshcedules',
            'almanac',
            'remainingBudget',
            'totalScheduleBudget'
        ));
    }


    public function sumall($id){
        $almanac = Almanac::findOrFail($id);
        $initialbudget= $almanac->budget;


    }

    public function index(){
        $almanacs=AlmanacSchedule::latest()->get();
        return view('backend.almanac.index',compact('almanacs'));
    }

    public function create($id)
    {
        $almanac = Almanac::findOrFail($id);
        $financialyear = FinancialYear::all();
        $meeting=Meeting::all();
        $committees = BoardCommittee::all();
        return view('backend.almanac.create',compact('financialyear','meeting','almanac','committees'));
    }

    public function createtoapproved($id)
    {
        $almanac = Almanac::findOrFail($id);
        $financialyear = FinancialYear::all();
        $meeting=Meeting::all();
        $committees = BoardCommittee::all();
        return view('backend.almanac.createapproved',compact('financialyear','meeting','almanac','committees'));
    }

    public function almanacstore0(Request $request)
    {
        $request->validate([
            'fy_id' => 'required|exists:financial_years,id',
            'budget' => 'required|numeric|min:0',

        ]);


        // Get the current year
        $currentYear = date('Y');

        // Find the last record for the current year
        $lastAlmanac = Almanac::whereYear('created_at', $currentYear)
            ->orderBy('id', 'desc')
            ->first();

        // Extract the last serial number and increment
        if ($lastAlmanac && preg_match('/ALM-(\d+)-\d{4}/', $lastAlmanac->serialno, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        // Format the number as three digits
        $serialNo = 'ALM-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '-' . $currentYear;

        $almanac = new Almanac();
        $almanac->fy_id = $request->fy_id;
        $almanac->budget = $request->budget;
        $almanac->status ='Pending';
        $almanac->serialno =$serialNo;
        $almanac->created_by = auth()->user()->name;
        $almanac->save();

        return redirect()->route('all.almanac.main')->with('success', 'Almanac created successfully!');
    }


    public function almanacstore(Request $request, AlmanacService $service)
    {
        $service->store($request);

        return redirect()->route('all.almanac.main')->with('success', 'Almanac created successfully!');
    }

    public function store0(Request $request,$id)
    {
        $request->validate([

            'meeting_id' => 'required|exists:meetings,id',
            'meeting_date' => 'required|date',
            'purpose' => 'required|string',
            'budget' => 'required|numeric|min:0',

        ]);

        $almanac = new AlmanacSchedule();
        $almanac->almanac_id = $id;
        $almanac->meeting_id = $request->meeting_id;
        $almanac->meeting_date = $request->meeting_date;
        $almanac->purpose = $request->purpose;
        $almanac->budget = $request->budget;
        $almanac->status ='Pending';
        $almanac->created_by = auth()->user()->name;
        $almanac->save();

        return redirect()->route('all.almanac.main')->with('success', 'Almanac schedule created successfully!');
    }


    public function store(Request $request, $id, AlmanacScheduleService $service)
    {
        $service->store($request, $id);

//        return redirect()->route('all.almanac.main')->with('success', 'Almanac schedule created successfully!');
        return redirect()->route('almanac.show.all',$id)->with('success', 'Almanac schedule created successfully!');

    }

    public function storeAp(Request $request, $id, AlmanacScheduleService $service)
    {
        $service->storeAp($request, $id);

//        return redirect()->route('all.almanac.main')->with('success', 'Almanac schedule created successfully!');
        return redirect()->route('almanac.show.all',$id)->with('success', 'Almanac schedule created successfully!');

    }


    public function edit($id)
    {
        $almanac = AlmanacSchedule::findOrFail($id);
        $meetings = BoardCommittee::all();
        return view('backend.almanac.edit', compact('almanac', 'meetings'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'meeting_id' => 'required|exists:board_committees,id',
            'meeting_date' => 'required|date',
            'purpose' => 'required|string',
            'budget' => 'required|numeric|min:0',

        ]);

        $almanac = AlmanacSchedule::findOrFail($id);

        // ✅ Save current version before updating
        AlmanacScheduleVersion::create([
            'almanac_schedule_id' => $almanac->id,
            'version_number' => $almanac->versions()->count() + 1,
            'almanac_id' => $almanac->almanac_id,
            'meeting_id' => $almanac->meeting_id,
            'board_committee_id' => $almanac->board_committee_id,
            'meeting_date' => $almanac->meeting_date,
            'purpose' => $almanac->purpose,
            'budget' => $almanac->budget,
            'status' => $almanac->status,
            'change_reason'=> $request->change_reason,
            'created_by' => auth()->user()->name,
        ]);

        // ✅ Now update the record
        $almanac->meeting_id = $request->meeting_id;
        $almanac->meeting_date = $request->meeting_date;
        $almanac->purpose = $request->purpose;
        $almanac->budget = $request->budget;
        $almanac->status = $request->status;
        $almanac->save();

        return redirect()->route('almanac.show.all', $almanac->almanac_id)
            ->with('success', 'Almanac schedule updated and version saved!');
    }

//    public function update(Request $request, $id)
//    {
//        $request->validate([
//            'meeting_id' => 'required|exists:meetings,id',
//            'meeting_date' => 'required|date',
//            'purpose' => 'required|string',
//            'budget' => 'required|numeric|min:0',
//        ]);
//
//        $almanac = AlmanacSchedule::findOrFail($id);
//        $almanac->meeting_id = $request->meeting_id;
//        $almanac->meeting_date = $request->meeting_date;
//        $almanac->purpose = $request->purpose;
//        $almanac->budget = $request->budget;
//        $almanac->status = $request->status;
//        $almanac->save();
//
//        return redirect()->route('almanac.show.all', $almanac->almanac_id)->with('success', 'Almanac schedule updated successfully!');
//    }
    public function destroy(Request $request, $id)
    {
        $schedule = AlmanacSchedule::findOrFail($id);

        // Save version before soft-deleting
        \App\Models\AlmanacScheduleVersion::create([
            'almanac_schedule_id' => $schedule->id,
            'version_number' => $schedule->versions()->count() + 1,
            'almanac_id' => $schedule->almanac_id,
            'meeting_id' => $schedule->meeting_id,
            'board_committee_id' => $schedule->board_committee_id,
            'meeting_date' => $schedule->meeting_date,
            'purpose' => $schedule->purpose,
            'budget' => $schedule->budget,
            'status' => 'deleted',
            'change_reason' => 'Deleted',
            'created_by' => auth()->user()->name,
        ]);

        $schedule->delete(); // this now performs a soft delete

        return redirect()->route('almanac.show.all', $schedule->almanac_id)
            ->with('success', 'Schedule soft-deleted and version saved.');
    }


//    public function destroy($id)
//    {
//        $almanac = AlmanacSchedule::findOrFail($id);
//        $almanac->delete();
//
//        return redirect()->route('almanac.show.all', $almanac->almanac_id)->with('success', 'Almanac schedule deleted successfully!');
//    }




    public function submitForApproval(Request $request,$id)
    {
        $almanac = Almanac::findOrFail($id);

//        if ($almanac->status !== 'Pending') {
//            return back()->with('error', 'Only pending almanacs can be submitted.');
//        }
        if (!in_array($almanac->status, ['Pending', 'Rejected'])) {
            return back()->with('error', 'Only pending or rejected almanacs can be submitted.');
        }

        $almanac->status = 'Awaiting Approval';
        $almanac->save();

        AuditLogService::log($request, 'SUBMIT', 'almanacs', $id, null, ['status' => 'Awaiting Approval']);
        return back()->with('success', 'Almanac submitted successfully for approval.');
    }


    public function downloadPdf($id)
    {
        $almanac = Almanac::with('financialYear', 'schedules.boardCommittee', 'schedules.meeting')->findOrFail($id);

        $pdf = Pdf::loadView('backend.almanac.pdf', compact('almanac'));
        return $pdf->download(setting('institution_acronym', 'ORG') . '_Board_Almanac_' . $almanac->financialYear->name . '.pdf');
    }

}
