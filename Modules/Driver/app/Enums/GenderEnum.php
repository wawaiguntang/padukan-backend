<?php

namespace Modules\Driver\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::MALE => __('driver::enum.gender.male'),
            self::FEMALE => __('driver::enum.gender.female'),
            self::OTHER => __('driver::enum.gender.other'),
        };
    }
}