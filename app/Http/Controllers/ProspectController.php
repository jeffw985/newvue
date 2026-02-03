<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProspectController extends Controller
{
    /**
     * Display a listing of the prospects.
     */
    public function index(Request $request): Response
    {
        $prospects = Prospect::query()
            ->orderBy('date', 'desc')
            ->paginate(100);

        return Inertia::render('prospects/Index', [
            'prospects' => $prospects,
        ]);
    }

    /**
     * Store a newly created prospect.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'work_description' => 'nullable|string',
            'confirmation' => 'nullable|date',
        ]);

        Prospect::create($validated);

        return back()->with('success', 'Prospect created successfully.');
    }

    /**
     * Update the specified prospect.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $prospect = Prospect::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'work_description' => 'nullable|string',
            'confirmation' => 'nullable|date',
        ]);

        $prospect->update($validated);

        return back()->with('success', 'Prospect updated successfully.');
    }

    /**
     * Remove the specified prospect.
     */
    public function destroy(int $id): RedirectResponse
    {
        $prospect = Prospect::findOrFail($id);
        $prospect->delete();

        return back()->with('success', 'Prospect deleted successfully.');
    }
}
