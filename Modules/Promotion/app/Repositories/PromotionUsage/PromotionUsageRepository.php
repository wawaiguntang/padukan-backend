<?php

namespace Modules\Promotion\Repositories\PromotionUsage;

use Illuminate\Database\Eloquent\Collection;
use Modules\Promotion\Models\PromotionUsage;

class PromotionUsageRepository implements IPromotionUsageRepository
{
    public function recordUsage(array $data): PromotionUsage
    {
        return PromotionUsage::create($data);
    }

    public function getUsageByUser(int $promotionId, string $usageById): Collection
    {
        return PromotionUsage::where('promotion_id', $promotionId)
            ->where('usage_by_id', $usageById)
            ->get();
    }

    public function getTotalUsage(int $promotionId): int
    {
        return PromotionUsage::where('promotion_id', $promotionId)->count();
    }

    public function hasReachedUsageLimit(int $promotionId, string $usageById, int $limit): bool
    {
        $usageCount = $this->getUsageByUser($promotionId, $usageById)->count();
        return $usageCount >= $limit;
    }
}
