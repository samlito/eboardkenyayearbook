<?php

namespace App\Http\Controllers;

use App\Models\QuickSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickSignatureController extends Controller
{
    public function index()
    {
        $signatures = QuickSignature::latest()->get();
        return view('backend.quicksign.index', compact('signatures'));
    }

    public function create()
    {
        return view('backend.quicksign.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_title' => 'required|string|max:255',
            'prepared_by'    => 'required|string|max:255',
            'notes'          => 'nullable|string',
            'signer_name'    => 'required|string|max:255',
            'signature_data' => 'required|string',
        ]);

        QuickSignature::create([
            'document_title' => $request->document_title,
            'prepared_by'    => $request->prepared_by,
            'notes'          => $request->notes,
            'signer_name'    => $request->signer_name,
            'signature_data' => $request->signature_data,
            'signed_by'      => Auth::id(),
        ]);

        return redirect()->route('quicksign.index')->with([
            'message'    => 'Document signed successfully!',
            'alert-type' => 'success',
        ]);
    }
}
