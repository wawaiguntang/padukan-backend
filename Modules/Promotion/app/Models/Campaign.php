<?php

namespace Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'campaigns';

    protected $fillable = [
        'name',
        'description',
        'banner_image',
        'start_at',
        'end_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'campaign_promotions');
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
