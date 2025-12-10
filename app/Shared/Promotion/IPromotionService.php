<?php

namespace App\Shared\Promotion;

interface IPromotionService
{
    /**
     * Calculate discount for a set of items (cart)
     *
     * @param array $cartItems Array of items with 'product_id', 'price', 'quantity'
     * @param string|null $userId User ID
     * @param string|null $regionId Region ID
     * @param string|null $couponCode Coupon code (optional)
     * @return array Calculation result with 'total_discount', 'breakdown', 'final_amount'
     */
    public function calculateCartDiscount(array $cartItems, ?string $userId, ?string $regionId, ?string $couponCode = null): array;

    /**
     * Get active product discount (coret harga)
     *
     * @param string $productId
     * @param string $regionId
     * @return array|null Discount details or null if none
     */
    public function getProductDiscount(string $productId, string $regionId): ?array;

    /**
     * Validate coupon code
     *
     * @param string $code
     * @param string $userId
     * @return array Validation result with 'valid' (bool) and 'message'
     */
    public function validateCoupon(string $code, string $userId): array;
}
