<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

// use Modules\Profile\Database\Factories\CustomerProfileFactory;

class Profile extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'customer';

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
        'is_verified',
        'verification_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verification_status' => \Modules\Customer\Enums\VerificationStatusEnum::class,
            'gender' => \Modules\Customer\Enums\GenderEnum::class,
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the documents for the customer profile.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the addresses for the customer profile.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    // protected static function newFactory(): CustomerProfileFactory
    // {
    //     // return CustomerProfileFactory::new();
    // }
}
