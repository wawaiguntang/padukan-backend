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
        return match ($this) {
            self::PENDING => __('driver::enum.verification_status.pending'),
            self::APPROVED => __('driver::enum.verification_status.approved'),
            self::REJECTED => __('driver::enum.verification_status.rejected'),
            self::ON_REVIEW => __('driver::enum.verification_status.on_review'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::ON_REVIEW => 'blue',
        };
    }
}
