<?php

namespace Modules\Customer\Repositories\Document;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Enums\DocumentTypeEnum;
use Modules\Customer\Enums\VerificationStatusEnum;
use Modules\Customer\Models\Document;
use Modules\Customer\Cache\KeyManager\IKeyManager;

/**
 * Document Repository Implementation
 *
 * This class handles all document-related database operations
 * for the customer module with caching support.
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
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * Cache TTL in seconds (10 minutes - shorter for document data)
     *
     * @var int
     */
    protected int $cacheTtl = 600;

    /**
     * Constructor
     *
     * @param Document $model The Document model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Document $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByProfileId(string $profileId): Collection
    {
        $cacheKey = $this->cacheKeyManager::documentsByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return $this->model->where('profile_id', $profileId)->get();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Document
    {
        $cacheKey = $this->cacheKeyManager::documentById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Document
    {
        $document = $this->model->create($data);

        // Invalidate profile documents cache
        $this->invalidateProfileDocumentsCache($document->profile_id);

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $document = $this->model->find($id); // Don't use cached version for updates

        if (!$document) {
            return false;
        }

        $oldProfileId = $document->profile_id;
        $result = $document->update($data);

        if ($result) {
            $document->refresh();

            // Invalidate caches
            $this->invalidateDocumentCaches($id);
            $this->invalidateProfileDocumentsCache($oldProfileId);

            // If profile changed, invalidate new profile cache too
            if (isset($data['profile_id']) && $data['profile_id'] !== $oldProfileId) {
                $this->invalidateProfileDocumentsCache($data['profile_id']);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $document = $this->model->find($id); // Don't use cached version for deletes

        if (!$document) {
            return false;
        }

        $profileId = $document->profile_id;
        $result = $document->delete();

        if ($result) {
            // Invalidate all document caches
            $this->invalidateDocumentCaches($id);
            $this->invalidateProfileDocumentsCache($profileId);
        }

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
            ->where('profile_id', $profileId)
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

    /**
     * Invalidate profile documents cache
     *
     * @param string $profileId The profile ID
     * @return void
     */
    protected function invalidateProfileDocumentsCache(string $profileId): void
    {
        $this->cache->forget($this->cacheKeyManager::documentsByProfileId($profileId));
    }

    /**
     * Invalidate all cache keys related to a document
     *
     * @param string $documentId The document ID
     * @return void
     */
    protected function invalidateDocumentCaches(string $documentId): void
    {
        $this->cache->forget($this->cacheKeyManager::documentById($documentId));
    }
}
