<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a document is not found
 */
class DocumentNotFoundException extends BaseException
{
    /**
     * Create a new DocumentNotFoundException instance
     *
     * @param string $documentId The document ID that was not found
     */
    public function __construct(string $documentId)
    {
        parent::__construct('validation.document_not_found', ['id' => $documentId], 'profile', 404);
    }
}