<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(20);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
        ]);

        $validated['is_active'] = true;

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            // FIX: Changed back to 'phone' to match database column
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'is_active' => 'boolean',
        ]);

        // FIX: Handle checkbox - if unchecked, set to false
        $validated['is_active'] = $request->has('is_active');

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function trashed()
    {
        $suppliers = Supplier::onlyTrashed()->latest()->paginate(20);
        return view('admin.suppliers.trashed', compact('suppliers'));
    }

    public function restore($supplier)
    {
        Supplier::withTrashed()->findOrFail($supplier)->restore();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier restored successfully.');
    }

    public function forceDelete($supplier)
    {
        Supplier::withTrashed()->findOrFail($supplier)->forceDelete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier permanently deleted.');
    }
}