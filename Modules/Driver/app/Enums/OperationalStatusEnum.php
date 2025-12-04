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
            self::AVAILABLE => __('driver::enum.operational_status.available'),
            self::ON_ORDER => __('driver::enum.operational_status.on_order'),
            self::REST => __('driver::enum.operational_status.rest'),
            self::SUSPENDED => __('driver::enum.operational_status.suspended'),
        };
    }
}
