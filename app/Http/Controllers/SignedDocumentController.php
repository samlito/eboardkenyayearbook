<?php

namespace App\Http\Controllers;

use App\Models\SignedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignedDocumentController extends Controller
{
    /**
     * List all documents
     */
    public function index()
    {
        $documents = SignedDocument::with(['uploader', 'signer'])->latest()->get();
        return view('backend.pdfsign.index', compact('documents'));
    }

    /**
     * Show upload form
     */
    public function create()
    {
        return view('backend.pdfsign.upload');
    }

    /**
     * Store uploaded PDF
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf_file' => 'required|mimes:pdf|max:20480', // 20MB max
        ]);

        $path = $request->file('pdf_file')->store('documents/originals', 'public');

        $doc = SignedDocument::create([
            'title'       => $request->title,
            'original_pdf' => $path,
            'status'      => 'pending',
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('pdfsign.viewer', $doc->id)->with([
            'message'    => 'PDF uploaded! You can now review and sign it.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the PDF viewer + signature page
     */
    public function viewer(SignedDocument $document)
    {
        return view('backend.pdfsign.viewer', compact('document'));
    }

    /**
     * Save the signature (receives base64 signature + position data)
     */
    public function sign(Request $request, SignedDocument $document)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'signature_page' => 'required|integer|min:1',
            'signature_x'    => 'required|numeric',
            'signature_y'    => 'required|numeric',
        ]);

        $document->update([
            'signature_data' => $request->signature_data,
            'signature_page' => $request->signature_page,
            'signature_x'    => $request->signature_x,
            'signature_y'    => $request->signature_y,
            'status'         => 'signed',
            'signed_by'      => Auth::id(),
            'signed_at'      => now(),
        ]);

        return redirect()->route('pdfsign.viewer', $document->id)->with([
            'message'    => 'Document signed successfully!',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove signature (allow re-signing)
     */
    public function unsign(SignedDocument $document)
    {
        $document->update([
            'signature_data' => null,
            'signature_page' => null,
            'signature_x'    => null,
            'signature_y'    => null,
            'status'         => 'pending',
            'signed_by'      => null,
            'signed_at'      => null,
        ]);

        return redirect()->route('pdfsign.viewer', $document->id)->with([
            'message'    => 'Signature removed. You can re-sign.',
            'alert-type' => 'info',
        ]);
    }

    /**
     * Delete document
     */
    public function destroy(SignedDocument $document)
    {
        if ($document->original_pdf) {
            Storage::disk('public')->delete($document->original_pdf);
        }
        $document->delete();

        return redirect()->route('pdfsign.index')->with([
            'message'    => 'Document deleted.',
            'alert-type' => 'success',
        ]);
    }
}
