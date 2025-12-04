<?php

namespace Modules\Merchant\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MALE => __('merchant::enum.gender.male'),
            self::FEMALE => __('merchant::enum.gender.female'),
            self::OTHER => __('merchant::enum.gender.other'),
        };
    }
}
