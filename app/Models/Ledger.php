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
 * Class Ledger
 *
 * @property int $id
 * @property bool|null $billed
 * @property int $cust_id
 * @property Carbon $work_date
 * @property float|null $hours
 * @property string|null $job_code
 * @property string|null $job_notes
 * @property string $work_type
 * @property string|null $sheet_number
 * @property string|null $times
 * @property string|null $work_performed
 * @property int|null $sheet_link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 * @property-read Sheet|null $sheet
 */
class Ledger extends Model
{
    use SoftDeletes;

    protected $table = 'ledger';

    protected $casts = [
        'billed' => 'bool',
        'cust_id' => 'int',
        'work_date' => 'datetime',
        'hours' => 'decimal:2',
        'sheet_link' => 'int',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'billed',
        'cust_id',
        'work_date',
        'hours',
        'job_code',
        'job_notes',
        'work_type',
        'sheet_number',
        'times',
        'work_performed',
        'sheet_link',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class, 'sheet_link');
    }
}
