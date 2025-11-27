<?php

namespace Modules\Profile\Enums;

enum BusinessTypeEnum: string
{
    case FOOD = 'food';
    case MART = 'mart';

    public function label(): string
    {
        return match($this) {
            self::FOOD => 'Food',
            self::MART => 'Mart',
        };
    }
}