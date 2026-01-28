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
 * Class Irrigation
 *
 * @property int $id
 * @property int $cust_id
 * @property string|null $backflow_brand
 * @property string|null $backflow_location
 * @property string|null $backflow_model
 * @property string|null $backflow_serial
 * @property Carbon|null $backflow_test_date
 * @property bool $backflow_testing
 * @property bool $backflow_test_pass
 * @property string|null $backflow_type
 * @property bool $blowout
 * @property Carbon|null $blowout_date
 * @property string|null $controller_location
 * @property string|null $irrig_notes
 * @property string|null $pvb_ai
 * @property bool $pvb_ai_opened
 * @property string|null $pvb_cv
 * @property bool $pvb_cv_held
 * @property string|null $rp_cv1
 * @property bool $rp_cv1_held
 * @property string|null $rp_cv2
 * @property bool $rp_cv2_held
 * @property string|null $rp_rv
 * @property bool $rp_rv_opened
 * @property string|null $site_id
 * @property bool $turn_on
 * @property Carbon|null $turn_on_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $site_address
 * @property int|null $sequence_order
 * @property Carbon|null $deleted_at
 * @property string|null $subdivision
 * @property-read Customer $customer
 */
class Irrigation extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation';

    protected $casts = [
        'cust_id' => 'int',
        'backflow_test_date' => 'date',
        'backflow_testing' => 'bool',
        'backflow_test_pass' => 'bool',
        'blowout' => 'bool',
        'blowout_date' => 'date',
        'pvb_ai_opened' => 'bool',
        'pvb_cv_held' => 'bool',
        'rp_cv1_held' => 'bool',
        'rp_cv2_held' => 'bool',
        'rp_rv_opened' => 'bool',
        'turn_on' => 'bool',
        'turn_on_date' => 'date',
        'sequence_order' => 'int',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'cust_id',
        'backflow_brand',
        'backflow_location',
        'backflow_model',
        'backflow_serial',
        'backflow_test_date',
        'backflow_testing',
        'backflow_test_pass',
        'backflow_type',
        'blowout',
        'blowout_date',
        'controller_location',
        'irrig_notes',
        'pvb_ai',
        'pvb_ai_opened',
        'pvb_cv',
        'pvb_cv_held',
        'rp_cv1',
        'rp_cv1_held',
        'rp_cv2',
        'rp_cv2_held',
        'rp_rv',
        'rp_rv_opened',
        'site_id',
        'turn_on',
        'turn_on_date',
        'site_address',
        'sequence_order',
        'subdivision',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
