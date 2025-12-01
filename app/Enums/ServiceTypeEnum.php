<?php

namespace App\Enums;

enum ServiceTypeEnum: string
{
    case FOOD = 'food';
    case RIDE = 'ride';
    case CAR = 'car';
    case SEND = 'send';
    case MART = 'mart';

    public function label(): string
    {
        return match ($this) {
            self::FOOD => 'Food Delivery',
            self::RIDE => 'Ride Sharing',
            self::CAR => 'Car Rental',
            self::SEND => 'Courier Service',
            self::MART => 'Mart Delivery',
        };
    }
}
