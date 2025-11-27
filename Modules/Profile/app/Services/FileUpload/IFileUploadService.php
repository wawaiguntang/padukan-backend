<?php

namespace Modules\Profile\Services\FileUpload;

interface IFileUploadService
{
    /**
     * Upload avatar file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $userId
     * @return array{path: string, url: string}
     */
    public function uploadAvatar(\Illuminate\Http\UploadedFile $file, string $userId): array;

    /**
     * Upload document file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $userId
     * @param string $type
     * @return array{path: string, url: string, file_name: string, mime_type: string, file_size: int}
     */
    public function uploadDocument(\Illuminate\Http\UploadedFile $file, string $userId, string $type): array;

    /**
     * Delete file
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * Get file URL
     *
     * @param string $path
     * @return string
     */
    public function getFileUrl(string $path): string;

    /**
     * Validate file for avatar upload
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function validateAvatarFile(\Illuminate\Http\UploadedFile $file): bool;

    /**
     * Validate file for document upload
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @return bool
     */
    public function validateDocumentFile(\Illuminate\Http\UploadedFile $file, string $type): bool;
}