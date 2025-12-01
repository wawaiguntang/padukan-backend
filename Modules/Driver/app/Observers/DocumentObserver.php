<?php

namespace Modules\Driver\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Cache\KeyManager\IKeyManager;
use Modules\Driver\Models\Document;

/**
 * Document Model Observer
 *
 * Handles cache management for Document model events
 */
class DocumentObserver
{
    /**
     * Cache repository instance
     */
    protected Cache $cache;

    /**
     * Cache key manager instance
     */
    protected IKeyManager $keyManager;

    /**
     * Cache TTL in seconds (10 minutes)
     */
    protected int $cacheTtl = 600;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Document "created" event
     */
    public function created(Document $document): void
    {
        $this->invalidateRelatedCaches($document);
    }

    /**
     * Handle the Document "updated" event
     */
    public function updated(Document $document): void
    {
        // Get original documentable info before update
        $originalDocumentableId = $document->getOriginal('documentable_id');
        $originalDocumentableType = $document->getOriginal('documentable_type');

        // Invalidate caches for both old and new relationships
        $this->invalidateDocumentableCaches($originalDocumentableId, $originalDocumentableType);

        // If documentable changed, invalidate new documentable cache too
        if (
            $document->documentable_id !== $originalDocumentableId ||
            $document->documentable_type !== $originalDocumentableType
        ) {
            $this->invalidateDocumentableCaches($document->documentable_id, $document->documentable_type);
        }
    }

    /**
     * Handle the Document "deleted" event
     */
    public function deleted(Document $document): void
    {
        $this->invalidateDocumentableCaches($document->documentable_id, $document->documentable_type);
    }

    /**
     * Invalidate caches related to the documentable entity (Profile or Vehicle)
     */
    protected function invalidateDocumentableCaches(string $documentableId, string $documentableType): void
    {
        // For now, we only cache documents by profile_id
        // If document belongs to a profile, invalidate profile documents cache
        if ($documentableType === 'Modules\Driver\Models\Profile') {
            $this->cache->forget($this->keyManager::documentsByProfileId($documentableId));
        }
    }

    /**
     * Invalidate all caches related to this document
     */
    protected function invalidateRelatedCaches(Document $document): void
    {
        $this->invalidateDocumentableCaches($document->documentable_id, $document->documentable_type);
    }
}
