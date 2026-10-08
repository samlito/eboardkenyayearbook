<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;


class AuditLogController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);

        $users = User::orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $tables  = AuditLog::select('table_name')->distinct()->pluck('table_name');

        return view('backend.audit_logs.index', compact('logs', 'users', 'actions', 'tables'));
    }



    public function exportPdf0(Request $request)
    {
//        dd('exportPdf');
        $logs = AuditLog::with('user')->latest()->get();

        $pdf =\Barryvdh\DomPDF\Facade\Pdf::loadView('backend.audit_logs.pdf', compact('logs'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('audit_logs_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportPdf(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get(); // no pagination, we need full dataset

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('backend.audit_logs.pdf', compact('logs'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('audit_logs_' . now()->format('Ymd_His') . '.pdf');
    }


}
