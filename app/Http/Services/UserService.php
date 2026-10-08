<?php

namespace App\Http\Services;

use App\Models\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserService
{
    public function create(array $data)
    {
        $randomPassword = Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'staff_number' => $data['staff_number'],
            'created_by' => Auth::user()->id,
            'password' => Hash::make($randomPassword),
            'password_reset' => true,
            'email_verified_at' => Carbon::now(),
            'division_id' => $data['division_id'] ?? null,
        ]);

        $role = Role::where('name', $data['role'])->first();
        if ($role) {
            $user->assignRole($role);
        }

        $roleName = $data['role'];
        $institutionName = setting('institution_name', 'eBoard System');
        $prefix = setting('email_subject_prefix', setting('institution_acronym', 'ORG'));

        $subject = "{$prefix} — Welcome to {$institutionName}";
        $body = view('emails.welcome', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $randomPassword,
            'roleName' => $roleName,
        ])->render();

        EmailService::sendEmail($user->email, $subject, $body);

        return $user;
    }

    // Method to update a user's details
    public function update(User $user, array $data)
    {
        // Custom validation and other logic here if needed
        $user->update($data);

        return $user;
    }

    // Method to delete a user
    public function delete(User $user)
    {
        $user->delete();
    }

    // Method to activate/deactivate a user
    public function activate(User $user)
    {
        $user->update(['status' => 'active']); // Assuming there's a status field in the user model
    }

    public function validateUserData($data)
    {
        // Add custom validation rules here if needed (for example, email domain check or existing user check)
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages(['email' => 'This email address is already taken.']);
        }
    }

    // Method to assign role to a user
    public function assignRole(User $user, $role)
    {
        $user->assignRole($role);
    }
}
