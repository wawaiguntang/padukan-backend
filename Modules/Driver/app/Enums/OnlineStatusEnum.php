<?php

namespace Modules\Driver\Enums;

enum OnlineStatusEnum: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
        };
    }
}
