<?php

namespace Modules\Driver\Services\FileUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Driver\Exceptions\FileUploadException;
use Modules\Driver\Exceptions\FileValidationException;

/**
 * File Upload Service Implementation
 *
 * This class handles file upload operations for the driver module,
 * supporting both public (avatar) and private (document) file storage.
 */
class FileUploadService implements IFileUploadService
{

    /**
     * {@inheritDoc}
     */
    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        $filename = $this->generateUniqueFilename($file->getClientOriginalName());
        $path = "avatars/{$userId}/{$filename}";

        // Store file in S3 with public visibility
        $storedPath = $file->storeAs("avatars/{$userId}", $filename, [
            'disk' => 's3',
            'visibility' => 'public'
        ]);

        if (!$storedPath) {
            throw new FileUploadException();
        }

        return [
            'path' => $storedPath,
            'url' => $this->getAvatarUrl($storedPath),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function uploadDocument(UploadedFile $file, string $userId, string $documentType): array
    {
        $filename = $this->generateUniqueFilename($file->getClientOriginalName());
        $path = "documents/{$userId}/{$documentType}/{$filename}";

        // Store file in S3 with private visibility
        $storedPath = $file->storeAs("documents/{$userId}/{$documentType}", $filename, [
            'disk' => 's3',
            'visibility' => 'private'
        ]);

        if (!$storedPath) {
            throw new FileUploadException();
        }

        return [
            'path' => $storedPath,
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAvatar(string $filePath): bool
    {
        return Storage::disk('s3')->delete($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(string $filePath): bool
    {
        return Storage::disk('s3')->delete($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function getAvatarUrl(string $filePath): string
    {
        return Storage::url($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function generateUniqueFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);

        // Clean the basename
        $basename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $basename);

        // Generate unique filename
        return $basename . '_' . Str::uuid() . '.' . $extension;
    }

    /**
     * {@inheritDoc}
     */
    public function generateTemporaryUrl(string $filePath, int $minutes = 60): string
    {
        // For private S3 files, create a signed URL that allows temporary access
        $disk = Storage::temporaryUrl($filePath, now()->addMinutes($minutes));
        if ($disk) {
            return $disk;
        }

        throw new FileUploadException('exception.file.generate_temporary_url_failed');
    }
}
