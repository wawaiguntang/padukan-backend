<?php

namespace Modules\Customer\Services\FileUpload;

use Illuminate\Http\UploadedFile;

/**
 * Interface for File Upload Service
 *
 * This interface defines the contract for file upload operations
 * in the customer module, handling both public and private files.
 */
interface IFileUploadService
{
    /**
     * Upload an avatar file (public access)
     *
     * @param UploadedFile $file The uploaded file
     * @param string $userId The user ID for organizing files
     * @return array Returns array with 'path', 'url', 'filename', 'size', 'mime_type'
     * @throws \Exception If upload fails or file validation fails
     */
    public function uploadAvatar(UploadedFile $file, string $userId): array;

    /**
     * Upload a document file (private access)
     *
     * @param UploadedFile $file The uploaded file
     * @param string $userId The user ID for organizing files
     * @param string $documentType The document type for subfolder organization
     * @return array Returns array with 'path', 'filename', 'size', 'mime_type'
     * @throws \Exception If upload fails or file validation fails
     */
    public function uploadDocument(UploadedFile $file, string $userId, string $documentType): array;

    /**
     * Delete an avatar file
     *
     * @param string $filePath The file path to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteAvatar(string $filePath): bool;

    /**
     * Delete a document file
     *
     * @param string $filePath The file path to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteDocument(string $filePath): bool;

    /**
     * Get the full URL for a public avatar file
     *
     * @param string $filePath The file path
     * @return string The full URL
     */
    public function getAvatarUrl(string $filePath): string;

    /**
     * Validate file for avatar upload
     *
     * @param UploadedFile $file The file to validate
     * @throws \Exception If validation fails
     */
    public function validateAvatarFile(UploadedFile $file): void;

    /**
     * Validate file for document upload
     *
     * @param UploadedFile $file The file to validate
     * @throws \Exception If validation fails
     */
    public function validateDocumentFile(UploadedFile $file): void;

    /**
     * Generate a unique filename
     *
     * @param string $originalName The original filename
     * @return string The unique filename
     */
    public function generateUniqueFilename(string $originalName): string;
}
