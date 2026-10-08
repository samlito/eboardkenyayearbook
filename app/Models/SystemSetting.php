<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function setValueAttribute($value)
    {
        if ($this->type === 'json' && (is_array($value) || is_object($value))) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public static function getValue($key, $default = null)
    {
        $settings = Cache::remember('system_settings', 3600, function () {
            try {
                return static::all()->pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        return $settings[$key] ?? $default;
    }

    public static function setValue($key, $value, $group = 'general', $type = 'string', $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $type === 'json' && (is_array($value) || is_object($value)) ? json_encode($value) : $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget('system_settings');

        return $setting;
    }

    public static function setMany(array $settings)
    {
        foreach ($settings as $setting) {
            static::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => ($setting['type'] ?? 'string') === 'json' && is_array($setting['value'] ?? null)
                        ? json_encode($setting['value'])
                        : ($setting['value'] ?? null),
                    'group' => $setting['group'] ?? 'general',
                    'type' => $setting['type'] ?? 'string',
                    'description' => $setting['description'] ?? null,
                    'is_public' => $setting['is_public'] ?? false,
                ]
            );
        }

        Cache::forget('system_settings');
    }

    public static function getGroup($group)
    {
        $settings = Cache::remember('system_settings', 3600, function () {
            try {
                return static::all()->groupBy('group')->map(function ($items) {
                    return $items->pluck('value', 'key');
                })->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        return $settings[$group] ?? [];
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('system_settings');
        });

        static::deleted(function () {
            Cache::forget('system_settings');
        });
    }
}
