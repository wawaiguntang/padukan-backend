<?php

namespace Modules\Product\Services\FileUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * File Upload Service Implementation
 *
 * Handles file upload operations for the product module.
 */
class FileUploadService implements IFileUploadService
{
    protected string $disk = 'public';

    /**
     * Upload a product image
     */
    public function uploadProductImage(UploadedFile $file, string $merchantId, string $productId): array
    {
        $path = "products/{$merchantId}/{$productId}";
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

        $storedPath = $file->storeAs($path, $filename, ['disk' => $this->disk]);

        if (!$storedPath) {
            throw new RuntimeException('Failed to upload product image');
        }

        return [
            'path' => $storedPath,
            'url' => $this->getProductImageUrl($storedPath),
            'filename' => $filename,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    /**
     * Delete a product image
     */
    public function deleteProductImage(string $filePath): bool
    {
        if (Storage::disk($this->disk)->exists($filePath)) {
            return Storage::disk($this->disk)->delete($filePath);
        }
        return false;
    }

    /**
     * Get URL for a product image
     */
    public function getProductImageUrl(string $filePath): string
    {
        return Storage::disk($this->disk)->url($filePath);
    }
}
