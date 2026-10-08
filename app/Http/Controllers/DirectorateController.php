<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directorate;
use App\Models\User;
use App\Models\Twwda\TwwdaUserDirectorateMapping;
use App\Http\Services\DirectorateService;
use App\Http\Services\Twwda\TwwdaAuditService;

class DirectorateController extends Controller
{
    protected $service;

    public function __construct(DirectorateService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $directorates = Directorate::with('divisions')->orderBy('name')->get();

        // Load active director mapping for each directorate
        $directorMappings = TwwdaUserDirectorateMapping::where('is_active', true)
            ->where('role_type', 'director')
            ->with('user')
            ->get()
            ->keyBy('directorate_id');

        // All users with Director role for the assignment dropdown
        $directorUsers = User::role('Director')->orderBy('name')->get();

        return view('backend.directorates.index', compact('directorates', 'directorMappings', 'directorUsers'));
    }

    public function create()
    {
        return view('backend.directorates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:directorates,name',
        ]);

        $this->service->store($request);

        return redirect()->route('directorates.index')->with('success', 'Directorate created successfully');
    }

    public function edit(Directorate $directorate)
    {
        return view('backend.directorates.edit', compact('directorate'));
    }

    public function update(Request $request, Directorate $directorate)
    {
        $request->validate([
            'name' => 'required|unique:directorates,name,' . $directorate->id,
        ]);

        $this->service->update($request, $directorate);

        return redirect()->route('directorates.index')->with('success', 'Directorate updated successfully');
    }

    public function destroy(Request $request, Directorate $directorate)
    {
        $this->service->delete($request, $directorate);

        return redirect()->route('directorates.index')->with('success', 'Directorate deleted successfully');
    }

    /**
     * Assign a Director to a directorate.
     */
    public function assignDirector(Request $request, Directorate $directorate)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        // Deactivate any existing director mapping for this directorate
        TwwdaUserDirectorateMapping::where('directorate_id', $directorate->id)
            ->where('role_type', 'director')
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Also deactivate any existing director mapping for this user (a director can only head one directorate)
        TwwdaUserDirectorateMapping::where('user_id', $userId)
            ->where('role_type', 'director')
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create new active mapping
        TwwdaUserDirectorateMapping::create([
            'user_id' => $userId,
            'directorate_id' => $directorate->id,
            'role_type' => 'director',
            'is_active' => true,
        ]);

        TwwdaAuditService::log(
            'director_assigned',
            'Directorate Management',
            Directorate::class,
            $directorate->id,
            "Assigned {$user->name} as Director of {$directorate->name}",
            null,
            ['user_id' => $userId, 'directorate_id' => $directorate->id],
            null
        );

        return redirect()->route('directorates.index')
            ->with('success', "{$user->name} assigned as Director of {$directorate->name}");
    }

    /**
     * Remove the Director from a directorate.
     */
    public function removeDirector(Request $request, Directorate $directorate)
    {
        $mapping = TwwdaUserDirectorateMapping::where('directorate_id', $directorate->id)
            ->where('role_type', 'director')
            ->where('is_active', true)
            ->with('user')
            ->first();

        if ($mapping) {
            $directorName = $mapping->user->name;
            $mapping->update(['is_active' => false]);

            TwwdaAuditService::log(
                'director_removed',
                'Directorate Management',
                Directorate::class,
                $directorate->id,
                "Removed {$directorName} as Director of {$directorate->name}",
                null,
                ['user_id' => $mapping->user_id, 'directorate_id' => $directorate->id],
                null
            );

            return redirect()->route('directorates.index')
                ->with('success', "{$directorName} removed as Director of {$directorate->name}");
        }

        return redirect()->route('directorates.index')
            ->with('success', 'No active director found for this directorate.');
    }
}
