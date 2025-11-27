<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Modules\Profile\Database\Factories\ProfileFactory;

class Profile extends Model
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
        'first_name',
        'last_name',
        'avatar',
        'gender',
        'language',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'gender' => \Modules\Profile\Enums\GenderEnum::class,
        ];
    }

    /**
     * Get the addresses for the profile.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the driver profile for the profile.
     */
    public function driverProfile(): HasOne
    {
        return $this->hasOne(DriverProfile::class);
    }

    /**
     * Get the merchant profile for the profile.
     */
    public function merchantProfile(): HasOne
    {
        return $this->hasOne(MerchantProfile::class);
    }

    /**
     * Get the customer profile for the profile.
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    // protected static function newFactory(): ProfileFactory
    // {
    //     // return ProfileFactory::new();
    // }
}
