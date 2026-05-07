<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers;

use App\Models\Irrigation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IrrigationController extends Controller
{
    /**
     * Display a listing of irrigation records.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $resultFilter = $request->input('result');
        $submittedFilter = $request->input('submitted');
        $billedFilter = $request->input('billed');
        $completeFilter = $request->input('complete');
        $turnOnFilter = $request->input('turn_on');
        $backflowTestingFilter = $request->input('backflow_testing');
        $blowoutFilter = $request->input('blowout');

        $irrigations = Irrigation::query()
            ->with('customer:id,full_name')
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('customer', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(full_name) like ?', ["%$searchTerm%"]);
                    })->orWhereRaw('LOWER(site_address) like ?', ["%$searchTerm%"]);
                });
            })
            ->when($resultFilter !== null, function ($query) use ($resultFilter) {
                if ($resultFilter === 'pass') {
                    $query->where('backflow_test_pass', 'Pass');
                } elseif ($resultFilter === 'fail') {
                    $query->where('backflow_test_pass', 'Fail');
                } elseif ($resultFilter === 'null') {
                    $query->whereNull('backflow_test_pass');
                }
            })
            ->when($submittedFilter !== null, function ($query) use ($submittedFilter) {
                if ($submittedFilter === 'null') {
                    $query->whereNull('submitted');
                } else {
                    $query->where('submitted', $submittedFilter);
                }
            })
            ->when($billedFilter !== null, function ($query) use ($billedFilter) {
                if ($billedFilter === 'null') {
                    $query->whereNull('billed');
                } else {
                    $query->where('billed', $billedFilter);
                }
            })
            ->when($completeFilter !== null, function ($query) use ($completeFilter) {
                if ($completeFilter === 'complete') {
                    $query->where('clear_list', true);
                } elseif ($completeFilter === 'incomplete') {
                    $query->where('clear_list', false);
                }
            })
            ->when(
                $turnOnFilter !== null || $backflowTestingFilter !== null || $blowoutFilter !== null,
                function ($query) use ($turnOnFilter, $backflowTestingFilter, $blowoutFilter) {
                    $query->where(function ($q) use ($turnOnFilter, $backflowTestingFilter, $blowoutFilter) {
                        $hasCondition = false;

                        if ($turnOnFilter === 'yes') {
                            $q->orWhere('turn_on', true);
                            $hasCondition = true;
                        } elseif ($turnOnFilter === 'no') {
                            $q->orWhere('turn_on', false);
                            $hasCondition = true;
                        }

                        if ($backflowTestingFilter === 'yes') {
                            $q->orWhere('backflow_testing', true);
                            $hasCondition = true;
                        } elseif ($backflowTestingFilter === 'no') {
                            $q->orWhere('backflow_testing', false);
                            $hasCondition = true;
                        }

                        if ($blowoutFilter === 'yes') {
                            $q->orWhere('blowout', true);
                            $hasCondition = true;
                        } elseif ($blowoutFilter === 'no') {
                            $q->orWhere('blowout', false);
                            $hasCondition = true;
                        }
                    });
                }
            )
            ->orderBy('turn_on_date', 'asc')
            ->get();

        return Inertia::render('irrigation-completions/Index', [
            'irrigations' => $irrigations,
            'search' => $search,
            'filters' => [
                'result' => $resultFilter,
                'submitted' => $submittedFilter,
                'billed' => $billedFilter,
                'complete' => $completeFilter,
                'turn_on' => $turnOnFilter,
                'backflow_testing' => $backflowTestingFilter,
                'blowout' => $blowoutFilter,
            ],
        ]);
    }

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
            'backflow_test_pass' => 'nullable|in:Pass,Fail',
            'backflow_test_date' => 'nullable|date',
            'pvb_ai' => 'nullable|string|max:255',
            'pvb_ai_opened' => 'nullable|in:Opened,Did Not Open',
            'pvb_cv' => 'nullable|string|max:255',
            'pvb_cv_held' => 'nullable|in:Held,Not Held',
            'rp_cv1' => 'nullable|string|max:255',
            'rp_cv1_held' => 'nullable|in:Held,Not Held',
            'rp_cv2' => 'nullable|string|max:255',
            'rp_cv2_held' => 'nullable|in:Held,Not Held',
            'rp_rv' => 'nullable|string|max:255',
            'rp_rv_opened' => 'nullable|in:Opened,Did Not Open',
            'irrig_notes' => 'nullable|string',
            'required_by' => 'nullable|date',
            'required_reason' => 'nullable|string|max:255',
            'submitted' => 'nullable|string|max:255',
            'billed' => 'nullable|string|max:255',
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
            'backflow_test_pass' => 'nullable|in:Pass,Fail',
            'backflow_test_date' => 'nullable|date',
            'pvb_ai' => 'nullable|string|max:255',
            'pvb_ai_opened' => 'nullable|in:Opened,Did Not Open',
            'pvb_cv' => 'nullable|string|max:255',
            'pvb_cv_held' => 'nullable|in:Held,Not Held',
            'rp_cv1' => 'nullable|string|max:255',
            'rp_cv1_held' => 'nullable|in:Held,Not Held',
            'rp_cv2' => 'nullable|string|max:255',
            'rp_cv2_held' => 'nullable|in:Held,Not Held',
            'rp_rv' => 'nullable|string|max:255',
            'rp_rv_opened' => 'nullable|in:Opened,Did Not Open',
            'irrig_notes' => 'nullable|string',
            'required_by' => 'nullable|date',
            'required_reason' => 'nullable|string|max:255',
            'submitted' => 'nullable|string|max:255',
            'billed' => 'nullable|string|max:255',
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
     * Update specific field (for inline editing).
     */
    public function updateField(Request $request, Irrigation $irrigation): RedirectResponse
    {
        $validated = $request->validate([
            'field' => 'required|in:submitted,billed,clear_list',
            'value' => 'nullable',
        ]);

        // Handle boolean conversion for clear_list
        $value = $validated['value'];
        if ($validated['field'] === 'clear_list') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $irrigation->update([
            $validated['field'] => $value,
        ]);

        return back();
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
