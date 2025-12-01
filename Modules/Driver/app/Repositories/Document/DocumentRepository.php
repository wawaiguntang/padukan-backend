<?php

namespace Modules\Driver\Repositories\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Models\Document;

/**
 * Document Repository Implementation
 *
 * This class handles all document-related database operations
 * for the driver module with caching support.
 */
class DocumentRepository implements IDocumentRepository
{
    /**
     * The Document model instance
     *
     * @var Document
     */
    protected Document $model;

    /**
     * Constructor
     *
     * @param Document $model The Document model instance
     */
    public function __construct(Document $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Document
    {
        $document = $this->model->create($data);

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $document = $this->model->find($id);

        if (!$document) {
            return false;
        }

        $result = $document->update($data);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypeAndProfileId(string $profileId, DocumentTypeEnum $type): Collection
    {
        return $this->model
            ->where('documentable_id', $profileId)
            ->where('documentable_type', \Modules\Driver\Models\Profile::class)
            ->where('type', $type)
            ->get();
    }
}
