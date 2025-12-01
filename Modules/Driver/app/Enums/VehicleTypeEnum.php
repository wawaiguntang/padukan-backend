<?php

namespace Modules\Driver\Enums;

enum VehicleTypeEnum: string
{
    case MOTORCYCLE = 'motorcycle';
    case CAR = 'car';

    public function label(): string
    {
        return match($this) {
            self::MOTORCYCLE => 'Motorcycle',
            self::CAR => 'Car',
        };
    }
}