<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Services\AuditLogService;
use App\Http\Services\AuthService;
use App\Http\Services\UserService;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\Twwda\TwwdaUserDirectorateMapping;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    //
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Display the list of users
    public function index()
    {
        $users = User::with(['roles', 'division.directorate', 'activeDirectorateMapping.directorate'])->get();
        return view('backend.users.index', compact('users'));
    }


    public function unlock($id, Request $request)
    {
        $user = User::findOrFail($id);

        if (!$user->locked) {
            return back()->with('info', 'User account is already unlocked.');
        }

        $oldValues = $user->only(['locked', 'failed_logins']);

        $user->update([
            'locked' => false,
            'failed_logins' => 0,
        ]);

       AuditLogService::log(
            $request,
            'UNLOCK_ACCOUNT',
            'users',
            $user->id,
            $oldValues,
            $user->only(['locked', 'failed_logins'])
        );

        return back()->with('success', "User {$user->name}'s account has been unlocked.");
    }

    // Display the user creation form
    public function create()
    {
        $roles = Role::latest()->select('name')->get();
        $divisions = Division::with('directorate')->orderBy('name')->get();
        $directorates = Directorate::orderBy('name')->get();

        return view('backend.users.create', compact('roles', 'divisions', 'directorates'));
    }

    // Handle user creation
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            // If role is Director, create directorate mapping
            if ($request->role === 'Director' && $request->filled('directorate_id')) {
                TwwdaUserDirectorateMapping::updateOrCreate(
                    ['user_id' => $user->id, 'directorate_id' => $request->directorate_id],
                    ['role_type' => 'director', 'is_active' => true]
                );
            }

            $notification = array(
                'message' => 'User created successfully and email sent.',
                'alert-type' => 'success'
            );

            return redirect()->route('users.index')->with($notification);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Show user details
    public function show(User $user)
    {

        return view('users.show', compact('user'));
    }

    // Show the edit form
    public function edit(User $user)
    {
        $user->load(['roles', 'division', 'activeDirectorateMapping.directorate']);
        $roles = Role::latest()->select('name')->get();
        $divisions = Division::with('directorate')->orderBy('name')->get();
        $directorates = Directorate::orderBy('name')->get();

        return view('backend.users.edit', compact('user', 'roles', 'divisions', 'directorates'));
    }

    // Handle user update
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Update user details
            $updatedUser = $this->userService->update($user, $validated);

            // Update user role (Remove old roles and assign the new one)
            if ($request->filled('role')) {
                $user->syncRoles([$validated['role']]);
            }

            // Handle directorate mapping for Director role
            if ($request->role === 'Director' && $request->filled('directorate_id')) {
                // Deactivate any existing mappings
                $user->directorateMappings()->update(['is_active' => false]);
                // Create or reactivate the mapping
                TwwdaUserDirectorateMapping::updateOrCreate(
                    ['user_id' => $user->id, 'directorate_id' => $request->directorate_id],
                    ['role_type' => 'director', 'is_active' => true]
                );
            } elseif ($request->role !== 'Director') {
                // If no longer a Director, deactivate director mappings
                $user->directorateMappings()->where('role_type', 'director')->update(['is_active' => false]);
            }

            $notification = array(
                'message' => 'User updated successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('users.index')->with($notification);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Handle user deletion
    public function destroy(User $user)
    {
        try {
            $this->userService->delete($user);
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Activate user account
    public function activate(User $user)
    {
        try {
            $this->userService->activate($user);
            return redirect()->route('users.index')->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Assign role to a user
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        try {
            $this->userService->assignRole($user, $request->role);
            return redirect()->route('users.index')->with('success', 'Role assigned successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }



    public function changeStatus0(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validStatuses = ['inactive', 'suspended', 'disabled', 'active'];

        if (!in_array($request->action, $validStatuses)) {
            return response()->json(['message' => 'Invalid action'], 400);
        }

        $user->status = $request->action;
        $user->save();

        return response()->json(['message' => 'User status updated successfully']);
    }
    public function changeStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:inactive,suspended,disabled,active',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->status = $request->action;
        $user->save();

        $notification = array(
            'message' => 'User status updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

}
