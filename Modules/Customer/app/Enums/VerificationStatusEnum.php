<?php

namespace Modules\Customer\Enums;

enum VerificationStatusEnum: string
{
    case PENDING = 'pending';
    case ON_REVIEW = 'on_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ON_REVIEW => 'On Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::ON_REVIEW => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }
}
