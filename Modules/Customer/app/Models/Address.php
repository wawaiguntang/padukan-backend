<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Profile\Database\Factories\AddressFactory;

class Address extends Model
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
        'profile_id',
        'type',
        'label',
        'street',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => \Modules\Customer\Enums\AddressTypeEnum::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the profile that owns the address.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // protected static function newFactory(): AddressFactory
    // {
    //     // return AddressFactory::new();
    // }
}
