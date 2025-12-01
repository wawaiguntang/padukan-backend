<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Modules\Profile\Database\Factories\MerchantProfileFactory;

class MerchantProfile extends Model
{
    use HasFactory;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'business_phone',
        'is_verified',
        'verification_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'business_type' => \Modules\Profile\Enums\BusinessTypeEnum::class,
            'verification_status' => \Modules\Profile\Enums\VerificationStatusEnum::class,
        ];
    }


    /**
     * Get the merchant banks for the merchant profile.
     */
    public function merchantBanks(): HasMany
    {
        return $this->hasMany(MerchantBank::class);
    }

    /**
     * Get the merchant address for the merchant profile.
     */
    public function merchantAddress(): HasOne
    {
        return $this->hasOne(MerchantAddress::class);
    }

    /**
     * Get the documents for the merchant profile.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(MerchantDocument::class);
    }

    // protected static function newFactory(): MerchantProfileFactory
    // {
    //     // return MerchantProfileFactory::new();
    // }
}
