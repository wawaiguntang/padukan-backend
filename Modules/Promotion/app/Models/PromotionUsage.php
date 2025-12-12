<?php

namespace Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PromotionUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'promotion_usages';

    protected $fillable = [
        'promotion_id',
        'usage_by_type',
        'usage_by_id',
        'usage_on_type',
        'usage_on_id',
        'usage_at',
        'metadata',
    ];

    protected $casts = [
        'usage_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
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
