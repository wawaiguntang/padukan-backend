<?php

namespace Modules\Promotion\Repositories\PromotionTarget;

use Illuminate\Database\Eloquent\Collection;
use Modules\Promotion\Models\PromotionTarget;

class PromotionTargetRepository implements IPromotionTargetRepository
{
    public function syncTargets(int $promotionId, array $targets): void
    {
        // Delete existing targets
        PromotionTarget::where('promotion_id', $promotionId)->delete();

        // Add new targets
        foreach ($targets as $target) {
            PromotionTarget::create([
                'promotion_id' => $promotionId,
                'target_type' => $target['target_type'],
                'target_id' => $target['target_id'],
                'operator' => $target['operator'] ?? 'include',
            ]);
        }
    }

    public function getTargets(int $promotionId): Collection
    {
        return PromotionTarget::where('promotion_id', $promotionId)->get();
    }
}
