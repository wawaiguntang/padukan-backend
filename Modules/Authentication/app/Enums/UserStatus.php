<?php

namespace Modules\Authentication\Enums;

enum UserStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPEND = 'suspend';
}
