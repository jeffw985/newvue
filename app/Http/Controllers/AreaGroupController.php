<?php

namespace App\Http\Controllers;

use App\Models\AreaGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AreaGroupController extends Controller
{
    /**
     * Display a listing of area groups.
     */
    public function index(): Response
    {
        $areaGroups = AreaGroup::query()
            ->orderBy('area_name')
            ->get();

        return Inertia::render('area-groups/Index', [
            'areaGroups' => $areaGroups,
        ]);
    }

    /**
     * Store a newly created area group.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area_name' => 'required|string|max:255',
        ]);

        $areaGroup = AreaGroup::create($validated);

        return redirect()->back()->with([
            'flash' => [
                'areaGroup' => $areaGroup,
            ],
        ]);
    }

    /**
     * Update the specified area group.
     */
    public function update(Request $request, AreaGroup $areaGroup): RedirectResponse
    {
        $validated = $request->validate([
            'area_name' => 'required|string|max:255',
        ]);

        $areaGroup->update($validated);

        return redirect()->back()->with([
            'flash' => [
                'areaGroup' => $areaGroup,
            ],
        ]);
    }

    /**
     * Remove the specified area group.
     */
    public function destroy(AreaGroup $areaGroup): RedirectResponse
    {
        $areaGroup->delete();

        return redirect()->back();
    }
}
