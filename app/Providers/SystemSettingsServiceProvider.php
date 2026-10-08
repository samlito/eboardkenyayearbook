<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SystemSettingsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->shareSettingsWithViews();
            $this->overrideMailConfig();
        }
    }

    protected function shareSettingsWithViews()
    {
        try {
            if (!Schema::hasTable('system_settings')) {
                $this->shareDefaults();
                return;
            }

            $settings = Cache::remember('system_settings_shared', 3600, function () {
                return [
                    'institutionName' => SystemSetting::getValue('institution_name', 'eBoard System'),
                    'institutionAcronym' => SystemSetting::getValue('institution_acronym', 'ORG'),
                    'institutionTagline' => SystemSetting::getValue('institution_tagline', ''),
                    'primaryColor' => SystemSetting::getValue('primary_color', '#1a1a1a'),
                    'accentColor' => SystemSetting::getValue('accent_color', '#23803a'),
                    'secondaryColor' => SystemSetting::getValue('secondary_color', '#2b2b2b'),
                    'logoMain' => SystemSetting::getValue('logo_main', 'backend/assets/images/logo-dark.png'),
                    'logoSidebar' => SystemSetting::getValue('logo_sidebar', 'backend/assets/images/logo-sm.png'),
                    'logoLetterhead' => SystemSetting::getValue('logo_letterhead', ''),
                    'favicon' => SystemSetting::getValue('favicon', 'backend/assets/images/favicon.ico'),
                    'websiteUrl' => SystemSetting::getValue('website_url', '#'),
                    'setupCompleted' => SystemSetting::getValue('setup_completed', false),
                ];
            });

            View::share($settings);
        } catch (\Exception $e) {
            $this->shareDefaults();
        }
    }

    protected function overrideMailConfig()
    {
        try {
            if (!Schema::hasTable('system_settings')) {
                return;
            }

            $mailHost = SystemSetting::getValue('mail_host');
            if (!$mailHost) {
                return;
            }

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $mailHost);
            Config::set('mail.mailers.smtp.port', SystemSetting::getValue('mail_port', '587'));
            Config::set('mail.mailers.smtp.username', SystemSetting::getValue('mail_username', ''));
            Config::set('mail.mailers.smtp.encryption', SystemSetting::getValue('mail_encryption', 'tls'));
            Config::set('mail.from.address', SystemSetting::getValue('mail_from_address', 'noreply@example.com'));
            Config::set('mail.from.name', SystemSetting::getValue('mail_from_name', 'eBoard System'));

            $mailPassword = SystemSetting::getValue('mail_password');
            if ($mailPassword) {
                try {
                    Config::set('mail.mailers.smtp.password', decrypt($mailPassword));
                } catch (\Exception $e) {
                    Config::set('mail.mailers.smtp.password', $mailPassword);
                }
            }
        } catch (\Exception $e) {
            // Fall back to .env config
        }
    }

    protected function shareDefaults()
    {
        View::share([
            'institutionName' => 'eBoard System',
            'institutionAcronym' => 'ORG',
            'institutionTagline' => '',
            'primaryColor' => '#24348A',
            'accentColor' => '#7AC143',
            'secondaryColor' => '#005bac',
            'logoMain' => 'backend/assets/images/logo-dark.png',
            'logoSidebar' => 'backend/assets/images/logo-sm.png',
            'logoLetterhead' => '',
            'favicon' => 'backend/assets/images/favicon.ico',
            'websiteUrl' => '#',
            'setupCompleted' => false,
        ]);
    }
}
