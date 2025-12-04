<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Merchant extends Model
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
        'profile_id',
        'business_name',
        'business_description',
        'business_category',
        'slug',
        'phone',
        'email',
        'website',
        'address',
        'latitude',
        'longitude',
        'is_verified',
        'verification_status',
        'is_active',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'business_category' => \Modules\Merchant\Enums\BusinessCategoryEnum::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'verification_status' => \Modules\Merchant\Enums\VerificationStatusEnum::class,
            'status' => \Modules\Merchant\Enums\MerchantStatusEnum::class,
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
     * Get the profile that owns the merchant.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the schedules for the merchant.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(MerchantSchedule::class);
    }

    /**
     * Get the address for the merchant.
     */
    public function address(): HasOne
    {
        return $this->hasOne(MerchantAddress::class);
    }

    /**
     * Get the settings for the merchant.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(MerchantSetting::class);
    }
}
