<?php

namespace Modules\Driver\Enums;

enum VerificationStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ON_REVIEW = 'on_review';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::ON_REVIEW => 'On Review',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::ON_REVIEW => 'blue',
        };
    }
}