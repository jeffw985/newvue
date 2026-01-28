<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prospect
 *
 * @property int $id
 * @property string|null $city
 * @property Carbon|null $confirmation
 * @property Carbon|null $date
 * @property string|null $email
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $state
 * @property string|null $street
 * @property string|null $work_description
 * @property string|null $zip
 */
class Prospect extends Model
{
    protected $table = 'prospects';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'confirmation' => 'datetime',
        'date' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'city',
        'confirmation',
        'date',
        'email',
        'name',
        'phone',
        'state',
        'street',
        'work_description',
        'zip',
    ];
}
