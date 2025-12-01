<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Profile\Database\Factories\MerchantAddressFactory;

class MerchantAddress extends Model
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
        'merchant_profile_id',
        'street',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
    ];

    /**
     * Get the merchant profile that owns the merchant address.
     */
    public function merchantProfile(): BelongsTo
    {
        return $this->belongsTo(MerchantProfile::class);
    }

    // protected static function newFactory(): MerchantAddressFactory
    // {
    //     // return MerchantAddressFactory::new();
    // }
}
