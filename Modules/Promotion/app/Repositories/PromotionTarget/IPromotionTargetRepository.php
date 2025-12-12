<?php

namespace Modules\Promotion\Repositories\PromotionTarget;

use Illuminate\Database\Eloquent\Collection;

interface IPromotionTargetRepository
{
    /**
     * Sync targets for a promotion.
     *
     * @param int $promotionId The ID of the promotion.
     * @param array $targets An array of targets.
     *        Each target is an array: ['target_type' => 'product', 'target_id' => '123', 'operator' => 'include']
     * @return void
     */
    public function syncTargets(int $promotionId, array $targets): void;

    /**
     * Get all targets for a promotion.
     *
     * @param int $promotionId The ID of the promotion.
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\PromotionTarget> A collection of promotion targets.
     */
    public function getTargets(int $promotionId): Collection;
}
