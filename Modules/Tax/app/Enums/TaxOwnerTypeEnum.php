<?php

namespace Modules\Tax\Enums;

enum TaxOwnerTypeEnum: string
{
    case SYSTEM = 'system';
    case MERCHANT = 'merchant';
    case ORGANIZATION = 'organization';

    public function label(): string
    {
        return match ($this) {
            self::SYSTEM => __('tax::enum.tax_owner_type.system'),
            self::MERCHANT => __('tax::enum.tax_owner_type.merchant'),
            self::ORGANIZATION => __('tax::enum.tax_owner_type.organization'),
        };
    }
}
