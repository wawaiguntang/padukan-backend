<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Profile\Database\Factories\MerchantBankFactory;

class MerchantBank extends Model
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
        'bank_id',
        'account_number',
        'is_primary',
    ];

    /**
     * Get the merchant profile that owns the merchant bank.
     */
    public function merchantProfile(): BelongsTo
    {
        return $this->belongsTo(MerchantProfile::class);
    }

    /**
     * Get the bank that owns the merchant bank.
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    // protected static function newFactory(): MerchantBankFactory
    // {
    //     // return MerchantBankFactory::new();
    // }
}
