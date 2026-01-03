<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display all users
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest();

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->approved();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display pending approval users
     */
    public function pending(): View
    {
        $users = User::pending()
            ->with('roles')
            ->latest()
            ->paginate(15);

        return view('admin.users.pending', compact('users'));
    }

    /**
     * Approve a user
     */
    public function approve(User $user): RedirectResponse
    {
        if ($user->is_approved) {
            return back()->with('error', 'User is already approved.');
        }

        $user->approve(auth()->id());

        // Send approval notification SMS
        $this->smsService->sendApprovalNotification($user->phone, $user->name);

        return back()->with('success', 'User approved successfully!');
    }

    /**
     * Reject a user
     */
    public function reject(User $user): RedirectResponse
    {
        if ($user->is_approved) {
            return back()->with('error', 'Cannot reject an approved user.');
        }

        $user->delete();

        return back()->with('success', 'User rejected and removed.');
    }

    /**
     * Show edit form
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'],
        ]);

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }
}
