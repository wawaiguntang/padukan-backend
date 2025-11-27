<?php

namespace Modules\Profile\Services\FileUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Profile\Policies\DataManagement\IDocumentUploadPolicy;
use Modules\Profile\Exceptions\FileUploadException;
use Modules\Profile\Exceptions\InvalidFileException;

class FileUploadService implements IFileUploadService
{
    private IDocumentUploadPolicy $uploadPolicy;

    public function __construct(IDocumentUploadPolicy $uploadPolicy)
    {
        $this->uploadPolicy = $uploadPolicy;
    }

    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        try {
            // Validate file
            if (!$this->validateAvatarFile($file)) {
                throw new InvalidFileException('validation.invalid_avatar_file');
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;

            // Store file in S3 with public visibility
            $disk = 's3';
            $path = $file->storeAs('avatars', $filename, [
                'disk' => $disk,
                'visibility' => 'public'
            ]);

            if (!$path) {
                throw new FileUploadException('validation.avatar_upload_failed');
            }

            return [
                'path' => $path,
                'url' => $this->getFileUrl($path, $disk)
            ];
        } catch (\Exception $e) {
            throw new FileUploadException('validation.avatar_upload_failed', ['error' => $e->getMessage()]);
        }
    }

    public function uploadDocument(UploadedFile $file, string $userId, string $type): array
    {
        try {
            // Validate file
            if (!$this->validateDocumentFile($file, $type)) {
                throw new InvalidFileException('validation.invalid_document_file', ['type' => $type]);
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = $type . '_' . $userId . '_' . time() . '.' . $extension;

            // Store file in S3 with private visibility
            $disk = 's3';
            $path = $file->storeAs('documents', $filename, [
                'disk' => $disk,
                'visibility' => 'private'
            ]);

            if (!$path) {
                throw new FileUploadException('validation.document_upload_failed', ['type' => $type]);
            }

            return [
                'path' => $path,
                'url' => $this->getFileUrl($path, $disk),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ];
        } catch (\Exception $e) {
            throw new FileUploadException('validation.document_upload_failed', ['type' => $type, 'error' => $e->getMessage()]);
        }
    }

    public function deleteFile(string $path): bool
    {
        try {
            $disk = 's3';
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFileUrl(string $path, ?string $disk = null): string
    {
        $disk = $disk ?? 's3';

        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk($disk);

            // Check if file is public or private based on path
            $isPublic = str_starts_with($path, 'avatars/');

            if ($isPublic) {
                // For public files, return direct S3 URL
                return $storage->url($path);
            } else {
                // For private files, generate temporary signed URL (1 hour)
                return $storage->temporaryUrl($path, now()->addHour());
            }
        } catch (\Exception $e) {
            // Fallback to a generic URL structure
            return config('filesystems.disks.' . $disk . '.url') . '/' . $path;
        }
    }

    public function validateAvatarFile(UploadedFile $file): bool
    {
        // Check file size (5MB limit)
        if ($file->getSize() > 5242880) {
            return false;
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        return true;
    }

    public function validateDocumentFile(UploadedFile $file, string $type): bool
    {
        // Use upload policy for validation
        if (!$this->uploadPolicy->isFileSizeAllowed($file->getSize())) {
            return false;
        }

        if (!$this->uploadPolicy->isMimeTypeAllowed($file->getMimeType())) {
            return false;
        }

        return true;
    }
}