<?php

namespace Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'short_description',
        'terms_conditions',
        'banner_image',
        'owner_type',
        'owner_id',
        'priority',
        'stackable',
        'start_at',
        'end_at',
        'status',
        'rules_json',
        'actions_json',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stackable' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'rules_json' => 'array',
        'actions_json' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the targets for the promotion.
     */
    public function targets(): HasMany
    {
        return $this->hasMany(PromotionTarget::class);
    }

    /**
     * Get the usages for the promotion.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    /**
     * The campaigns that belong to the promotion.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_promotions');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
