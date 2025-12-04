<?php

namespace Modules\Merchant\Enums;

enum VerificationStatusEnum: string
{
    case PENDING = 'pending';
    case ON_REVIEW = 'on_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('merchant::enum.verification_status.pending'),
            self::ON_REVIEW => __('merchant::enum.verification_status.on_review'),
            self::APPROVED => __('merchant::enum.verification_status.approved'),
            self::REJECTED => __('merchant::enum.verification_status.rejected'),
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
