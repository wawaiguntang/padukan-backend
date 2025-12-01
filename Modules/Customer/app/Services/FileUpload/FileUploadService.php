<?php

namespace Modules\Customer\Services\FileUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Customer\Exceptions\FileUploadException;
use Modules\Customer\Exceptions\FileValidationException;

/**
 * File Upload Service Implementation
 *
 * This class handles file upload operations for the customer module,
 * supporting both public (avatar) and private (document) file storage.
 */
class FileUploadService implements IFileUploadService
{
    /**
     * Allowed MIME types for avatar uploads
     */
    private const AVATAR_ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Maximum file size for avatars (5MB)
     */
    private const AVATAR_MAX_SIZE = 5242880; // 5MB in bytes

    /**
     * Allowed MIME types for document uploads
     */
    private const DOCUMENT_ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    /**
     * Maximum file size for documents (10MB)
     */
    private const DOCUMENT_MAX_SIZE = 10485760; // 10MB in bytes

    /**
     * {@inheritDoc}
     */
    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        $this->validateAvatarFile($file);

        $filename = $this->generateUniqueFilename($file->getClientOriginalName());
        $path = "avatars/{$userId}/{$filename}";

        // Store file in public disk
        $storedPath = $file->storeAs("avatars/{$userId}", $filename, 'public');

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
        $this->validateDocumentFile($file);

        $filename = $this->generateUniqueFilename($file->getClientOriginalName());
        $path = "documents/{$userId}/{$documentType}/{$filename}";

        // Store file in private disk (local storage, not publicly accessible)
        $storedPath = $file->storeAs("documents/{$userId}/{$documentType}", $filename, 'local');

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
        return Storage::disk('public')->delete($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(string $filePath): bool
    {
        return Storage::disk('local')->delete($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function getAvatarUrl(string $filePath): string
    {
        return asset('storage/' . $filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function validateAvatarFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::AVATAR_MAX_SIZE) {
            throw new FileValidationException('customer.file.too_large');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::AVATAR_ALLOWED_MIME_TYPES)) {
            throw new FileValidationException('customer.file.invalid_type');
        }

        // Check if file is actually an image
        $imageInfo = getimagesize($file->getPathname());
        if (!$imageInfo) {
            throw new FileValidationException('customer.file.avatar.not_image');
        }

        // Check image dimensions (optional - prevent extremely large images)
        if ($imageInfo[0] > 4096 || $imageInfo[1] > 4096) {
            throw new FileValidationException('customer.file.avatar.invalid_dimensions');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateDocumentFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::DOCUMENT_MAX_SIZE) {
            throw new FileValidationException('customer.file.too_large');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::DOCUMENT_ALLOWED_MIME_TYPES)) {
            throw new FileValidationException('customer.file.document.invalid_type');
        }
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
}
