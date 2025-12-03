<?php

namespace Modules\Merchant\Enums;

enum BusinessCategoryEnum: string
{
    case FOOD = 'food';
    case MART = 'mart';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::FOOD => 'Food',
            self::MART => 'Mart',
            self::SERVICE => 'Service',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FOOD => 'green',
            self::MART => 'blue',
            self::SERVICE => 'purple',
        };
    }
}
