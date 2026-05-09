<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Auth\Models\User;

class CustomerController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.customers.view')->only(['index', 'show']);
        $this->middleware('permission:admin.customers.edit')->only(['edit', 'update']);
        $this->middleware('permission:admin.customers.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $customers = User::query()
            ->isCustomer()
            ->with(['orders' => function($query) {
                $query->select('id', 'user_id', 'grand_total', 'status', 'created_at');
            }])
            ->withCount(['orders'])
            ->latest()
            ->paginate(15);

        return view('auth::admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->load([
            'orders' => function($query) {
                $query->latest()->with(['items.product']);
            }
        ]);

        return view('auth::admin.customers.show', compact('customer'));
    }

    public function edit(User $customer): View
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        return view('auth::admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.index')
            ->with('status', __('Customer updated successfully.'));
    }

    public function destroy(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('status', __('Customer deleted successfully.'));
    }
}
