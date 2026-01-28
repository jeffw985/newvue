<?php

/** @noinspection PhpUnused */

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sheet
 *
 * @property int $id
 * @property Carbon $begin_date
 * @property Carbon $end_date
 * @property string|null $sheet_link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Ledger> $ledgers
 */
class Sheet extends Model
{
    use SoftDeletes;

    protected $table = 'sheets';

    protected $casts = [
        'begin_date' => 'date',
        'end_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'begin_date',
        'end_date',
        'sheet_link',
    ];

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class, 'sheet_link');
    }
}
