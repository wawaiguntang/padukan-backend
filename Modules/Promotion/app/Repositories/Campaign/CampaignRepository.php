<?php

namespace Modules\Promotion\Repositories\Campaign;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Promotion\Models\Campaign;
use Modules\Promotion\Repositories\Promotion\IPromotionRepository;

class CampaignRepository implements ICampaignRepository
{
    protected $promotionRepository;

    public function __construct(IPromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    public function update(int $id, array $data): Campaign
    {
        $campaign = $this->findOrFail($id);
        $campaign->update($data);
        return $campaign;
    }

    public function delete(int $id): bool
    {
        $campaign = $this->findOrFail($id);
        return $campaign->delete();
    }

    public function find(int $id): ?Campaign
    {
        return Campaign::find($id);
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
        $query = Campaign::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Campaign::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function getActiveCampaigns(): Collection
    {
        return Campaign::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->get();
    }

    public function addPromotion(int $campaignId, int $promotionId, ?array $owner = null): bool
    {
        $campaign = $this->findOrFail($campaignId);
        $this->promotionRepository->findOrFail($promotionId, $owner); // Validate ownership
        $campaign->promotions()->attach($promotionId);
        return true;
    }

    public function removePromotion(int $campaignId, int $promotionId, ?array $owner = null): bool
    {
        $campaign = $this->findOrFail($campaignId);
        $this->promotionRepository->findOrFail($promotionId, $owner); // Validate ownership
        $campaign->promotions()->detach($promotionId);
        return true;
    }

    public function syncPromotions(int $campaignId, array $promotionIds, ?array $owner = null): void
    {
        $campaign = $this->findOrFail($campaignId);

        if ($owner) {
            // Validate all promotions belong to the owner
            foreach ($promotionIds as $promoId) {
                $this->promotionRepository->findOrFail($promoId, $owner);
            }
        }

        $campaign->promotions()->sync($promotionIds);
    }

    public function getPromotions(int $campaignId): Collection
    {
        $campaign = $this->findOrFail($campaignId);
        return $campaign->promotions;
    }

    public function flushCacheForCampaign(int $id): void
    {
        \Modules\Promotion\Cache\Campaign\CampaignCacheManager::invalidateCampaignEntity($id);
        \Modules\Promotion\Cache\Campaign\CampaignCacheManager::invalidateCampaignPromotions($id);
    }
}
