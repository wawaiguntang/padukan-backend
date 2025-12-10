<?php

namespace Modules\Product\Services\FileUpload;

use Illuminate\Http\UploadedFile;

/**
 * Interface for File Upload Service
 *
 * This interface defines the contract for file upload operations
 * in the product module.
 */
interface IFileUploadService
{
    /**
     * Upload a product image
     *
     * @param UploadedFile $file The uploaded file
     * @param string $merchantId The merchant ID for organizing files
     * @param string $productId The product ID for organizing files
     * @return array The uploaded file details (path, url, type, etc.)
     */
    public function uploadProductImage(UploadedFile $file, string $merchantId, string $productId): array;

    /**
     * Delete a product image
     *
     * @param string $filePath The file path to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteProductImage(string $filePath): bool;

    /**
     * Get URL for a product image
     *
     * @param string $filePath The file path
     * @return string The public URL
     */
    public function getProductImageUrl(string $filePath): string;
}
