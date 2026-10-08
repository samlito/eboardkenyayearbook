<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Institution Identity
            ['group' => 'institution', 'key' => 'institution_name', 'value' => 'eBoard Institution', 'type' => 'string', 'description' => 'Full institution name'],
            ['group' => 'institution', 'key' => 'institution_acronym', 'value' => 'ORG', 'type' => 'string', 'description' => 'Short acronym'],
            ['group' => 'institution', 'key' => 'institution_tagline', 'value' => 'Board Management System', 'type' => 'string', 'description' => 'Tagline or motto'],
            ['group' => 'institution', 'key' => 'physical_address', 'value' => '', 'type' => 'string', 'description' => 'Physical address'],
            ['group' => 'institution', 'key' => 'postal_address', 'value' => '', 'type' => 'string', 'description' => 'Postal address'],
            ['group' => 'institution', 'key' => 'phone_primary', 'value' => '', 'type' => 'string', 'description' => 'Primary phone number'],
            ['group' => 'institution', 'key' => 'phone_secondary', 'value' => '', 'type' => 'string', 'description' => 'Secondary phone number'],
            ['group' => 'institution', 'key' => 'email_info', 'value' => '', 'type' => 'string', 'description' => 'General info email address'],
            ['group' => 'institution', 'key' => 'website_url', 'value' => '', 'type' => 'string', 'description' => 'Official website URL'],

            // Branding
            ['group' => 'branding', 'key' => 'logo_main', 'value' => '', 'type' => 'image', 'description' => 'Main logo (header, login)'],
            ['group' => 'branding', 'key' => 'logo_sidebar', 'value' => '', 'type' => 'image', 'description' => 'Sidebar logo (small)'],
            ['group' => 'branding', 'key' => 'logo_letterhead', 'value' => '', 'type' => 'image', 'description' => 'Letterhead logo (PDF documents)'],
            ['group' => 'branding', 'key' => 'favicon', 'value' => '', 'type' => 'image', 'description' => 'Browser favicon'],
            ['group' => 'branding', 'key' => 'primary_color', 'value' => '#1a1a1a', 'type' => 'string', 'description' => 'Primary brand color'],
            ['group' => 'branding', 'key' => 'secondary_color', 'value' => '#2b2b2b', 'type' => 'string', 'description' => 'Secondary brand color'],
            ['group' => 'branding', 'key' => 'accent_color', 'value' => '#23803a', 'type' => 'string', 'description' => 'Accent/highlight color'],

            // Workflow Configuration
            ['group' => 'workflow', 'key' => 'default_venue', 'value' => 'Board Room', 'type' => 'string', 'description' => 'Default meeting venue'],
            ['group' => 'workflow', 'key' => 'reference_number_prefix', 'value' => 'ORG', 'type' => 'string', 'description' => 'Prefix for reference numbers'],
            ['group' => 'workflow', 'key' => 'email_subject_prefix', 'value' => 'eBoard', 'type' => 'string', 'description' => 'Email subject line prefix'],
            ['group' => 'workflow', 'key' => 'signatory_title_ceo', 'value' => 'Chief Executive Officer', 'type' => 'string', 'description' => 'CEO signatory title on documents'],
            ['group' => 'workflow', 'key' => 'signatory_title_chairperson', 'value' => 'Chairperson', 'type' => 'string', 'description' => 'Board chairperson signatory title'],
            ['group' => 'workflow', 'key' => 'signatory_title_audit_head', 'value' => 'Head of Internal Audit', 'type' => 'string', 'description' => 'Audit head signatory title'],

            // Role Display Labels
            ['group' => 'roles', 'key' => 'role_label_ceo', 'value' => 'CEO', 'type' => 'string', 'description' => 'Display label for CEO role'],
            ['group' => 'roles', 'key' => 'role_label_corporation_secretary', 'value' => 'Corporation Secretary', 'type' => 'string', 'description' => 'Display label for CS role'],
            ['group' => 'roles', 'key' => 'role_label_board_member', 'value' => 'Board Member', 'type' => 'string', 'description' => 'Display label for board member role'],
            ['group' => 'roles', 'key' => 'role_label_dd_internal_audit', 'value' => 'DD Internal Audit', 'type' => 'string', 'description' => 'Display label for audit role'],
            ['group' => 'roles', 'key' => 'role_label_director', 'value' => 'Director', 'type' => 'string', 'description' => 'Display label for director role'],
            ['group' => 'roles', 'key' => 'role_label_division_head', 'value' => 'Division Head', 'type' => 'string', 'description' => 'Display label for division head role'],

            // Email Configuration
            ['group' => 'email', 'key' => 'mail_host', 'value' => '', 'type' => 'string', 'description' => 'SMTP host'],
            ['group' => 'email', 'key' => 'mail_port', 'value' => '587', 'type' => 'string', 'description' => 'SMTP port'],
            ['group' => 'email', 'key' => 'mail_username', 'value' => '', 'type' => 'string', 'description' => 'SMTP username'],
            ['group' => 'email', 'key' => 'mail_password', 'value' => '', 'type' => 'string', 'description' => 'SMTP password (encrypted)'],
            ['group' => 'email', 'key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'description' => 'SMTP encryption (tls/ssl)'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => '', 'type' => 'string', 'description' => 'From email address'],
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'eBoard System', 'type' => 'string', 'description' => 'From display name'],

            // Landing Page
            ['group' => 'landing', 'key' => 'landing_show_features', 'value' => '1', 'type' => 'boolean', 'description' => 'Show features section on landing page'],
            ['group' => 'landing', 'key' => 'landing_show_board_members', 'value' => '1', 'type' => 'boolean', 'description' => 'Show board members section on landing page'],
            ['group' => 'landing', 'key' => 'landing_cta_title', 'value' => 'Ready to Get Started?', 'type' => 'string', 'description' => 'CTA section title'],
            ['group' => 'landing', 'key' => 'landing_cta_subtitle', 'value' => 'Access the board portal to manage meetings, papers, and governance.', 'type' => 'string', 'description' => 'CTA section subtitle'],
            ['group' => 'landing', 'key' => 'landing_cta_button_text', 'value' => 'Access Board Portal', 'type' => 'string', 'description' => 'CTA button text'],
            ['group' => 'landing', 'key' => 'landing_hero_slides', 'value' => json_encode([
                ['title' => 'Board Management System', 'subtitle' => 'Streamline your board governance with our digital platform', 'button_text' => 'Get Started', 'button_url' => '/login'],
            ]), 'type' => 'json', 'description' => 'Hero carousel slides'],
            ['group' => 'landing', 'key' => 'landing_features', 'value' => json_encode([
                ['icon' => 'mdi mdi-file-document-outline', 'title' => 'Board Papers', 'description' => 'Create, review, and approve board papers digitally'],
                ['icon' => 'mdi mdi-calendar-check', 'title' => 'Meeting Management', 'description' => 'Schedule and manage board meetings efficiently'],
                ['icon' => 'mdi mdi-shield-check', 'title' => 'Governance', 'description' => 'Ensure compliance with governance requirements'],
            ]), 'type' => 'json', 'description' => 'Feature cards for landing page'],
            ['group' => 'landing', 'key' => 'landing_board_members', 'value' => json_encode([]), 'type' => 'json', 'description' => 'Board members displayed on landing page'],

            // System
            ['group' => 'system', 'key' => 'setup_completed', 'value' => '0', 'type' => 'boolean', 'description' => 'Whether initial setup has been completed'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
