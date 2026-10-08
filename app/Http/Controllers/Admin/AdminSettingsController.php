<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        $heroSlides = setting('landing_hero_slides', []);
        $features = setting('landing_features', []);
        $boardMembers = setting('landing_board_members', []);

        if (is_string($heroSlides)) $heroSlides = json_decode($heroSlides, true) ?: [];
        if (is_string($features)) $features = json_decode($features, true) ?: [];
        if (is_string($boardMembers)) $boardMembers = json_decode($boardMembers, true) ?: [];

        return view('admin.settings.index', compact('settings', 'heroSlides', 'features', 'boardMembers'));
    }

    public function update(Request $request)
    {
        $group = $request->input('group', 'institution');
        $rules = $this->getValidationRules($group);
        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $existing = SystemSetting::where('key', $key)->first();
                $type = $existing->type ?? 'string';
                $settingGroup = $existing->group ?? $group;

                if ($key === 'mail_password' && $value) {
                    $value = encrypt($value);
                }

                SystemSetting::setValue($key, $value, $settingGroup, $type);
            }
        }

        $this->flushCache();

        return redirect()->route('admin.settings.index', ['tab' => $group])
            ->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
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

        foreach (['logo_main', 'logo_sidebar', 'logo_letterhead', 'favicon', 'ceo_signature_image', 'login_background'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/branding'), $filename);
                SystemSetting::setValue($field, 'uploads/branding/' . $filename, 'branding', 'image');
            }
        }

        foreach (['primary_color', 'accent_color', 'secondary_color'] as $colorField) {
            if ($request->filled($colorField)) {
                SystemSetting::setValue($colorField, $request->input($colorField), 'branding', 'string');
            }
        }

        $this->flushCache();

        return redirect()->route('admin.settings.index', ['tab' => 'branding'])
            ->with('success', 'Branding updated successfully.');
    }

    public function updateLandingPage(Request $request)
    {
        $request->validate([
            'landing_cta_title' => 'nullable|string|max:255',
            'landing_cta_subtitle' => 'nullable|string|max:500',
            'landing_cta_button_text' => 'nullable|string|max:100',
            'slides' => 'nullable|array',
            'slides.*.title' => 'required|string|max:255',
            'slides.*.subtitle' => 'required|string|max:500',
            'slides.*.button_text' => 'nullable|string|max:100',
            'slides.*.button_url' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'features.*.icon' => 'required|string|max:100',
            'features.*.title' => 'required|string|max:100',
            'features.*.description' => 'required|string|max:500',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:100',
            'members.*.designation' => 'nullable|string|max:100',
            'members.*.role' => 'nullable|string|max:100',
            'members.*.photo_url' => 'nullable|string|max:500',
        ]);

        $slides = collect($request->input('slides', []))->filter(fn($s) => !empty($s['title']))->values()->toArray();
        SystemSetting::setValue('landing_hero_slides', json_encode($slides), 'landing', 'json');

        $features = collect($request->input('features', []))->filter(fn($f) => !empty($f['title']))->values()->toArray();
        SystemSetting::setValue('landing_features', json_encode($features), 'landing', 'json');

        $members = collect($request->input('members', []))->filter(fn($m) => !empty($m['name']))->values()->toArray();
        SystemSetting::setValue('landing_board_members', json_encode($members), 'landing', 'json');

        foreach (['landing_cta_title', 'landing_cta_subtitle', 'landing_cta_button_text'] as $field) {
            if ($request->has($field)) {
                SystemSetting::setValue($field, $request->input($field), 'landing', 'string');
            }
        }

        SystemSetting::setValue('landing_show_board_members', $request->has('landing_show_board_members') ? '1' : '0', 'landing', 'boolean');
        SystemSetting::setValue('landing_show_features', $request->has('landing_show_features') ? '1' : '0', 'landing', 'boolean');

        $this->flushCache();

        return redirect()->route('admin.settings.index', ['tab' => 'landing'])
            ->with('success', 'Landing page settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from ' . setting('institution_name', 'eBoard System') . '. If you received this, your email configuration is working correctly.',
                function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject(setting('institution_acronym', 'ORG') . ' eBoard — Test Email');
                }
            );

            return back()->with('success', 'Test email sent to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Email failed: ' . $e->getMessage());
        }
    }

    protected function flushCache()
    {
        Cache::forget('system_settings');
        Cache::forget('system_settings_shared');
        Cache::forget('setup_completed');
    }

    protected function getValidationRules(string $group): array
    {
        return match ($group) {
            'institution' => [
                'institution_name' => 'required|string|max:255',
                'institution_acronym' => 'required|string|max:20',
                'institution_tagline' => 'nullable|string|max:255',
                'physical_address' => 'nullable|string|max:500',
                'postal_address' => 'nullable|string|max:255',
                'phone_primary' => 'nullable|string|max:20',
                'phone_secondary' => 'nullable|string|max:20',
                'email_info' => 'nullable|email|max:255',
                'website_url' => 'nullable|string|max:255',
            ],
            'workflow' => [
                'default_venue' => 'nullable|string|max:255',
                'staff_number_prefix' => 'nullable|string|max:20',
                'reference_number_prefix' => 'required|string|max:20',
                'email_subject_prefix' => 'required|string|max:50',
                'signatory_title_ceo' => 'required|string|max:100',
                'signatory_title_chairperson' => 'required|string|max:100',
                'signatory_title_audit_head' => 'nullable|string|max:100',
                'signatory_title_cs' => 'nullable|string|max:100',
            ],
            'roles' => [
                'role_label_ceo' => 'required|string|max:100',
                'role_label_corporation_secretary' => 'required|string|max:100',
                'role_label_board_member' => 'required|string|max:100',
                'role_label_dd_internal_audit' => 'nullable|string|max:100',
                'role_label_director' => 'required|string|max:100',
                'role_label_division_head' => 'required|string|max:100',
                'role_label_chairperson' => 'nullable|string|max:100',
            ],
            'email' => [
                'mail_host' => 'required|string|max:255',
                'mail_port' => 'required|integer',
                'mail_username' => 'required|string|max:255',
                'mail_password' => 'nullable|string|max:255',
                'mail_encryption' => 'required|in:tls,ssl,null',
                'mail_from_address' => 'required|email|max:255',
                'mail_from_name' => 'required|string|max:255',
            ],
            default => [],
        };
    }
}
