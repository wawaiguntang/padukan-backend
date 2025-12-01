<?php

namespace Modules\Driver\Enums;

enum OperationalStatusEnum: string
{
    case AVAILABLE = 'available';
    case ON_ORDER = 'on_order';
    case REST = 'rest';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::ON_ORDER => 'On Order',
            self::REST => 'Rest',
            self::SUSPENDED => 'Suspended',
        };
    }
}
