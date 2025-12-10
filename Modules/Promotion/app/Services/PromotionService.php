<?php

namespace Modules\Promotion\Services;

use App\Shared\Promotion\IPromotionService;
use Modules\Promotion\Models\Promotion;
use Modules\Promotion\Models\Coupon;
use Illuminate\Support\Facades\Cache;

class PromotionService implements IPromotionService
{
    /**
     * Calculate discount for a set of items (cart)
     */
    public function calculateCartDiscount(array $cartItems, ?string $userId, ?string $regionId, ?string $couponCode = null): array
    {
        // Placeholder implementation for cart discount logic
        // This would involve:
        // 1. Loading active promotions (auto-applied) based on rules (min purchase, etc)
        // 2. Loading coupon promotion if code provided
        // 3. Calculating benefit value
        // 4. Checking usage limits

        // For now, return 0 discount
        return [
            'total_discount' => 0,
            'breakdown' => [],
            'final_amount' => array_sum(array_column($cartItems, 'price'))
        ];
    }

    /**
     * Get active product discount (coret harga)
     */
    public function getProductDiscount(string $productId, string $regionId): ?array
    {
        // Simple implementation for Product Discount (Coret Harga)
        // Find active promotions of type 'product_discount'
        // That target this product (via rules)

        return Cache::remember("product_discount:{$productId}:{$regionId}", 300, function () use ($productId, $regionId) {
            $promotion = Promotion::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->whereHas('rules', function ($q) use ($productId) {
                    $q->where('rule_type', 'specific_products')
                        ->whereJsonContains('value', $productId);
                })
                ->whereHas('benefits', function ($q) {
                    $q->where('benefit_type', 'percentage_discount')
                        ->orWhere('benefit_type', 'fixed_discount');
                })
                ->with('benefits')
                ->first();

            if (!$promotion) {
                return null;
            }

            $benefit = $promotion->benefits->first();

            return [
                'promotion_id' => $promotion->id,
                'name' => $promotion->name,
                'type' => $benefit->benefit_type,
                'value' => (float) $benefit->value,
                'max_amount' => $benefit->max_discount_amount ? (float) $benefit->max_discount_amount : null,
            ];
        });
    }

    /**
     * Validate coupon code
     */
    public function validateCoupon(string $code, string $userId): array
    {
        $coupon = Coupon::where('code', $code)->with('promotion')->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => __('promotion::messages.coupon_not_found')];
        }

        if (!$coupon->promotion->is_active) {
            return ['valid' => false, 'message' => __('promotion::messages.promotion_inactive')];
        }

        // Add more checks: date range, usage limit, user eligibility

        return [
            'valid' => true,
            'message' => __('promotion::messages.coupon_valid'),
            'promotion' => $coupon->promotion
        ];
    }
}
