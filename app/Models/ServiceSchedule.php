<?php

/** @noinspection SpellCheckingInspection */

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property array|null $service_requested
 * @property Carbon|null $start_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $crew_status
 * @property string|null $site_address
 * @property-read Customer $customer
 */
class ServiceSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'service_schedule';

    protected $casts = [
        'cust_id' => 'int',
        'end_time' => 'datetime',
        'start_time' => 'datetime',
        'deleted_at' => 'datetime',
        'service_requested' => 'array',
    ];

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
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
