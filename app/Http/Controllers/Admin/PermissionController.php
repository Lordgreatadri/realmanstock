<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display all permissions
     */
    public function index(): View
    {
        $permissions = Permission::withCount('roles')->get()->groupBy(function($permission) {
            // Group permissions by prefix (e.g., 'view-users', 'create-users' grouped under 'users')
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? $parts[1] : 'general';
        });

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store new permission
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return back()->with('success', 'Permission created successfully!');
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return back()->with('success', 'Permission deleted successfully!');
    }
}
