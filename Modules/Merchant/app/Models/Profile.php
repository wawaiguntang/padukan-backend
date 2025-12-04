<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

// use Modules\Profile\Database\Factories\MerchantProfileFactory;

class Profile extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'merchant';

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
        'max_merchant',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verification_status' => \Modules\Merchant\Enums\VerificationStatusEnum::class,
            'gender' => \Modules\Merchant\Enums\GenderEnum::class,
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
            // Set max_merchant based on verification status
            $model->max_merchant = $model->is_verified ? 10 : 1;
        });

        static::updating(function ($model) {
            // Update max_merchant when verification status changes
            if ($model->isDirty('is_verified')) {
                $model->max_merchant = $model->is_verified ? 10 : 1;
            }
        });
    }

    /**
     * Get the documents for the merchant profile.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the merchants for the profile.
     */
    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }


    // protected static function newFactory(): MerchantProfileFactory
    // {
    //     // return MerchantProfileFactory::new();
    // }
}
