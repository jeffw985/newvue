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
 * Class AreaGroup
 *
 * @property int $id
 * @property string $area_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Customer> $customers
 */
class AreaGroup extends Model
{
    use SoftDeletes;

    protected $table = 'area_groups';

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'area_name',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
