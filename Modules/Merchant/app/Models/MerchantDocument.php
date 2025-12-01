<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Profile\Database\Factories\MerchantDocumentFactory;

class MerchantDocument extends Model
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
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'meta',
        'expiry_date',
        'is_verified',
        'verification_status',
        'verified_at',
        'verified_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => \Modules\Profile\Enums\MerchantDocumentTypeEnum::class,
            'meta' => 'array',
            'expiry_date' => 'date',
            'verification_status' => \Modules\Profile\Enums\VerificationStatusEnum::class,
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the merchant profile that owns the document.
     */
    public function merchantProfile(): BelongsTo
    {
        return $this->belongsTo(MerchantProfile::class);
    }

    // protected static function newFactory(): MerchantDocumentFactory
    // {
    //     // return MerchantDocumentFactory::new();
    // }
}
