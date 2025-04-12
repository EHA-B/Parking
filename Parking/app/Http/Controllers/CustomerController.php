<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List all customers
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    // Show form to create a new customer
    public function create()
    {
        return view('customers.create');
    }

    // Store a new customer
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'hours' => 'nullable|integer'
        ]);

        $customer = Customer::create($validatedData);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Customer created successfully.');
    }

    // Show a specific customer with their VICs
    public function show(Customer $customer)
    {
        // Eager load the VICs to avoid N+1 query problem
        $customer->load('vics');
        return view('customers.show', compact('customer'));
    }

    // Show form to edit a customer
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Update a customer
    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'hours' => 'nullable|integer'
        ]);

        $customer->update($validatedData);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Customer updated successfully.');
    }

    // Delete a customer
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}