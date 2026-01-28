<?php

/** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\Sheet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SheetController extends Controller
{
    /**
     * Display a listing of sheets ordered by begin_date descending.
     */
    public function index(): Response
    {
        $sheets = Sheet::query()
            ->withCount('ledgers')
            ->orderBy('begin_date', 'desc')
            ->paginate(50);

        return Inertia::render('sheets/Index', [
            'sheets' => $sheets,
        ]);
    }

    /**
     * Store a newly created sheet.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'begin_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:begin_date',
            'sheet_link' => 'nullable|string|max:255',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['begin_date'])) {
            $validated['begin_date'] = Carbon::parse($validated['begin_date'], $userTimezone)->utc();
        }
        if (! empty($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date'], $userTimezone)->utc();
        }

        Sheet::create($validated);

        return back()->with('success', 'Sheet created successfully.');
    }

    /**
     * Update the specified sheet.
     */
    public function update(Request $request, Sheet $sheet)
    {
        $validated = $request->validate([
            'begin_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:begin_date',
            'sheet_link' => 'nullable|string|max:255',
        ]);

        // Convert date inputs from user's timezone to UTC
        $userTimezone = $request->header('X-Timezone', config('app.timezone'));
        if (! empty($validated['begin_date'])) {
            $validated['begin_date'] = Carbon::parse($validated['begin_date'], $userTimezone)->utc();
        }
        if (! empty($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date'], $userTimezone)->utc();
        }

        $sheet->update($validated);

        return back()->with('success', 'Sheet updated successfully.');
    }

    /**
     * Remove the specified sheet.
     */
    public function destroy(Sheet $sheet)
    {
        $sheet->delete();

        return back()->with('success', 'Sheet deleted successfully.');
    }
}
