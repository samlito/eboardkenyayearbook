<?php

namespace App\Http\Controllers;

use App\Models\BoardCommittee;
use App\Models\BoardMember;
use App\Models\DocumentCategory;
use App\Models\RepositoryDocument;
use App\Models\RepositoryDocumentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRepositoryController extends Controller
{
    // ── Categories ────────────────────────────────────────────────────

    public function index()
    {
        $categories = DocumentCategory::withCount('documents')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('repository.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('repository.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_categories,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
        ]);

        DocumentCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'mdi-folder-outline',
            'display_order' => DocumentCategory::max('display_order') + 1,
            'created_by' => Auth::id(),
        ]);

        session()->flash('message', 'Category created successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.index');
    }

    public function editCategory(DocumentCategory $category)
    {
        return view('repository.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, DocumentCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: $category->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flash('message', 'Category updated successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.index');
    }

    public function destroyCategory(DocumentCategory $category)
    {
        if ($category->documents()->count() > 0) {
            session()->flash('message', 'Cannot delete category with existing documents. Remove all documents first.');
            session()->flash('alert-type', 'error');
            return back();
        }

        $category->delete();

        session()->flash('message', 'Category deleted successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.index');
    }

    // ── Documents ─────────────────────────────────────────────────────

    public function showCategory(DocumentCategory $category)
    {
        $documents = $category->documents()
            ->with(['uploader', 'accessRules.committee', 'accessRules.member'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('repository.documents.index', compact('category', 'documents'));
    }

    public function createDocument(DocumentCategory $category)
    {
        $committees = BoardCommittee::orderBy('name')->get();
        $members = BoardMember::orderBy('names')->get();
        return view('repository.documents.create', compact('category', 'committees', 'members'));
    }

    public function storeDocument(Request $request, DocumentCategory $category)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png|max:20480',
            'visibility' => 'required|in:all,committee,specific',
            'committee_ids' => 'required_if:visibility,committee|array',
            'committee_ids.*' => 'integer|exists:board_committees,id',
            'member_ids' => 'required_if:visibility,specific|array',
            'member_ids.*' => 'integer|exists:board_members,id',
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $basename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $storedPath = $file->storeAs(
            'repository/' . $category->slug,
            $basename . '-' . Str::random(6) . '.' . $ext,
            'public'
        );

        $document = RepositoryDocument::create([
            'category_id' => $category->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $storedPath,
            'file_name' => $originalName,
            'file_type' => $ext,
            'file_size' => $file->getSize(),
            'visibility' => $request->visibility,
            'uploaded_by' => Auth::id(),
        ]);

        $this->syncAccessRules($document, $request);

        session()->flash('message', 'Document uploaded successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.category.show', $category);
    }

    public function editDocument(DocumentCategory $category, RepositoryDocument $document)
    {
        $document->load(['accessRules']);
        $committees = BoardCommittee::orderBy('name')->get();
        $members = BoardMember::orderBy('names')->get();

        $selectedCommitteeIds = $document->accessRules->whereNotNull('board_committee_id')->pluck('board_committee_id')->toArray();
        $selectedMemberIds = $document->accessRules->whereNotNull('board_member_id')->pluck('board_member_id')->toArray();

        return view('repository.documents.edit', compact('category', 'document', 'committees', 'members', 'selectedCommitteeIds', 'selectedMemberIds'));
    }

    public function updateDocument(Request $request, DocumentCategory $category, RepositoryDocument $document)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png|max:20480',
            'visibility' => 'required|in:all,committee,specific',
            'committee_ids' => 'required_if:visibility,committee|array',
            'committee_ids.*' => 'integer|exists:board_committees,id',
            'member_ids' => 'required_if:visibility,specific|array',
            'member_ids.*' => 'integer|exists:board_members,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $basename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $storedPath = $file->storeAs(
                'repository/' . $category->slug,
                $basename . '-' . Str::random(6) . '.' . $ext,
                'public'
            );

            $data['file_path'] = $storedPath;
            $data['file_name'] = $originalName;
            $data['file_type'] = $ext;
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);
        $this->syncAccessRules($document, $request);

        session()->flash('message', 'Document updated successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.category.show', $category);
    }

    public function destroyDocument(DocumentCategory $category, RepositoryDocument $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        session()->flash('message', 'Document deleted successfully.');
        session()->flash('alert-type', 'success');
        return redirect()->route('repository.category.show', $category);
    }

    public function downloadDocument(RepositoryDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            session()->flash('message', 'File not found.');
            session()->flash('alert-type', 'error');
            return back();
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function syncAccessRules(RepositoryDocument $document, Request $request): void
    {
        $document->accessRules()->delete();

        if ($request->visibility === 'committee' && $request->committee_ids) {
            foreach ($request->committee_ids as $committeeId) {
                RepositoryDocumentAccess::create([
                    'document_id' => $document->id,
                    'board_committee_id' => $committeeId,
                ]);
            }
        }

        if ($request->visibility === 'specific' && $request->member_ids) {
            foreach ($request->member_ids as $memberId) {
                RepositoryDocumentAccess::create([
                    'document_id' => $document->id,
                    'board_member_id' => $memberId,
                ]);
            }
        }
    }
}
