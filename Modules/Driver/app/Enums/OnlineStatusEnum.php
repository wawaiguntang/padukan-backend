<?php

namespace Modules\Driver\Enums;

enum OnlineStatusEnum: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => __('driver::enum.online_status.online'),
            self::OFFLINE => __('driver::enum.online_status.offline'),
        };
    }
}
