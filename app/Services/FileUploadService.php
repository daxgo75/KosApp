<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2MB
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    public static function validateFile(UploadedFile $file, string $type = 'document'): array
    {
        $errors = [];

        // Check file extension
        if (!in_array($file->getClientOriginalExtension(), self::ALLOWED_EXTENSIONS)) {
            $errors[] = 'Invalid file type. Allowed: ' . implode(', ', self::ALLOWED_EXTENSIONS);
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            $errors[] = 'Invalid file MIME type';
        }

        // Check file size based on type
        $maxSize = $type === 'image' ? self::MAX_IMAGE_SIZE : self::MAX_FILE_SIZE;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum of ' . ($maxSize / 1024 / 1024) . 'MB';
        }

        // Check file is valid
        if (!$file->isValid()) {
            $errors[] = 'File upload error: ' . $file->getErrorMessage();
        }

        // Check magic bytes for images
        if ($type === 'image' && !self::validateImageMagicBytes($file)) {
            $errors[] = 'Invalid image file';
        }

        return $errors;
    }

    public static function storeFile(UploadedFile $file, string $path = 'uploads'): ?string
    {
        try {
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            return $file->storeAs($path, $filename, 'public');
        } catch (\Exception $e) {
            LogService::error('File upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public static function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            return Storage::disk('public')->delete($path);
        } catch (\Exception $e) {
            LogService::error('File deletion failed', ['error' => $e->getMessage(), 'file' => $path]);
            return false;
        }
    }

    private static function validateImageMagicBytes(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
            return true; // Skip magic bytes check for non-images
        }

        try {
            $resource = fopen($file->getRealPath(), 'rb');
            $bytes = fread($resource, 4);
            fclose($resource);

            $hex = bin2hex($bytes);

            return match ($mimeType) {
                'image/jpeg' => str_starts_with($hex, 'ffd8ff'),
                'image/png' => str_starts_with($hex, '89504e47'),
                default => true,
            };
        } catch (\Exception $e) {
            return false;
        }
    }
}
