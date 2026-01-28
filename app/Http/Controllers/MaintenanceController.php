<?php

/** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Store a newly created maintenance record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'annual_pay' => 'boolean',
            'mulch_color' => 'nullable|string|max:255',
            'service_interval' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'subdivision' => 'nullable|string|max:255',
            'sequence_order' => 'nullable|integer',
        ]);

        $maintenance = Maintenance::create($validated);

        return back()->with([
            'success' => 'Maintenance record created successfully.',
            'maintenance' => $maintenance,
        ]);
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'annual_pay' => 'boolean',
            'mulch_color' => 'nullable|string|max:255',
            'service_interval' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'subdivision' => 'nullable|string|max:255',
            'sequence_order' => 'nullable|integer',
        ]);

        $maintenance->update($validated);

        return back()->with([
            'success' => 'Maintenance record updated successfully.',
            'maintenance' => $maintenance,
        ]);
    }
}
