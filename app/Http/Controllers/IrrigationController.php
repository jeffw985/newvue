<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers;

use App\Models\Irrigation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IrrigationController extends Controller
{
    /**
     * Store a newly created irrigation record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'site_address' => 'nullable|string|max:255',
            'subdivision' => 'nullable|string|max:255',
            'site_id' => 'nullable|string|max:255',
            'sequence_order' => 'nullable|integer',
            'controller_location' => 'nullable|string|max:255',
            'turn_on' => 'boolean',
            'turn_on_date' => 'nullable|date',
            'blowout' => 'boolean',
            'blowout_date' => 'nullable|date',
            'backflow_brand' => 'nullable|string|max:255',
            'backflow_model' => 'nullable|string|max:255',
            'backflow_serial' => 'nullable|string|max:255',
            'backflow_type' => 'nullable|string|max:255',
            'backflow_location' => 'nullable|string|max:255',
            'backflow_testing' => 'boolean',
            'backflow_test_pass' => 'boolean',
            'backflow_test_date' => 'nullable|date',
            'pvb_ai' => 'nullable|string|max:255',
            'pvb_ai_opened' => 'boolean',
            'pvb_cv' => 'nullable|string|max:255',
            'pvb_cv_held' => 'boolean',
            'rp_cv1' => 'nullable|string|max:255',
            'rp_cv1_held' => 'boolean',
            'rp_cv2' => 'nullable|string|max:255',
            'rp_cv2_held' => 'boolean',
            'rp_rv' => 'nullable|string|max:255',
            'rp_rv_opened' => 'boolean',
            'irrig_notes' => 'nullable|string',
            'required_by' => 'nullable|date',
            'required_reason' => 'nullable|string|max:255',
            'paid' => 'boolean',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string|max:255',
            'submitted' => 'nullable|string|max:255',
            'billed' => 'nullable|string|max:255',
            'prepayment_waived' => 'boolean',
            'clear_list' => 'boolean',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['turn_on_date'])) {
            $validated['turn_on_date'] = Carbon::parse($validated['turn_on_date'], $userTimezone)->utc();
        }
        if (! empty($validated['blowout_date'])) {
            $validated['blowout_date'] = Carbon::parse($validated['blowout_date'], $userTimezone)->utc();
        }
        if (! empty($validated['backflow_test_date'])) {
            $validated['backflow_test_date'] = Carbon::parse($validated['backflow_test_date'], $userTimezone)->utc();
        }
        if (! empty($validated['required_by'])) {
            $validated['required_by'] = Carbon::parse($validated['required_by'], $userTimezone)->utc();
        }

        $irrigation = Irrigation::create($validated);

        return back()->with([
            'success' => 'Irrigation record created successfully.',
            'irrigation' => $irrigation,
        ]);
    }

    /**
     * Update the specified irrigation record.
     */
    public function update(Request $request, Irrigation $irrigation): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'site_address' => 'nullable|string|max:255',
            'subdivision' => 'nullable|string|max:255',
            'site_id' => 'nullable|string|max:255',
            'sequence_order' => 'nullable|integer',
            'controller_location' => 'nullable|string|max:255',
            'turn_on' => 'boolean',
            'turn_on_date' => 'nullable|date',
            'blowout' => 'boolean',
            'blowout_date' => 'nullable|date',
            'backflow_brand' => 'nullable|string|max:255',
            'backflow_model' => 'nullable|string|max:255',
            'backflow_serial' => 'nullable|string|max:255',
            'backflow_type' => 'nullable|string|max:255',
            'backflow_location' => 'nullable|string|max:255',
            'backflow_testing' => 'boolean',
            'backflow_test_pass' => 'boolean',
            'backflow_test_date' => 'nullable|date',
            'pvb_ai' => 'nullable|string|max:255',
            'pvb_ai_opened' => 'boolean',
            'pvb_cv' => 'nullable|string|max:255',
            'pvb_cv_held' => 'boolean',
            'rp_cv1' => 'nullable|string|max:255',
            'rp_cv1_held' => 'boolean',
            'rp_cv2' => 'nullable|string|max:255',
            'rp_cv2_held' => 'boolean',
            'rp_rv' => 'nullable|string|max:255',
            'rp_rv_opened' => 'boolean',
            'irrig_notes' => 'nullable|string',
            'required_by' => 'nullable|date',
            'required_reason' => 'nullable|string|max:255',
            'paid' => 'boolean',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string|max:255',
            'submitted' => 'nullable|string|max:255',
            'billed' => 'nullable|string|max:255',
            'prepayment_waived' => 'boolean',
            'clear_list' => 'boolean',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['turn_on_date'])) {
            $validated['turn_on_date'] = Carbon::parse($validated['turn_on_date'], $userTimezone)->utc();
        }
        if (! empty($validated['blowout_date'])) {
            $validated['blowout_date'] = Carbon::parse($validated['blowout_date'], $userTimezone)->utc();
        }
        if (! empty($validated['backflow_test_date'])) {
            $validated['backflow_test_date'] = Carbon::parse($validated['backflow_test_date'], $userTimezone)->utc();
        }
        if (! empty($validated['required_by'])) {
            $validated['required_by'] = Carbon::parse($validated['required_by'], $userTimezone)->utc();
        }

        $irrigation->update($validated);

        return back()->with([
            'success' => 'Irrigation record updated successfully.',
            'irrigation' => $irrigation,
        ]);
    }

    /**
     * Remove the specified irrigation record.
     */
    public function destroy(Irrigation $irrigation): RedirectResponse
    {
        $irrigation->delete();

        return back()->with('success', 'Irrigation record deleted successfully.');
    }
}
