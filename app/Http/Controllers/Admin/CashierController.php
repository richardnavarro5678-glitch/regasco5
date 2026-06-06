<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = User::where('role', 'cashier')
            ->withCount('sales')
            ->latest()
            ->get();
        return view('admin.cashiers.index', compact('cashiers'));
    }

    public function create()
    {
        return view('admin.cashiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $validated['role'] = 'cashier';
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('admin.cashiers.index')
            ->with('success', 'Cashier account created successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role !== 'cashier') {
            abort(403);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Cashier account {$status} successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->role !== 'cashier') {
            abort(403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => $validated['password'],
            'password_changed_at' => null,
        ]);

        return back()->with('success', 'Password reset successfully.');
    }
}