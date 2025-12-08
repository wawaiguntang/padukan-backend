<?php

namespace Modules\Product\Enums;

enum ProductStatusEnum: string
{
    case AVAILABLE = 'available';
    case NOT_AVAILABLE = 'not_available';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => __('product::enum.product_status.available'),
            self::NOT_AVAILABLE => __('product::enum.product_status.not_available'),
        };
    }
}
