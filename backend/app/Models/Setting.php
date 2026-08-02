<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'value_ar',
        'value_en',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // 'value' => 'json',
        // 'value_ar' => 'json',
        // 'value_en' => 'json',
    ];

    // 🔹 Helper methods
    public static function getValue(string $group, string $key, $default = null)
    {
        $setting = self::where('group', $group)
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        // Return value based on type
        if ($setting->type === 'json' || $setting->type === 'array') {
            return json_decode($setting->value, true) ?? $default;
        }

        return $setting->value ?? $default;
    }

    public static function getValueLocalized(string $group, string $key, string $locale = 'ar', $default = null)
    {
        $setting = self::where('group', $group)
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        $field = $locale === 'ar' ? 'value_ar' : 'value_en';

        if ($setting->type === 'json' || $setting->type === 'array') {
            return json_decode($setting->$field, true) ?? $default;
        }

        return $setting->$field ?? $default;
    }

    public static function getGroup(string $group): array
    {
        $settings = self::where('group', $group)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;

            // Add localized versions
            $result[$setting->key . '_ar'] = $setting->value_ar;
            $result[$setting->key . '_en'] = $setting->value_en;
        }

        return $result;
    }

    public static function setValue(string $group, string $key, $value, $type = 'text')
    {
        return self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    public static function setLocalizedValue(string $group, string $key, ?string $valueAr, ?string $valueEn, $type = 'text')
    {
        return self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value_ar' => $valueAr,
                'value_en' => $valueEn,
                'type' => $type
            ]
        );
    }
}
