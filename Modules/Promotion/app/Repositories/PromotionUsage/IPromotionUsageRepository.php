<?php

namespace Modules\Promotion\Repositories\PromotionUsage;

use Illuminate\Database\Eloquent\Collection;
use Modules\Promotion\Models\PromotionUsage;

interface IPromotionUsageRepository
{
    /**
     * Record a promotion usage.
     *
     * @param array $data The data for recording the usage.
     *        Contains: ['promotion_id' => string, 'usage_by_type' => string, 'usage_by_id' => string,
     *        'usage_on_type' => string|null, 'usage_on_id' => string|null, 'usage_at' => string|null,
     *        'metadata' => array|null]
     * @return PromotionUsage The created promotion usage record.
     */
    public function recordUsage(array $data): PromotionUsage;

    /**
     * Get usage history for a promotion by a user.
     *
     * @param int $promotionId The ID of the promotion.
     * @param string $usageById The ID of the user.
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\PromotionUsage> A collection of promotion usages.
     */
    public function getUsageByUser(int $promotionId, string $usageById): Collection;

    /**
     * Get the total usage count for a promotion.
     *
     * @param int $promotionId The ID of the promotion.
     * @return int The total usage count.
     */
    public function getTotalUsage(int $promotionId): int;

    /**
     * Check if a promotion has reached its usage limit for a user.
     *
     * @param int $promotionId The ID of the promotion.
     * @param string $usageById The ID of the user.
     * @param int $limit The usage limit.
     * @return bool True if the limit has been reached, false otherwise.
     */
    public function hasReachedUsageLimit(int $promotionId, string $usageById, int $limit): bool;
}
