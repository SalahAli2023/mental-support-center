<?php

namespace App\Support;

class MediaHelper
{
    /**
     * Normalize filesystem paths so they work across operating systems.
     */
    public static function normalizePath(?string $path): ?string
    {
        if (!$path) {
            return $path;
        }

        return str_replace('\\', '/', $path);
    }

    /**
     * Convert a stored path into a publicly accessible URL.
     */
    public static function toPublicUrl(?string $path): ?string
    {
        \Log::info('MediaHelper::toPublicUrl called', [
            'original_path' => $path,
            'path_type' => gettype($path)
        ]);
        
        $normalized = self::normalizePath($path);

        if (!$normalized) {
            \Log::warning('MediaHelper::toPublicUrl - normalized path is empty', [
                'original_path' => $path
            ]);
            return null;
        }

        // إذا كان URL كامل بالفعل، استخدمه مباشرة
        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            \Log::info('MediaHelper::toPublicUrl - already full URL', [
                'url' => $normalized
            ]);
            return $normalized;
        }

        // تنظيف المسار
        if (str_starts_with($normalized, '/')) {
            $normalized = ltrim($normalized, '/');
        }

        // إزالة storage/ من البداية إذا كان موجوداً
        $normalized = preg_replace('#^storage/#', '', $normalized);

        // استخدام مسار /media (يمر عبر PublicStorageController) بدلاً من /storage
        // لتجنب أي قيود أمان على /storage على بعض الخوادم
        $url = url('/media/' . $normalized);
        
        \Log::info('MediaHelper::toPublicUrl - generated URL', [
            'normalized_path' => $normalized,
            'generated_url' => $url
        ]);

        return $url;
    }
}

