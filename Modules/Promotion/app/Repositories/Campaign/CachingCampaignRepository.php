<?php

namespace Modules\Promotion\Repositories\Campaign;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Promotion\Cache\Campaign\CampaignCacheManager;
use Modules\Promotion\Cache\Campaign\CampaignKeyManager;
use Modules\Promotion\Cache\Campaign\CampaignTtlManager;
use Modules\Promotion\Models\Campaign;

/**
 * Caching Campaign Repository
 *
 * Decorates CampaignRepository with caching functionality.
 */
class CachingCampaignRepository implements ICampaignRepository
{
    private CampaignRepository $repository;

    public function __construct(CampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): Campaign
    {
        $campaign = $this->repository->create($data);

        // Invalidate related caches
        CampaignCacheManager::invalidateForOperation('create', ['id' => $campaign->id]);

        return $campaign;
    }

    public function update(int $id, array $data): Campaign
    {
        $campaign = $this->repository->update($id, $data);

        // Invalidate related caches
        CampaignCacheManager::invalidateForOperation('update', ['id' => $id, 'data' => $data]);

        return $campaign;
    }

    public function delete(int $id): bool
    {
        $result = $this->repository->delete($id);

        if ($result) {
            // Invalidate related caches
            CampaignCacheManager::invalidateForOperation('delete', ['id' => $id]);
        }

        return $result;
    }

    public function find(int $id): ?Campaign
    {
        $cacheKey = CampaignKeyManager::campaignById($id);
        $ttl = CampaignTtlManager::getCampaignEntityTtl();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function findOrFail(int $id): Campaign
    {
        $campaign = $this->find($id);

        if (!$campaign) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Campaign with ID {$id} not found.");
        }

        return $campaign;
    }

    public function getAll(array $filters = []): Collection
    {
        $cacheKey = CampaignKeyManager::campaignsList($filters);
        $ttl = CampaignTtlManager::getCampaignListTtl();

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            return $this->repository->getAll($filters);
        });
    }

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = CampaignKeyManager::campaignsPaginated($perPage, $filters);
        $ttl = CampaignTtlManager::getCampaignListTtl();

        return Cache::remember($cacheKey, $ttl, function () use ($perPage, $filters) {
            return $this->repository->getPaginated($perPage, $filters);
        });
    }

    public function getActiveCampaigns(): Collection
    {
        $cacheKey = CampaignKeyManager::activeCampaigns();
        $ttl = CampaignTtlManager::getActiveCampaignsTtl();

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->repository->getActiveCampaigns();
        });
    }

    public function addPromotion(int $campaignId, int $promotionId, ?array $owner = null): bool
    {
        $result = $this->repository->addPromotion($campaignId, $promotionId, $owner);

        if ($result) {
            // Invalidate related caches
            CampaignCacheManager::invalidateForOperation('add_promotion', ['campaign_id' => $campaignId]);
        }

        return $result;
    }

    public function removePromotion(int $campaignId, int $promotionId, ?array $owner = null): bool
    {
        $result = $this->repository->removePromotion($campaignId, $promotionId, $owner);

        if ($result) {
            // Invalidate related caches
            CampaignCacheManager::invalidateForOperation('remove_promotion', ['campaign_id' => $campaignId]);
        }

        return $result;
    }

    public function syncPromotions(int $campaignId, array $promotionIds, ?array $owner = null): void
    {
        $this->repository->syncPromotions($campaignId, $promotionIds, $owner);

        // Invalidate related caches
        CampaignCacheManager::invalidateForOperation('sync_promotions', ['campaign_id' => $campaignId]);
    }

    public function getPromotions(int $campaignId): Collection
    {
        $cacheKey = CampaignKeyManager::campaignPromotions($campaignId);
        $ttl = CampaignTtlManager::getCampaignPromotionsTtl();

        return Cache::remember($cacheKey, $ttl, function () use ($campaignId) {
            return $this->repository->getPromotions($campaignId);
        });
    }

    public function flushCacheForCampaign(int $id): void
    {
        CampaignCacheManager::invalidateCampaignEntity($id);
        CampaignCacheManager::invalidateCampaignPromotions($id);
    }
}
