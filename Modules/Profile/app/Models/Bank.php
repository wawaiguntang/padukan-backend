<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Modules\Profile\Database\Factories\BankFactory;

class Bank extends Model
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
        'name',
        'code',
        'is_active',
    ];

    /**
     * Get the merchant banks for the bank.
     */
    public function merchantBanks(): HasMany
    {
        return $this->hasMany(MerchantBank::class);
    }

    // protected static function newFactory(): BankFactory
    // {
    //     // return BankFactory::new();
    // }
}
