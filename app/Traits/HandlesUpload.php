<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesUpload
{
    /**
     * Upload a single file (image or any file).
     */
    public function uploadFile($file, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, $disk);
    }

    /**
     * Upload multiple files.
     */
    public function uploadFiles(array $files, string $directory = 'uploads', string $disk = 'public'): array
    {
        return array_map(fn($file) => $this->uploadFile($file, $directory, $disk), $files);
    }

    /**
     * Delete a file.
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        return $path && Storage::disk($disk)->exists($path)
            ? Storage::disk($disk)->delete($path)
            : false;
    }

    /**
     * Get file URL.
     */
    public function getFileUrl(?string $path, string $disk = 'public'): ?string
    {
        return $path ? Storage::disk($disk)->url($path) : null;
    }

    /**
     * --- Aliases for backward compatibility (no duplication) ---
     */
    public function uploadImage($image, string $directory = 'uploads/images', string $disk = 'public'): ?string
    {
        return $this->uploadFile($image, $directory, $disk);
    }

    public function uploadImages(array $images, string $directory = 'uploads/images', string $disk = 'public'): array
    {
        return $this->uploadFiles($images, $directory, $disk);
    }

    public function deleteImage(?string $path, string $disk = 'public'): bool
    {
        return $this->deleteFile($path, $disk);
    }

    public function getImageUrl(?string $path, string $disk = 'public'): ?string
    {
        return $this->getFileUrl($path, $disk);
    }
}
