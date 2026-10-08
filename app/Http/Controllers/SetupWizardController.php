<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class SetupWizardController extends Controller
{
    protected $steps = [
        1 => 'Institution Identity',
        2 => 'Branding & Appearance',
        3 => 'Workflow Configuration',
        4 => 'Role Display Labels',
        5 => 'Email Configuration',
        6 => 'Admin Account & Summary',
    ];

    public function index()
    {
        if ($this->isSetupComplete()) {
            return redirect('/dashboard');
        }

        return view('setup.index', ['steps' => $this->steps]);
    }

    public function show($step)
    {
        if ($this->isSetupComplete()) {
            return redirect('/dashboard');
        }

        $step = (int) $step;
        if ($step < 1 || $step > 6) {
            return redirect()->route('setup.show', 1);
        }

        $data = [
            'steps' => $this->steps,
            'currentStep' => $step,
            'savedData' => session('setup_data', []),
        ];

        return view("setup.step{$step}", $data);
    }

    public function process(Request $request, $step)
    {
        $step = (int) $step;
        $setupData = session('setup_data', []);

        switch ($step) {
            case 1:
                $validated = $request->validate([
                    'institution_name' => 'required|string|max:255',
                    'institution_acronym' => 'required|string|max:20',
                    'institution_tagline' => 'nullable|string|max:255',
                    'physical_address' => 'nullable|string|max:500',
                    'postal_address' => 'nullable|string|max:255',
                    'phone_primary' => 'nullable|string|max:20',
                    'phone_secondary' => 'nullable|string|max:20',
                    'email_info' => 'nullable|email|max:255',
                    'website_url' => 'nullable|url|max:255',
                ]);
                $setupData['institution'] = $validated;
                break;

            case 2:
                $validated = $request->validate([
                    'logo_main' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'logo_sidebar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'logo_letterhead' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                    'favicon' => 'nullable|mimes:ico,png,jpg,svg|max:512',
                    'ceo_signature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'login_background' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                    'primary_color' => 'required|string|max:7',
                    'accent_color' => 'required|string|max:7',
                    'secondary_color' => 'nullable|string|max:7',
                ]);

                $uploads = [];
                foreach (['logo_main', 'logo_sidebar', 'logo_letterhead', 'favicon', 'ceo_signature_image', 'login_background'] as $field) {
                    if ($request->hasFile($field)) {
                        $uploads[$field] = $this->handleImageUpload($request, $field);
                    }
                }

                $setupData['branding'] = array_merge(
                    ['primary_color' => $validated['primary_color'], 'accent_color' => $validated['accent_color']],
                    isset($validated['secondary_color']) ? ['secondary_color' => $validated['secondary_color']] : [],
                    $uploads
                );
                break;

            case 3:
                $validated = $request->validate([
                    'default_venue' => 'nullable|string|max:255',
                    'staff_number_prefix' => 'nullable|string|max:20',
                    'reference_number_prefix' => 'required|string|max:20',
                    'email_subject_prefix' => 'required|string|max:50',
                    'signatory_title_ceo' => 'required|string|max:100',
                    'signatory_title_chairperson' => 'required|string|max:100',
                    'signatory_title_audit_head' => 'nullable|string|max:100',
                    'signatory_title_cs' => 'nullable|string|max:100',
                ]);
                $setupData['workflow'] = $validated;
                break;

            case 4:
                $validated = $request->validate([
                    'role_label_ceo' => 'required|string|max:100',
                    'role_label_corporation_secretary' => 'required|string|max:100',
                    'role_label_board_member' => 'required|string|max:100',
                    'role_label_dd_internal_audit' => 'nullable|string|max:100',
                    'role_label_director' => 'required|string|max:100',
                    'role_label_division_head' => 'required|string|max:100',
                    'role_label_chairperson' => 'nullable|string|max:100',
                ]);
                $setupData['roles'] = $validated;
                break;

            case 5:
                $validated = $request->validate([
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|integer',
                    'mail_username' => 'required|string|max:255',
                    'mail_password' => 'required|string|max:255',
                    'mail_encryption' => 'required|in:tls,ssl,null',
                    'mail_from_address' => 'required|email|max:255',
                    'mail_from_name' => 'required|string|max:255',
                ]);
                $setupData['email'] = $validated;
                break;

            case 6:
                $validated = $request->validate([
                    'admin_name' => 'required|string|max:255',
                    'admin_email' => 'required|email|max:255',
                    'admin_password' => 'required|string|min:8|confirmed',
                ]);
                $setupData['admin'] = $validated;

                session(['setup_data' => $setupData]);
                return $this->completeSetup($setupData);
        }

        session(['setup_data' => $setupData]);

        $nextStep = $step + 1;
        return redirect()->route('setup.show', $nextStep);
    }

    protected function completeSetup(array $data)
    {
        $settings = [];

        if (isset($data['institution'])) {
            foreach ($data['institution'] as $key => $value) {
                $settings[] = [
                    'key' => $key,
                    'value' => $value,
                    'group' => 'institution',
                    'type' => 'string',
                    'description' => $this->getDescription($key),
                ];
            }
        }

        if (isset($data['branding'])) {
            foreach ($data['branding'] as $key => $value) {
                $type = in_array($key, ['logo_main', 'logo_sidebar', 'logo_letterhead', 'favicon', 'ceo_signature_image', 'login_background']) ? 'image' : 'string';
                $settings[] = [
                    'key' => $key,
                    'value' => $value,
                    'group' => 'branding',
                    'type' => $type,
                    'description' => $this->getDescription($key),
                ];
            }
        }

        if (isset($data['workflow'])) {
            foreach ($data['workflow'] as $key => $value) {
                $settings[] = [
                    'key' => $key,
                    'value' => $value,
                    'group' => 'workflow',
                    'type' => 'string',
                    'description' => $this->getDescription($key),
                ];
            }
        }

        if (isset($data['roles'])) {
            foreach ($data['roles'] as $key => $value) {
                $settings[] = [
                    'key' => $key,
                    'value' => $value,
                    'group' => 'roles',
                    'type' => 'string',
                    'description' => $this->getDescription($key),
                ];
            }
        }

        if (isset($data['email'])) {
            foreach ($data['email'] as $key => $value) {
                $settings[] = [
                    'key' => $key,
                    'value' => $key === 'mail_password' ? encrypt($value) : $value,
                    'group' => 'email',
                    'type' => 'string',
                    'description' => $this->getDescription($key),
                ];
            }
        }

        // Landing page defaults
        $acronym = $data['institution']['institution_acronym'] ?? 'ORG';
        $name = $data['institution']['institution_name'] ?? 'Organization';

        $settings[] = [
            'key' => 'landing_hero_slides',
            'value' => json_encode([
                ['title' => 'Digital Governance Made Seamless', 'subtitle' => "Welcome to the {$acronym} eBoard System — your secure platform for board management.", 'button_text' => 'Log in to eBoard', 'button_url' => '/login'],
                ['title' => 'Streamline Board Operations', 'subtitle' => 'Manage meetings, papers, and resolutions all in one place.', 'button_text' => 'Get Started', 'button_url' => '/login'],
            ]),
            'group' => 'landing',
            'type' => 'json',
            'description' => 'Hero section slides',
        ];

        $settings[] = [
            'key' => 'landing_features',
            'value' => json_encode([
                ['icon' => 'mdi mdi-calendar-check', 'title' => 'Meeting Management', 'description' => 'Schedule, track, and manage board meetings with an intuitive digital almanac.'],
                ['icon' => 'mdi mdi-file-document-outline', 'title' => 'Board Documents', 'description' => 'Securely upload, review, and share board papers and supporting documents.'],
                ['icon' => 'mdi mdi-check-decagram', 'title' => 'Approvals & Tracking', 'description' => 'Multi-level approval workflows for notices, papers, and resolutions.'],
                ['icon' => 'mdi mdi-shield-lock-outline', 'title' => 'Secure Governance', 'description' => 'Role-based access control, OTP verification, and comprehensive audit trails.'],
                ['icon' => 'mdi mdi-account-group-outline', 'title' => 'Collaborate & Discuss', 'description' => 'Engage with board members through structured communication channels.'],
                ['icon' => 'mdi mdi-chart-bar', 'title' => 'Smart Reporting', 'description' => 'Generate insightful reports and analytics for informed decision-making.'],
            ]),
            'group' => 'landing',
            'type' => 'json',
            'description' => 'Feature cards on landing page',
        ];

        $settings[] = [
            'key' => 'landing_board_members',
            'value' => json_encode([]),
            'group' => 'landing',
            'type' => 'json',
            'description' => 'Board members displayed on landing page',
        ];

        $settings[] = [
            'key' => 'landing_cta_title',
            'value' => "Join {$name}'s digital transformation journey",
            'group' => 'landing',
            'type' => 'string',
            'description' => 'Landing page CTA title',
        ];

        $settings[] = [
            'key' => 'landing_cta_subtitle',
            'value' => 'Experience the future of board governance with our comprehensive eBoard platform.',
            'group' => 'landing',
            'type' => 'string',
            'description' => 'Landing page CTA subtitle',
        ];

        $settings[] = [
            'key' => 'landing_show_board_members',
            'value' => '0',
            'group' => 'landing',
            'type' => 'boolean',
            'description' => 'Show board members section on landing page',
        ];

        $settings[] = [
            'key' => 'landing_show_features',
            'value' => '1',
            'group' => 'landing',
            'type' => 'boolean',
            'description' => 'Show features section on landing page',
        ];

        SystemSetting::setMany($settings);

        // Create system roles
        $systemRoles = [
            'Superadmin',
            'admin',
            'CEO',
            'Corporation Secretary',
            'Director',
            'Division Head',
            'DD Internal Audit',
            'DD ICT',
            'Board Member',
            'Officer',
        ];
        foreach ($systemRoles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create admin user
        if (isset($data['admin'])) {
            $user = User::firstOrCreate(
                ['email' => $data['admin']['admin_email']],
                [
                    'name' => $data['admin']['admin_name'],
                    'password' => Hash::make($data['admin']['admin_password']),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }

            if (!$user->hasRole('Superadmin')) {
                $user->assignRole('Superadmin');
            }
        }

        // Mark setup as complete
        SystemSetting::setValue('setup_completed', '1', 'system', 'boolean', 'Whether initial setup has been completed');
        Cache::forget('setup_completed');
        Cache::forget('system_settings_shared');

        session()->forget('setup_data');

        return redirect()->route('setup.complete');
    }

    public function complete()
    {
        return view('setup.complete', ['steps' => $this->steps]);
    }

    protected function handleImageUpload(Request $request, string $field): string
    {
        $file = $request->file($field);
        $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/branding'), $filename);
        return 'uploads/branding/' . $filename;
    }

    protected function isSetupComplete(): bool
    {
        try {
            if (!Schema::hasTable('system_settings')) {
                return false;
            }
            return SystemSetting::where('key', 'setup_completed')->where('value', '1')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getDescription(string $key): string
    {
        $descriptions = [
            'institution_name' => 'Full institution name',
            'institution_acronym' => 'Short institution acronym',
            'institution_tagline' => 'Institution tagline or motto',
            'physical_address' => 'Physical address',
            'postal_address' => 'Postal address',
            'phone_primary' => 'Primary phone number',
            'phone_secondary' => 'Secondary phone number',
            'email_info' => 'General info email address',
            'website_url' => 'Institution website URL',
            'logo_main' => 'Main logo image',
            'logo_sidebar' => 'Sidebar logo image',
            'logo_letterhead' => 'Letterhead image for PDFs',
            'favicon' => 'Browser favicon',
            'ceo_signature_image' => 'CEO digital signature image',
            'login_background' => 'Login page background image',
            'primary_color' => 'Primary brand color',
            'accent_color' => 'Accent brand color',
            'secondary_color' => 'Secondary brand color',
            'default_venue' => 'Default meeting venue',
            'staff_number_prefix' => 'Staff number prefix',
            'reference_number_prefix' => 'Meeting reference number prefix',
            'email_subject_prefix' => 'Email subject line prefix',
            'signatory_title_ceo' => 'CEO signatory title for documents',
            'signatory_title_chairperson' => 'Board chairperson title',
            'signatory_title_audit_head' => 'Audit head signatory title',
            'signatory_title_cs' => 'Corporation Secretary title for documents',
            'role_label_ceo' => 'Display label for CEO role',
            'role_label_corporation_secretary' => 'Display label for Corporation Secretary role',
            'role_label_board_member' => 'Display label for Board Member role',
            'role_label_dd_internal_audit' => 'Display label for DD Internal Audit role',
            'role_label_director' => 'Display label for Director role',
            'role_label_division_head' => 'Display label for Division Head role',
            'role_label_chairperson' => 'Display label for Board Chairperson',
            'mail_host' => 'SMTP mail host',
            'mail_port' => 'SMTP mail port',
            'mail_username' => 'SMTP username',
            'mail_password' => 'SMTP password (encrypted)',
            'mail_encryption' => 'Mail encryption type',
            'mail_from_address' => 'Default from email address',
            'mail_from_name' => 'Default from name',
        ];

        return $descriptions[$key] ?? ucwords(str_replace('_', ' ', $key));
    }
}
