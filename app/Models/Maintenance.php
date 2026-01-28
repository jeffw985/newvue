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
 * Class Maintenance
 *
 * @property int $id
 * @property int $cust_id
 * @property bool $annual_pay
 * @property string|null $mulch_color
 * @property string|null $service_interval
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $site_address
 * @property int|null $sequence_order
 * @property Carbon|null $deleted_at
 * @property string|null $subdivision
 * @property-read Customer $customer
 */
class Maintenance extends Model
{
    use SoftDeletes;

    protected $table = 'maintenance';

    protected $casts = [
        'cust_id' => 'int',
        'annual_pay' => 'bool',
        'sequence_order' => 'int',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'cust_id',
        'annual_pay',
        'mulch_color',
        'service_interval',
        'site_address',
        'sequence_order',
        'subdivision',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
