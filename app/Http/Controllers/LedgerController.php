<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ledger;
use App\Models\Sheet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LedgerController extends Controller
{
    /**
     * Display a listing of ledgers grouped by customer.
     */
    public function index(Request $request): Response
    {
        $filter = $request->input('filter', 'all'); // all, billed, unbilled
        $search = $request->input('search');
        $workType = $request->input('work_type', 'all');

        $query = Ledger::query()
            ->with(['customer', 'sheet'])
            ->when($filter === 'billed', fn ($q) => $q->where('billed', true))
            ->when($filter === 'unbilled', fn ($q) => $q->where('billed', false))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('customer', function ($q) use ($search) {
                    $q->where('full_name', 'ilike', "%$search%")
                        ->orWhere('street', 'ilike', "%$search%");
                });
            })
            ->when($workType !== 'all', fn ($q) => $q->where('work_type', $workType));

        $ledgers = $query
            ->orderBy('cust_id')
            ->orderBy('work_date', 'desc')
            ->paginate(500);

        // Get all customers for the create/edit form
        $customers = Customer::orderBy('full_name')->get(['id', 'full_name']);

        // Get all sheets for the dropdown
        $sheets = Sheet::orderBy('begin_date', 'desc')
            ->get(['id', 'begin_date', 'end_date']);

        return Inertia::render('ledgers/Index', [
            'ledgers' => $ledgers,
            'customers' => $customers,
            'sheets' => $sheets,
            'filter' => $filter,
            'search' => $search,
            'work_type' => $workType,
        ]);
    }

    /**
     * Store a newly created ledger.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'work_date' => 'required|date',
            'sheet_number' => 'nullable|string|max:255',
            'times' => 'nullable|string|max:255',
            'hours' => 'nullable|numeric|min:0',
            'work_type' => 'required|string|in:Site Work,Irrigation,Maintenance',
            'work_performed' => 'nullable|string',
            'job_notes' => 'nullable|string',
            'job_code' => 'nullable|string|max:255',
            'sheet_link' => 'nullable|exists:sheets,id',
            'billed' => 'boolean',
        ]);

        // Convert date input from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['work_date'])) {
            $validated['work_date'] = Carbon::parse($validated['work_date'], $userTimezone)->utc();
        }

        Ledger::create($validated);

        return back()->with('success', 'Ledger created successfully.');
    }

    /**
     * Update the specified ledger.
     */
    public function update(Request $request, Ledger $ledger): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'work_date' => 'required|date',
            'sheet_number' => 'nullable|string|max:255',
            'times' => 'nullable|string|max:255',
            'hours' => 'nullable|numeric|min:0',
            'work_type' => 'required|string|in:Site Work,Irrigation,Maintenance',
            'work_performed' => 'nullable|string',
            'job_notes' => 'nullable|string',
            'job_code' => 'nullable|string|max:255',
            'sheet_link' => 'nullable|exists:sheets,id',
            'billed' => 'boolean',
        ]);

        // Convert date input from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['work_date'])) {
            $validated['work_date'] = Carbon::parse($validated['work_date'], $userTimezone)->utc();
        }

        $ledger->update($validated);
        $ledger->load(['customer', 'sheet']);

        return back()->with([
            'success' => 'Ledger updated successfully.',
            'ledger' => $ledger,
        ]);
    }

    /**
     * Remove the specified ledger.
     */
    public function destroy(Ledger $ledger): RedirectResponse
    {
        $ledger->delete();

        return back()->with('success', 'Ledger deleted successfully.');
    }
}
