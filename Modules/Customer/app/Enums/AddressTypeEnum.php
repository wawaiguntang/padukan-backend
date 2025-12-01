<?php

namespace Modules\Customer\Enums;

enum AddressTypeEnum: string
{
    case HOME = 'home';
    case WORK = 'work';
    case BUSINESS = 'business';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::HOME => 'Home',
            self::WORK => 'Work',
            self::BUSINESS => 'Business',
            self::OTHER => 'Other',
        };
    }
}