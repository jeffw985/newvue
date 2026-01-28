<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->withCount(['ledgers', 'irrigations', 'maintenances', 'serviceSchedules'])
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(full_name) like ?', ["%$searchTerm%"])
                        ->orWhereRaw('LOWER(street) like ?', ["%$searchTerm%"])
                        ->orWhereRaw('LOWER(city) like ?', ["%$searchTerm%"])
                        ->orWhereRaw('LOWER(email) like ?', ["%$searchTerm%"])
                        ->orWhereRaw('LOWER(phone) like ?', ["%$searchTerm%"]);
                });
            })
            ->orderBy('full_name')
            ->paginate(15);

        return Inertia::render('customers/Index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): Response
    {
        $customer->load([
            'areaGroup',
            'ledgers' => fn ($query) => $query->with('sheet')->orderBy('work_date', 'desc')->limit(50),
            'irrigations' => fn ($query) => $query->latest()->limit(50),
            'maintenances' => fn ($query) => $query->latest()->limit(50),
            'serviceSchedules' => fn ($query) => $query->orderBy('start_time', 'desc')->limit(50),
        ]);

        // Load counts separately to show accurate badge counts
        $customer->loadCount(['ledgers', 'irrigations', 'maintenances', 'serviceSchedules']);

        return Inertia::render('customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'first' => 'nullable|string|max:255',
            'last' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
            'spouse_first' => 'nullable|string|max:255',
            'spouse_last' => 'nullable|string|max:255',
            'irrigation' => 'boolean',
            'maintenance' => 'boolean',
            'notes' => 'nullable|string',
            'area_group_id' => 'nullable|exists:area_groups,id',
            'xero_contact_id' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);
        $customer->load(['areaGroup']);

        return back()->with([
            'success' => 'Customer updated successfully.',
            'customer' => $customer,
        ]);
    }
}
