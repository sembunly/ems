<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles and permissions.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        // Get counts dynamically. Fallback to 0 if tables aren't ready
        $userCounts = [];
        foreach (['admin', 'seller', 'customer', 'visitor'] as $roleName) {
            try {
                $userCounts[$roleName] = User::role($roleName)->count();
            } catch (\Throwable $e) {
                // Fallback count based on the legacy role column if roles table isn't populated yet
                $userCounts[$roleName] = User::where('role', $roleName)->count();
            }
        }

        return view('admin.roles.index', compact('roles', 'permissions', 'userCounts'));
    }
}
