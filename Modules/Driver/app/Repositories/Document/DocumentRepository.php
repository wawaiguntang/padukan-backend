<?php

namespace Modules\Driver\Repositories\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Enums\VerificationStatusEnum;
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
    public function findByProfileId(string $profileId): Collection
    {
        return $this->model->where('documentable_id', $profileId)
            ->where('documentable_type', \Modules\Driver\Models\Profile::class)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Document
    {
        return $this->model->find($id);
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
    public function delete(string $id): bool
    {
        $document = $this->model->find($id);

        if (!$document) {
            return false;
        }

        $result = $document->delete();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $id, VerificationStatusEnum $status, ?string $verifiedBy = null): bool
    {
        $data = ['verification_status' => $status];

        if ($verifiedBy) {
            $data['verified_by'] = $verifiedBy;
            $data['verified_at'] = now();
        }

        return $this->update($id, $data);
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

    /**
     * {@inheritDoc}
     */
    public function existsById(string $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
