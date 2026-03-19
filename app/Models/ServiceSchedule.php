<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class ServiceSchedule
 *
 * @property int $id
 * @property string|null $crew_assigned
 * @property string|null $crew_comments
 * @property int $cust_id
 * @property Carbon|null $end_time
 * @property string|null $service_notes
 * @property string|null $service_status
 * @property string|null $service_requested
 * @property Carbon|null $start_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $crew_status
 * @property string|null $site_address
 * @property-read Customer $customer
 */
class ServiceSchedule extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $table = 'service_schedule';

    protected $fillable = [
        'crew_assigned',
        'crew_comments',
        'cust_id',
        'end_time',
        'service_notes',
        'service_status',
        'service_requested',
        'start_time',
        'crew_status',
        'site_address',
        'google_event_id',
        'last_synced_at',
    ];

    /** @noinspection PhpUnused */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'cust_id', 'cust_id');
    }

    /**
     * Register media collections and conversions
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('crew_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/heic']);
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'cust_id' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'deleted_at' => 'datetime',
            'service_requested' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * Register media conversions for thumbnails
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->format('jpg')
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->format('jpg')
            ->nonQueued();
    }
}
