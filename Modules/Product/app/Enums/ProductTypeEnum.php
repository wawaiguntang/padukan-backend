<?php

namespace Modules\Product\Enums;

enum ProductTypeEnum: string
{
    case FOOD = 'food';
    case MART = 'mart';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::FOOD => __('product::enum.product_type.food'),
            self::MART => __('product::enum.product_type.mart'),
            self::SERVICE => __('product::enum.product_type.service'),
        };
    }
}
