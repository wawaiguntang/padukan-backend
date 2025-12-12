<?php

namespace Modules\Tax\Enums;

enum TaxTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => __('tax::enum.tax_type.percentage'),
            self::FIXED => __('tax::enum.tax_type.fixed'),
        };
    }
}
