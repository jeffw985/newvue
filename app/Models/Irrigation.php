<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property string|null $site_address
 * @property int|null $sequence_order
 * @property string|null $subdivision
 * @property Carbon|null $required_by
 * @property string|null $required_reason
 * @property bool|null $paid
 * @property string|null $paid_amount
 * @property string|null $payment_type
 * @property string|null $submitted
 * @property string|null $billed
 * @property bool $prepayment_waived
 * @property bool $clear_list
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Customer $customer
 */
class Irrigation extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation';

    protected $casts = [
        'cust_id' => 'integer',
        'backflow_test_date' => 'date',
        'backflow_testing' => 'boolean',
        'blowout' => 'boolean',
        'blowout_date' => 'date',
        'turn_on' => 'boolean',
        'turn_on_date' => 'date',
        'sequence_order' => 'integer',
        'required_by' => 'date',
        'paid_amount' => 'decimal:2',
        'prepayment_waived' => 'boolean',
        'clear_list' => 'boolean',
        'deleted_at' => 'datetime',
        'pvb_ai_opened' => 'string',
        'pvb_cv_held' => 'string',
        'rp_rv_opened' => 'string',
        'rp_cv1_held' => 'string',
        'rp_cv2_held' => 'string',
        'backflow_test_pass' => 'string',
        'paid' => 'string',
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
        'required_by',
        'required_reason',
        'paid',
        'paid_amount',
        'payment_type',
        'submitted',
        'billed',
        'prepayment_waived',
        'clear_list',
    ];

    /** Relationships */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
