<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->withCount('orders')
            ->when($request->filled('search'), function ($query) use ($request) {

                $search = $request->string('search')->trim();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('role') &&
                in_array($request->role, ['admin', 'customer'], true),
                function ($query) use ($request) {
                    $query->where('role', $request->role);
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount('orders');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'customer']),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Prevent removing admin role from your own account.
        if ($user->is(Auth::user()) && $validated['role'] !== 'admin') {
            return back()->withInput()->withErrors(['role' => 'You cannot remove the administrator role from your own account.',]);
        }

        // Always keep at least one administrator.
        if (
            $user->role === 'admin' &&
            $validated['role'] === 'customer' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'The system must have at least one administrator',
                ]);
        }

        // Do not overwrite the current password with an empty value.
        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself.
        if ($user->is(Auth::user())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account');
        }

        // Prevent deleting the last administrator.
        if (
            $user->role === 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'The last administrator cannot be deleted');
        }

        // Preserve users that already have order history.
        if ($user->orders()->exists()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'This user cannot be deleted because they have existing orders');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}