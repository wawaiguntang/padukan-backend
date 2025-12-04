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
        'merchant_id',
        'street',
        'city',
        'province',
        'country',
        'postal_code',
        'latitude',
        'longitude',
    ];

    /**
     * Get the merchant that owns the address.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    // protected static function newFactory(): MerchantAddressFactory
    // {
    //     // return MerchantAddressFactory::new();
    // }
}
