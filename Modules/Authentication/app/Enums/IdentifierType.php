<?php

namespace Modules\Authentication\Enums;

enum IdentifierType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
}
