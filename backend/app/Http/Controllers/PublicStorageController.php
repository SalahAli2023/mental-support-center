<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PublicStorageController extends Controller
{
    /**
     * Serve files stored on the public disk even when the storage symlink is absent.
     */
    public function show(string $path): Response
    {
        $disk = Storage::disk('public');
        
        // تنظيف المسار من أي محاولات للوصول لملفات خارج storage
        $originalPath = $path;
        $path = ltrim($path, '/');
        $path = str_replace('..', '', $path);
        
        // إزالة storage/ من البداية إذا كان موجوداً (لأن disk('public') يبحث في storage/app/public)
        $path = preg_replace('#^storage/#', '', $path);
        
        Log::info('PublicStorageController::show', [
            'original_path' => $originalPath,
            'normalized_path' => $path,
            'request_path' => request()->path(),
            'request_url' => request()->url(),
            'full_disk_path' => $disk->path($path)
        ]);
        
        // إذا كان المسار فارغاً بعد التنظيف، رفض الطلب
        if (empty($path)) {
            Log::warning('Empty path after normalization', [
                'original_path' => $originalPath,
                'request_path' => request()->path(),
                'request_url' => request()->url()
            ]);
            abort(404, 'Invalid path');
        }

        // التحقق من وجود الملف
        $fileExists = $disk->exists($path);
        
        if (!$fileExists) {
            // محاولة البحث في المجلدات الفرعية
            $directories = $disk->directories();
            $files = $disk->allFiles();
            
            // محاولة البحث في therapists/avatars
            $therapistsFiles = [];
            if ($disk->exists('therapists')) {
                $therapistsFiles = $disk->files('therapists');
                if ($disk->exists('therapists/avatars')) {
                    $therapistsFiles = array_merge($therapistsFiles, $disk->files('therapists/avatars'));
                }
            }
            
            Log::warning('Storage file not found', [
                'path' => $path,
                'request_path' => request()->path(),
                'request_url' => request()->url(),
                'full_path' => $disk->path($path),
                'directories' => $directories,
                'all_files_count' => count($files),
                'sample_files' => array_slice($files, 0, 10),
                'therapists_dir_exists' => $disk->exists('therapists'),
                'therapists_avatars_exists' => $disk->exists('therapists/avatars'),
                'therapists_files' => $therapistsFiles,
                'therapists_avatars_files' => $disk->exists('therapists/avatars') ? $disk->files('therapists/avatars') : []
            ]);
            abort(404, 'File not found: ' . $path);
        }

        try {
            $contents = $disk->get($path);
            $mimeType = $disk->mimeType($path) ?? 'application/octet-stream';
            
            Log::info('Serving storage file successfully', [
                'path' => $path,
                'mime_type' => $mimeType,
                'file_size' => strlen($contents)
            ]);

            return response($contents, 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        } catch (\Exception $e) {
            Log::error('Error serving storage file', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error serving file: ' . $e->getMessage());
        }
    }
}




