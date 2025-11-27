<?php

namespace Modules\Profile\Services\Customer;

interface ICustomerDocumentService
{
    /**
     * Get all documents for a customer
     *
     * @param string $userId
     * @return array
     */
    public function getDocuments(string $userId): array;

    /**
     * Get specific document for a customer
     *
     * @param string $userId
     * @param string $documentId
     * @return array
     */
    public function getDocument(string $userId, string $documentId): array;

    /**
     * Create new document for customer
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createDocument(string $userId, array $data): array;

    /**
     * Update document for customer
     *
     * @param string $userId
     * @param string $documentId
     * @param array $data
     * @return array
     */
    public function updateDocument(string $userId, string $documentId, array $data): array;

    /**
     * Delete document for customer
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function deleteDocument(string $userId, string $documentId): bool;

    /**
     * Get document file URL (temporary for private files)
     *
     * @param string $userId
     * @param string $documentId
     * @return string
     */
    public function getDocumentFileUrl(string $userId, string $documentId): string;
}