<?php

/** @noinspection ALL */
/** @noinspection SpellCheckingInspection */

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 *
 * @property int $id
 * @property string|null $city
 * @property string|null $full_name
 * @property string|null $email
 * @property bool $irrigation
 * @property bool $maintenance
 * @property string|null $notes
 * @property string|null $phone
 * @property string|null $state
 * @property string|null $street
 * @property string|null $zipcode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $area_group_id
 * @property Carbon|null $deleted_at
 * @property string|null $xero_contact_id
 * @property string|null $first
 * @property string|null $last
 * @property string|null $company
 * @property string|null $email2
 * @property string|null $phone2
 * @property string|null $spouse_first
 * @property string|null $spouse_last
 * @property-read AreaGroup|null $areaGroup
 * @property-read Collection<int, Irrigation> $irrigations
 * @property-read Collection<int, Ledger> $ledgers
 * @property-read Collection<int, Maintenance> $maintenances
 * @property-read Collection<int, ServiceSchedule> $serviceSchedules
 */
class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $casts = [
        'irrigation' => 'bool',
        'maintenance' => 'bool',
        'area_group_id' => 'int',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'city',
        'full_name',
        'email',
        'irrigation',
        'maintenance',
        'notes',
        'phone',
        'state',
        'street',
        'zipcode',
        'area_group_id',
        'xero_contact_id',
        'first',
        'last',
        'company',
        'email2',
        'phone2',
        'spouse_first',
        'spouse_last',
    ];

    public function areaGroup(): BelongsTo
    {
        return $this->belongsTo(AreaGroup::class);
    }

    public function irrigations(): HasMany
    {
        return $this->hasMany(Irrigation::class, 'cust_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class, 'cust_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'cust_id');
    }

    public function serviceSchedules(): HasMany
    {
        return $this->hasMany(ServiceSchedule::class, 'cust_id');
    }
}
